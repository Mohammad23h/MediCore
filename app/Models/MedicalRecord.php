<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    use HasFactory;



    protected $fillable = [
        'patient_id',
        'assistant_id',
        'diagnosis',
        'notes',
        'date',


        'previous_diseases',
        'surgeries',
        'allergies',
    ];


    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function assistant()
    {
        return $this->belongsTo(Assistant::class);
    }
}
