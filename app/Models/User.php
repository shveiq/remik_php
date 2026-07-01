<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table(timestamps: false)]
class User extends Model
{
    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    public function devices(): BelongsToMany
    {
        return $this->belongsToMany(Device::class, 'users_devices');
    }

    public function games(): BelongsToMany
    {
        return $this->belongsToMany(Game::class, 'games_users');
    }

    public function gameSummaries(): HasMany
    {
        return $this->hasMany(GameSummary::class, 'user_id', 'id');
    }

    public static function createNewUser(string $nickname, string $email, string $password, string $birthday) : User
    {
        $league_type = 1;
        $league = League::findLeageueFreeSlot($league_type);
        if (!$league) {
            $league = League::createNewLeague($league_type, bots: true);
        }

        $user = new User();
        $user->nickname = $nickname;
        $user->email = $email;
        $user->birthday = $birthday;
        $user->password = password_hash($password, PASSWORD_ARGON2ID);
        $user->isBot = false;
        $user->isGuest = false;
        $user->league_id = $league->id;
        $user->level_id = 1;
        $user->save();

        if ($league->free_slots > 0) {
            $league->free_slots -= 1;
            $league->save();
        }

        return $user;
    }

    public static function createBot(string $nickname, string $avatar_id, int $league_id, int $level_id, int $level_points, $coins, $diamonds) : User
    {
        $user = new User();
        $user->nickname = $nickname;
        $user->avatar_id = $avatar_id;
        $user->isBot = true;
        $user->isGuest = false;
        $user->league_id = $league_id;
        $user->level_id = $level_id;
        $user->level_points = $level_points;
        $user->coins_amount = $coins;
        $user->diamonds_amount = $diamonds;
        $user->save();
        return $user;
    }

    public static function createGuest() : User
    {
        $league_type = 1;
        $league = League::findLeageueFreeSlot($league_type);
        if (!$league) {
            $league = League::createNewLeague($league_type, bots: true);
        }

        $count = User::count();

        $avatarId = random_int(0, 103);

        $user = new User();
        $user->nickname = "UPL".$count;
        $user->avatar_id = "avatar_".$avatarId."_.png";
        $user->isBot = false;
        $user->isGuest = true;
        $user->league_id = $league->id;
        $user->level_id = 1;
        $user->save();

        if ($league->free_slots > 0) {
            $league->free_slots -= 1;
            $league->save();
        }

        return $user;
    }

    public function startingData()
    {
        $this->level_points = 0;
        $this->league_points = 0;
        $this->coins_amount = 5000;
        $this->diamonds_amount = 30;
        $this->save();
    }

    public function saveDevice(Device $device) {
        $user_device = new UserDevice();
        $user_device->user_id = $this->id;
        $user_device->device_id = $device->id;
        $user_device->save();
    }
}
