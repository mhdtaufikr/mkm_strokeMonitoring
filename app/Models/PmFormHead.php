<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PmFormHead extends Model
{
    use HasFactory;
    public function MstStrokeDies()
    {
        return $this->belongsTo(MstStrokeDies::class, 'dies_id', 'id');
    }
}
