<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Model;

class TransactionType extends Model
{
    protected $fillable = [
        'name',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
