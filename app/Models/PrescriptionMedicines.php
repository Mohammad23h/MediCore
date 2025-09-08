<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrescriptionMedicines extends Model
{
    use HasFactory;

    protected $fillable = [
      'prescription_id',
      'medicine_name',
      'dosage',
      'frequency',
      'notes'
    ];

    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }
}
