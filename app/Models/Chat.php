<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chat extends Model
{
    /**
     * @return BelongsToMany
     */
    public function participants() {
        return $this->belongsToMany(User::class, 'participants');
    }

    /**
     * @return HasMany
     */
    public function messages() {
        return $this->hasMany(Message::class);
    }

    // Helper methods
    /**
     * Get latest message
     *
     * @return Model|HasMany|object|null
     */
    public function getLatestMessageAttribute() {
        return $this->messages()->latest()->first();
    }

    /**
     * Check if chat is unread
     *
     * @param $userId
     * @return bool
     */
    public function isUnreadForUser($userId) {
        return (bool)$this->messages()
            ->whereNull('last_read')
            ->where('user_id', '<>', $userId)
            ->count();
    }

    /**
     * @param $userId
     */
    public function markAsReadForUser($userId) {
        $this->messages()
            ->whereNull('last_read')
            ->where('user_id', '<>', $userId)
            ->update([
                'last_read' => Carbon::now()
            ]);
    }
}
