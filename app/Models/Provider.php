<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'document',
    ];

    public function services()
    {
        return $this->belongsToMany(Service::class)
            ->withPivot('price_override')
            ->withTimestamps();
    }
}
