<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpecialistForm extends Model
{
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'country',
        'major',
        'interest',
        'research_papers',
        'resume_path',
    ];
}
