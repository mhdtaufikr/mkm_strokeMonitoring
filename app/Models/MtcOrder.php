<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MtcOrder extends Model
{
    use HasFactory;

    // Specify the table name
    protected $table = 'mtc_orders';

    // Define the fillable fields for mass assignment
    protected $fillable = [
        'id_dies',
        'problem',
        'img',
        'date',
    ];

    // Define the relationship with the `mst_strokedies` table
    public function dies()
    {
        return $this->belongsTo(MstStrokedies::class, 'id_dies');
    }
    public function repair()
    {
        return $this->hasOne(Repair::class, 'id_order', 'id');
    }

}
