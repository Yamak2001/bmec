<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BmecWorldeTerm extends Model
{
    use HasFactory;

    protected $table = 'bmec_worlde_terms';

    protected $fillable = [
        'term',
        'hints',
        'is_used',
        'usage_date',
        'viewed_count',
    ];
}
