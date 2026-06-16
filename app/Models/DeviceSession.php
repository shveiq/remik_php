<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Table(timestamps: false)]
class DeviceSession extends Model
{
    public function device(): HasOne
    {
        return $this->hasOne(Device::class);
    }

    public function decode_session_data()
    {
        return json_decode($this->session_data, true);
    }

    public function save_session_data(array $data)
    {
        $this->session_data = json_encode($data);
        $this->save();
    }

    public function user()
    {
        $data = $this->decode_session_data();
        if (array_key_exists('user_id', $data)) {
            return User::find($data['user_id']);
        }
        return null;
    }
}