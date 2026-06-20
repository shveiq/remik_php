<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table(timestamps: false)]
class LeagueType extends Model
{
    public function leagues(): HasMany
    {
        return $this->hasMany(League::class);
    }
}