<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;
    protected $fillable = [
        'patient_id',
        'center_id'
    ];




    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function center()
    {
        return $this->belongsTo(Center::class);
    }

    public function requests()
    {
        return $this->hasMany(Request::class);
    }

    public function responses()
    {
        return $this->hasMany(Response::class);
    }
}
