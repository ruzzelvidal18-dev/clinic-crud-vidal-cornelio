<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_number',
        'first_name',
        'last_name',
        'birth_date',
        'sex',
        'contact_number',
        'address',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];
}