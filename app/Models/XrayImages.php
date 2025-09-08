<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XrayImages extends Model
{
    use HasFactory;

    protected $fillable = [
      'patient_id',
      'file_path',
      'description',
      'uploaded_at',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
