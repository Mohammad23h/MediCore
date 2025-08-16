<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabTest extends Model
{
    use HasFactory;


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
