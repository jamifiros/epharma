<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicineIntake extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'medicine_id',
        'intake',
        'count'
    ];

    /**
     * Relationship with User model.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship with Medicine model.
     */
    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }
}
