<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barcode extends Model
{
    use HasFactory;
    use HasTimestamps;

    protected $fillable = [
        'branch_id',
        'name',
        'value',
        'latitude',
        'longitude',
        'radius',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    function getLatLngAttribute(): array|null
    {
        if (is_null($this->latitude) || is_null($this->longitude)) {
            return null;
        }
        return  [
            'lat' => $this->latitude,
            'lng' => $this->longitude
        ];
    }
}
