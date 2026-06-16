<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table(timestamps: false)]
class League extends Model
{
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}