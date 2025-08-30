<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalSupply extends Model
{
    use HasFactory;

    protected $fillable = [
            'name' ,
            'category' ,
            'quantity_in_stock',
            'clinic_id' ,
            'reorder_level',
            'center_id'
    ];

    public function center()
    {
        return $this->belongsTo(Center::class);
    }
}
