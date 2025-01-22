<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $table = 'stocks';

    protected $fillable = [
        'medicine_name',
        'MRP',
        'sailed_quatity'
    ];

    /**
     * Get the stock details associated with the stock.
     */
    public function stockDetails()
{
    return $this->hasOne(StockDetails::class, 'medicine_id', 'id');
}
public function medicines()
{
    return $this->hasMany(Medicine::class, 'medicine_id', 'id');
}
}
