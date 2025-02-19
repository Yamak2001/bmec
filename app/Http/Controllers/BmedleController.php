<?php

namespace App\Http\Controllers;

use App\Models\BmecWorldeTerm;
use App\Models\BmecdleAttempt;
use App\Models\DictionaryWord;
use App\Http\Requests\StoreBmedleRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class BmedleController extends Controller
{
    /**
     * Display the list of attempts or the game page (depending on your design).
     * e.g. Could show all attempts by the current user, or a leaderboard, etc.
     */
    public function index()
    {
        // For example: show a list of recent attempts by the user
        $attempts = BmecdleAttempt::where('attempted_by', Auth::id())
                        ->orderByDesc('created_at')
                        ->get();

        return inertia('Bmedle/Index', [
            'attempts' => $attempts
        ]);
    }

    /**
     * Possibly direct user to the 'create' view where they start the game.
     */
    public function create()
    {
        // Example: pick today's term (or a random one) from bmec_worlde_terms
        $today = now()->format('Y-m-d');
        
        // Find or create the "term of the day" logic
        // For simplicity, let’s say you store that in 'bmedle_day' = $today
        $termOfTheDay = BmecWorldeTerm::inRandomOrder()->first();
        
        // Alternatively: $termOfTheDay = BmecWorldeTerm::whereDate('some_date_column', $today)->first();
        // or any logic to choose the puzzle of the day

        return inertia('Bmedle/Create', [
            'termOfTheDay' => $termOfTheDay,
            'bmedleDay' => $today
        ]);
    }

    /**
     * Store / start an attempt. This is where you create the BmedleAttempt row.
     */
    public function store(StoreBmedleRequest  $request)
    {
        // Validate input if needed (e.g., user must not have already started today's puzzle)
        $data = $request->validate([
            'bmec_term_id' => 'required|integer',
            'bmec_term'    => 'required|string',
            'bmedle_day'   => 'required|string',
        ]);

        $attempt = BmecdleAttempt::create([
            'bmec_term_id'      => $data['bmec_term_id'],
            'bmec_term'         => $data['bmec_term'],
            'bmedle_day'        => $data['bmedle_day'],
            'attempted_by'      => Auth::id(),
            'started_at'        => Carbon::now(),
            // The rest defaults: attempts_used=0, hint_used=false, etc.
        ]);

        // Now redirect the user to an Inertia page that displays the puzzle UI
        // passing along the $attempt->id so they can continue.
        return redirect()->route('bmedle.show', $attempt->id);
    }

    /**
     * Show the puzzle for the user’s attempt. 
     */
    public function show(BmecdleAttempt $bmedleAttempt)
    {
        // Ensure the current user is the owner, or has permission
        if ($bmedleAttempt->attempted_by !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Return puzzle UI with data about attempts, time, etc.
        return inertia('Bmedle/Show', [
            'attempt' => $bmedleAttempt,
        ]);
    }

    /**
     * This method could handle user guesses. 
     * e.g. user enters a guess -> we validate -> update the BmedleAttempt
     */
    public function update(StoreBmedleRequest  $request, BmecdleAttempt $bmedleAttempt)
    {
        // Example validation: guess must be 5 letters, must exist in dictionary_words
        $data = $request->validate([
            'guess' => 'required|string|size:5'
        ]);

        // Ensure the guess is a valid word
        $valid = DictionaryWord::where('word', strtoupper($data['guess']))->exists();
        if (!$valid) {
            return back()->withErrors(['guess' => 'Not a valid word']);
        }

        // Add the guess to the next empty attempt field
        // e.g. attempt_one, attempt_two, etc.
        // Increase attempts_used by 1
        // If guess == $bmedleAttempt->bmec_term, mark attempt_status
        // If attempts_used == 5 and not solved, mark as failed
        // Possibly store completed_at if solved or if user fails

        // For example:
        $nextAttemptNumber = $bmedleAttempt->attempts_used + 1;
        $fieldName = 'attempt_' . $nextAttemptNumber;
        $bmedleAttempt->$fieldName = strtoupper($data['guess']);
        $bmedleAttempt->attempts_used = $nextAttemptNumber;

        // Check if correct guess
        if (strtoupper($data['guess']) === strtoupper($bmedleAttempt->bmec_term)) {
            $bmedleAttempt->attempt_status = 'solved_on_' . $nextAttemptNumber;
            $bmedleAttempt->completed_at = Carbon::now();
            // Score calculation logic goes here
        } elseif ($nextAttemptNumber === 5) {
            // If it’s the last attempt, and still not solved -> mark as failed
            $bmedleAttempt->attempt_status = 'failed';
            $bmedleAttempt->completed_at = Carbon::now();
        }

        // Recalculate duration_of_attempt if puzzle is solved or ended
        if ($bmedleAttempt->attempt_status === 'failed' || strpos($bmedleAttempt->attempt_status, 'solved_on_') !== false) {
            $startTime = $bmedleAttempt->started_at;
            $endTime = $bmedleAttempt->completed_at ?: now(); // fallback
            $bmedleAttempt->duration_of_attempt = $endTime->diffInSeconds($startTime);

            // Example scoring logic
            $score = 1000; // baseline
            $score -= ($bmedleAttempt->attempts_used - 1) * 50; // penalty per attempt beyond first
            if ($bmedleAttempt->hint_used) {
                $score -= 100;
            }
            $bmedleAttempt->attempt_score = max($score, 0);
        }

        $bmedleAttempt->save();

        // Return them to the puzzle UI
        return redirect()->route('bmedle.show', $bmedleAttempt->id);
    }

    /**
     * If you wanted an explicit route to track hint usage, you could do something like:
     */
    public function useHint(BmecdleAttempt $bmedleAttempt)
    {
        // Mark hint_used = true
        $bmedleAttempt->hint_used = true;
        $bmedleAttempt->save();

        return back(); // or redirect as needed
    }

    /**
     * Potentially to let user 'delete' an attempt record if needed.
     */
    public function destroy(BmecdleAttempt $bmedleAttempt)
    {
        // Check if authorized
        if ($bmedleAttempt->attempted_by !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
        $bmedleAttempt->delete();
        return redirect()->route('bmedle.index');
    }
}
