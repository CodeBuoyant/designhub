<?php

namespace App\Models\Traits;

use App\Models\Like;

trait Likeable
{
    public function likes() {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function like() {
        // Check if the person is authenticated
        if ( !auth()->check() ) {
            return;
        }

        // Check if the current user already liked
        if ($this->isLikedByUser(auth()->id())) {
            return;
        }

        $this->likes()->create([ 'user_id' => auth()->id() ]);
    }

    public function isLikedByUser( $userId ) {
        return (bool)$this->likes()->where('user_id', $userId)->count();
    }
}
