<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Center extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image_url',
        'start_day',
        'user_id',
        'end_day',
        'start_time',
        'end_time',
        'clinic_id',
        'location'
    ];


    protected $hidden = [
        'user_id'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function clinics()
    {
        return $this->hasMany(Clinic::class);
    }

    public function laboratory()
    {
        return $this->hasOne(Laboratory::class);
    }

    public function assistants()
    {
        return $this->hasMany(Assistant::class);
    }

    public function medicalSupplies()
    {
        return $this->hasMany(MedicalSupply::class);
    }

    public function chats()
    {
        return $this->hasMany(Chat::class);
    }

    public function responses()
    {
        return $this->hasMany(Response::class);
    }

}
