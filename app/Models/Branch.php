<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'address',
        'latitude',
        'longitude',
        'radius',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function barcodes(): HasMany
    {
        return $this->hasMany(Barcode::class);
    }
}
