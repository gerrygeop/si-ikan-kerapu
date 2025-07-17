<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fish extends Model
{
    protected $guarded = ['id'];

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
