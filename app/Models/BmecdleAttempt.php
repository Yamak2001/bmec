<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BmecdleAttempt extends Model
{
    use HasFactory;

    protected $table = 'bmecdle_attempts';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'bmec_term_id',
        'bmec_term',
        'bmedle_day',
        'attempted_by',
        'started_at',
        'completed_at',
        'attempt_one',
        'attempt_two',
        'attempt_three',
        'attempt_four',
        'attempt_five',
        'attempts_used',
        'did_not_finish',
        'hint_used',
        'duration_of_attempt',
        'attempt_status',
        'attempt_score',
    ];
}
