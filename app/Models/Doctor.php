<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;


    protected $fillable = [
    'name',
    'user_id',
    'image_url',
    'start_day',
    'end_day',
    'start_time',
    'end_time',
    'clinic_id',
    'specialty',
    'services'
    ];

    
    protected $casts = [
        'services' => 'array',
    ];

    // accessor لإرجاع [] بدل null
    public function getServicesAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }


    protected $hidden = [
        'user_id'
    ];



    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
