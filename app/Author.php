<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    protected $table = 'authors';

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'description',
        'birth_date'
    ];
}
