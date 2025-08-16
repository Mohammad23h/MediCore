<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientIllness extends Model
{
    use HasFactory;


    protected $table = 'patient_illness';

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function illness()
    {
        return $this->belongsTo(Illness::class);
    }
}
