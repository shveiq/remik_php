<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Table(timestamps: false)]
class League extends Model
{
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function type(): HasOne
    {
        return $this->hasOne(LeagueType::class, 'league_type_id', 'id');
    }


    static public function findLeageueFreeSlot(int $league_type_id): ?League
    {
        return League::where('league_type_id', $league_type_id)
            ->where('free_slots', '>', 0)
            ->first();
    }

    static public function createNewLeague(int $league_type_id, bool $bots = false): League
    {
        $type = LeagueType::find($league_type_id);
        if ($type === null) {
            throw new \Exception("League type not found");
        }

        $league = new League();
        $league->name = $type->name;
        $league->league_type_id = $type->id;
        $league->free_slots = $type->max_users;
        $league->start_date = time();
        $league->end_date = time() + $type->duration;
        $league->save();

        if ($bots) {
        }
        return $league;
    }
}