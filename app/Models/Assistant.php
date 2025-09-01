<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assistant extends Model
{
    use HasFactory;


    protected $fillable = [
        'name',
        'email',
        'phone',
        'user_id',
        'center_id',
    ];


    // protected $hidden = [
    //     'user_id'
    // ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function center()
    {
        return $this->belongsTo(Center::class);
    }

    public function labTests()
    {
        return $this->hasMany(LabTest::class);
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }
}
