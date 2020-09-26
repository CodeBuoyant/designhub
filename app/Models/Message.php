<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use SoftDeletes;

    // Automatically update the updated_at field
    protected $touches = ['chat'];

    protected $fillable = [
        'user_id',
        'chat_id',
        'body',
        'last_read'
    ];

    public function getBodyAttribute($value) {
        if ($this->trashed()) {
            if (!auth()->check()) return null;

            return auth()->id() === $this->sender->id ?
                'You deleted this message' :
//                "{$this->sender->name} deleted this message";
                "This message is deleted";
        }

        return $value;
    }

    /**
     * @return BelongsTo
     */
    public function chat() {
        return $this->belongsTo(Chat::class);
    }

    /**
     * @return BelongsTo
     */
    public function sender() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
