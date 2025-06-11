<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'name',
        'description',
        'duration',
        'price',
    ];

    public function providers()
    {
        return $this->belongsToMany(Provider::class)
            ->withPivot('price_override')
            ->withTimestamps();
    }

}
