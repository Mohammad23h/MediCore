<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabTechnician extends Model
{
    use HasFactory;


    public function laboratory()
    {
        return $this->belongsTo(Laboratory::class, 'lab_id');
    }
}
