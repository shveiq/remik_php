<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table(timestamps: false)]
class Device extends Model
{
    public function sessions(): HasMany
    {
        return $this->hasMany(DeviceSession::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(Device::class, 'users_devices');
    }

}