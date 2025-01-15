<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'prescriptions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'userid',
        'image',
        'status',
    ];

    /**
     * Define the relationship with the UserDetails model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */


public function user()
    {
        return $this->belongsTo(User::class, 'userid');  // Specify the correct foreign key column 'userid'
    }

    public function medicines()
    {
        return $this->hasMany(Medicine::class);
    }
    
}
