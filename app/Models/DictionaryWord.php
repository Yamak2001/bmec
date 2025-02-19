<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DictionaryWord extends Model
{
    use HasFactory;

    protected $table = 'dictionary_words'; // If your table name is not the plural of the model name

    protected $fillable = [
        'word',
    ];
}
