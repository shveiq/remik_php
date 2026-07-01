<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Table(timestamps: false)]
class GameSummary extends Model{

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class, 'game_id', 'id');
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

}
