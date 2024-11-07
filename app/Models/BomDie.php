<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BomDie extends Model
{
    use HasFactory;

    // Specify the table name
    protected $table = 'bom_dies';

    // Define the fillable fields for mass assignment
    protected $fillable = [
        'id_dies',
        'name',
        'size',
        'qty',
    ];

    // Define the relationship with the `mst_strokedies` table
    public function dies()
    {
        return $this->belongsTo(MstStrokedie::class, 'id_dies');
    }
}
