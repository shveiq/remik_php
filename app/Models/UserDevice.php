<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Table(name: "users_devices", timestamps: false)]
class UserDevice extends Model
{
    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function device(): HasOne
    {
        return $this->hasOne(Device::class);
    }
}