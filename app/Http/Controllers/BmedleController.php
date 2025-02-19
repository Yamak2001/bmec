<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBmedleRequest;
use App\Models\BmecdleAttempt;
use App\Models\BmecWorldeTerm;
use App\Models\DictionaryWord;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BmedleController extends Controller
{
    /**
     * GET /api/bmedle/attempts
     * Return all attempts for the authenticated user.
     */
    public function listAttempts()
    {
        try {
            $userId = Auth::id();
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'error'   => 'Unauthenticated',
                ], 401);
            }

            $attempts = BmecdleAttempt::where('attempted_by', $userId)
                ->orderByDesc('created_at')
                ->get();

            Log::info("Listed attempts for user {$userId}.");

            return response()->json([
                'success'  => true,
                'attempts' => $attempts,
            ]);
        } catch (\Exception $e) {
            Log::error("Error listing attempts for user " . Auth::id(), [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'error'   => 'Failed to list attempts',
            ], 500);
        }
    }

    /**
     * POST /api/bmedle/start
     * Start a new attempt with a random or daily term.
     */
    public function startAttempt(StoreBmedleRequest $request)
    {
        try {
            // Log request details and authentication info
            Log::debug("startAttempt method invoked", [
                'request_input' => $request->all(),
                'Auth::id()'    => Auth::id(),
                'Auth::user()'  => Auth::user() ? Auth::user()->toArray() : null,
            ]);
    
            $userId = Auth::id();
            if (!$userId) {
                Log::warning("User is unauthenticated in startAttempt", [
                    'request_input' => $request->all()
                ]);
                return response()->json([
                    'success' => false,
                    'error'   => 'Unauthenticated',
                ], 401);
            }
    
            $termOfTheDay = BmecWorldeTerm::inRandomOrder()->first();
            if (!$termOfTheDay) {
                Log::error("No term found for starting attempt.");
                return response()->json([
                    'success' => false,
                    'error'   => 'No term available',
                ], 500);
            }
            
            Log::debug("Term of the day selected", [
                'term_id' => $termOfTheDay->id,
                'term'    => $termOfTheDay->term,
            ]);
    
            $attempt = BmecdleAttempt::create([
                'bmec_term_id' => $termOfTheDay->id,
                'bmec_term'    => $termOfTheDay->term,
                'bmedle_day'   => now()->format('Y-m-d'),
                'attempted_by' => $userId,
                'started_at'   => Carbon::now(),
            ]);
    
            Log::info("New attempt started", [
                'user'       => $userId,
                'attempt_id' => $attempt->id,
                'attempt_data' => $attempt->toArray()
            ]);
    
            return response()->json([
                'success' => true,
                'attempt' => $attempt,
            ]);
        } catch (\Exception $e) {
            $currentUserId = Auth::id();
            Log::error("Error starting attempt for user " . ($currentUserId ?? 'none'), [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_input' => $request->all(),
                'Auth::user()'  => Auth::user() ? Auth::user()->toArray() : null,
            ]);
            return response()->json([
                'success' => false,
                'error'   => 'Failed to start attempt',
            ], 500);
        }
    }
    

    /**
     * GET /api/bmedle/attempts/{bmedleAttempt}
     * Return data for a single attempt (must belong to user).
     */
    public function getAttempt(BmecdleAttempt $bmedleAttempt)
    {
        try {
            if ($bmedleAttempt->attempted_by !== Auth::id()) {
                Log::warning("Unauthorized access to attempt {$bmedleAttempt->id} by user " . Auth::id());
                return response()->json([
                    'success' => false,
                    'error'   => 'Unauthorized',
                ], 403);
            }

            return response()->json([
                'success' => true,
                'attempt' => $bmedleAttempt,
            ]);
        } catch (\Exception $e) {
            Log::error("Error fetching attempt {$bmedleAttempt->id}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'error'   => 'Failed to retrieve attempt',
            ], 500);
        }
    }

    /**
     * POST /api/bmedle/attempts/{bmedleAttempt}/guess
     * Submit a guess for the given attempt.
     */
    public function submitGuess(StoreBmedleRequest $request, BmecdleAttempt $bmedleAttempt)
    {
        try {
            if ($bmedleAttempt->attempted_by !== Auth::id()) {
                Log::warning("Unauthorized guess submission for attempt {$bmedleAttempt->id} by user " . Auth::id());
                return response()->json([
                    'success' => false,
                    'error'   => 'Unauthorized',
                ], 403);
            }

            $data = $request->validate([
                'guess' => 'required|string|size:5'
            ]);

            $guessUpper = strtoupper($data['guess']);
            $valid = DictionaryWord::where('word', $guessUpper)->exists();
            if (!$valid) {
                Log::info("Invalid word guessed: {$guessUpper}", ['user' => Auth::id(), 'attempt' => $bmedleAttempt->id]);
                return response()->json([
                    'success' => false,
                    'error'   => 'Not a valid word',
                ], 422);
            }

            $nextAttemptNumber = $bmedleAttempt->attempts_used + 1;
            if ($nextAttemptNumber > 5) {
                Log::info("Max attempts reached for attempt {$bmedleAttempt->id}", ['user' => Auth::id()]);
                return response()->json([
                    'success' => false,
                    'error'   => 'Max attempts reached',
                ], 400);
            }

            $fieldName = 'attempt_' . $nextAttemptNumber;
            $bmedleAttempt->$fieldName = $guessUpper;
            $bmedleAttempt->attempts_used = $nextAttemptNumber;

            if ($guessUpper === strtoupper($bmedleAttempt->bmec_term)) {
                $bmedleAttempt->attempt_status = 'solved_on_' . $nextAttemptNumber;
                $bmedleAttempt->completed_at   = now();
            } elseif ($nextAttemptNumber === 5) {
                $bmedleAttempt->attempt_status = 'failed';
                $bmedleAttempt->completed_at   = now();
            }

            if ($bmedleAttempt->attempt_status === 'failed' ||
                stripos($bmedleAttempt->attempt_status, 'solved_on_') !== false
            ) {
                $start = $bmedleAttempt->started_at;
                $end   = $bmedleAttempt->completed_at ?: now();
                $bmedleAttempt->duration_of_attempt = $end->diffInSeconds($start);

                $score = 1000 - ($bmedleAttempt->attempts_used - 1) * 50;
                if ($bmedleAttempt->hint_used) {
                    $score -= 100;
                }
                $bmedleAttempt->attempt_score = max($score, 0);
            }

            $bmedleAttempt->save();
            Log::info("Guess submitted for attempt {$bmedleAttempt->id}", [
                'guess'         => $guessUpper,
                'attempts_used' => $bmedleAttempt->attempts_used,
            ]);

            return response()->json([
                'success' => true,
                'attempt' => $bmedleAttempt,
            ]);
        } catch (\Exception $e) {
            Log::error("Error submitting guess for attempt {$bmedleAttempt->id}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'error'   => 'Failed to submit guess',
            ], 500);
        }
    }

    /**
     * PATCH /api/bmedle/attempts/{bmedleAttempt}/hint
     * Mark hint_used = true.
     */
    public function applyHint(BmecdleAttempt $bmedleAttempt)
    {
        try {
            if ($bmedleAttempt->attempted_by !== Auth::id()) {
                Log::warning("Unauthorized hint request for attempt {$bmedleAttempt->id}", ['user' => Auth::id()]);
                return response()->json([
                    'success' => false,
                    'error'   => 'Unauthorized',
                ], 403);
            }

            $bmedleAttempt->hint_used = true;
            $bmedleAttempt->save();
            Log::info("Hint applied for attempt {$bmedleAttempt->id}", ['user' => Auth::id()]);

            return response()->json([
                'success' => true,
                'attempt' => $bmedleAttempt,
            ]);
        } catch (\Exception $e) {
            Log::error("Error applying hint for attempt {$bmedleAttempt->id}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'error'   => 'Failed to apply hint',
            ], 500);
        }
    }

    /**
     * DELETE /api/bmedle/attempts/{bmedleAttempt}
     * Let user delete an attempt if needed.
     */
    public function deleteAttempt(BmecdleAttempt $bmedleAttempt)
    {
        try {
            if ($bmedleAttempt->attempted_by !== Auth::id()) {
                Log::warning("Unauthorized delete request for attempt {$bmedleAttempt->id}", ['user' => Auth::id()]);
                return response()->json([
                    'success' => false,
                    'error'   => 'Unauthorized',
                ], 403);
            }

            $bmedleAttempt->delete();
            Log::info("Attempt deleted: {$bmedleAttempt->id}", ['user' => Auth::id()]);

            return response()->json([
                'success' => true,
                'message' => 'Attempt deleted.',
            ]);
        } catch (\Exception $e) {
            Log::error("Error deleting attempt {$bmedleAttempt->id}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'error'   => 'Failed to delete attempt',
            ], 500);
        }
    }
}
