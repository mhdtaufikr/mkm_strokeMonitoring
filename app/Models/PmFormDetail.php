<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PmFormDetail extends Model
{
    use HasFactory;
    public function dropdown()
{
    return $this->hasOne(Dropdown::class, 'name_value', 'item_check');
}
}
