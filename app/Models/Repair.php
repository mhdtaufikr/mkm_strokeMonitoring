<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Repair extends Model
{
    use HasFactory;
    public function MstStrokeDies()
    {
        return $this->belongsTo(MstStrokeDies::class, 'id_dies', 'id');
    }

}
