<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Table(timestamps: false)]
class User extends Model
{
    public function level(): HasOne
    {
        return $this->hasOne(Level::class);
    }

    public function league(): HasOne
    {
        return $this->hasOne(League::class);
    }

    public function devices(): BelongsToMany
    {
        return $this->belongsToMany(Device::class, 'users_devices');
    }

    public static function createNewUser(string $nickname, string $email, string $password, string $birthday) : User
    {
        $user = new User();
        $user->nickname = $nickname;
        $user->email = $email;
        $user->birthday = $birthday;
        $user->password = password_hash($password, PASSWORD_ARGON2I);
        $user->isBot = false;
        $user->save();
        return $user;
    }

    public function startingData()
    {
        $league_type = 1;
        $league = League::findLeageueFreeSlot($league_type);
        if (!$league) {
            $league = League::createNewLeague($league_type, bots: true);
        }

        $this->level_id = 1;
        $this->level_points = 0;
        $this->league_points = 0;
        $this->league_id = $league->id;
        $this->coins_amount = 2500;
        $this->diamonds_amount = 50;
        $this->save();

        if ($league->free_slots > 0) {
            $league->free_slots -= 1;
            $league->save();
        }
    }
}
