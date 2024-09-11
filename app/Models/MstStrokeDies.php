<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MstStrokeDies extends Model
{
    use HasFactory;

    // Define the table associated with the model, if different from 'mst_stroke_dies'
    protected $table = 'mst_strokedies';

    // Specify which attributes are mass assignable
    protected $fillable = [
        'code',
        'part_no',
        'process',
        'std_stroke',
        'current_qty',
        'cutoff_date',
        'classification'
    ];

    // Other model methods and properties...
}
