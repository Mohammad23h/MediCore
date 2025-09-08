<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Health_Habit extends Model
{
    use HasFactory;

    protected $table = 'health__habits';
    
    protected $fillable = [
      'patient_id',
      'smoking',
      'alcohol',
      'diet',
      'exercise',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

}
