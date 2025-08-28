<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;
    protected $fillable = [
    'name',
    'user_id',
    'image_url',
    'phone' ,
    'gender',
    'date_of_birth' ,
    'blood_type',
    'registered_at'
    ];



    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function labTests()
    {
        return $this->hasMany(LabTest::class);
    }

    public function chat()
    {
        return $this->hasMany(Chat::class);
    }

    public function feedbacks()
    {
        return $this->hasMany(Feedback::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function illnesses()
    {
        return $this->belongsToMany(Illness::class, 'patient_illness')
                    ->withPivot(['injured_at', 'notes'])
                    ->withTimestamps();
    }

    public function requests()
    {
        return $this->hasMany(Request::class);
    }
}
