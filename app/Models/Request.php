<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    use HasFactory;

    protected $fillable = ['patient_id','chat_id','content','sent_at'];



    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }
}
