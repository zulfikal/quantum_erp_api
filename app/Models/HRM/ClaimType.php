<?php

namespace App\Models\HRM;

use Illuminate\Database\Eloquent\Model;

class ClaimType extends Model
{
    protected $fillable = [
        'name',
    ];

    public function claims()
    {
        return $this->hasMany(Claim::class);
    }
}
