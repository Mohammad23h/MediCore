<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laboratory extends Model
{
    use HasFactory;





    public function center()
    {
        return $this->belongsTo(Center::class);
    }

    public function labTests()
    {
        return $this->hasMany(LabTest::class, 'lab_id');
    }

    public function technicians()
    {
        return $this->hasMany(LabTechnician::class, 'lab_id');
    }
}
