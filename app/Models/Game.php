<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table(timestamps: false)]
class Game extends Model{

    public function table(): BelongsTo
    {
        return $this->belongsTo(RemikTable::class, 'table_id', 'id');
    }

    public function currentPlayer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'current_player_id', 'id');
    }

    public function players(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'games_users');
    }

    public function playerCards(): HasMany
    {
        return $this->hasMany(GameUser::class, 'game_id', 'id');
    }
}
