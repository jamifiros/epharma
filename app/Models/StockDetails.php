<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockDetails extends Model
{
    use HasFactory;

    protected $table = 'stock_details';

    protected $fillable = [
        'medicine_id',
        'batch_no',
        'expiry_date',
        'quantity',
        'payout',
        'balance',
    ];

    /**
     * Get the stock that owns the stock details.
     */
    public function stock()
{
    return $this->belongsTo(Stock::class, 'medicine_id', 'id');
}

}
