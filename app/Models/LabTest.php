<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabTest extends Model
{
    use HasFactory;

      protected $fillable = [
        'patient_id',
        'assistant_id',
        'test_type',
        'pdf_file_uri',
        'test_date',
        'lab_id',
        'result',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function assistant()
    {
        return $this->belongsTo(Assistant::class);
    }

    public function laboratory()
    {
        return $this->belongsTo(Laboratory::class, 'lab_id');
    }
}
