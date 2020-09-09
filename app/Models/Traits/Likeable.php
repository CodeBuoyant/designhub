<?php

namespace App\Models\Traits;

use App\Models\Like;

trait Likeable
{
    // Model observer: run delete method if the model is deleted
    public static function bootLikable() {
        static::deleting(function($model){
            $model->removeLikes();
        });
    }

    // Delete likes when model is being deleted
    public function removeLikes() {
        if($this->likes()->count()){
            $this->likes()->delete();
        }
    }

    public function likes() {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function like() {
        // Check if the person is authenticated
        if ( !auth()->check() ) return;

        // Check if the current user already liked
        if ($this->isLikedByUser(auth()->id())) return;

        $this->likes()->create([ 'user_id' => auth()->id() ]);
    }

    public function unlike() {
        // Check if the person is authenticated
        if ( !auth()->check() ) return;

        // Check if the current user already unliked
        if ( ! $this->isLikedByUser(auth()->id())) return;

        $this->likes()->where([ 'user_id' => auth()->id() ])->delete();
    }

    public function isLikedByUser( $userId ) {
        return (bool)$this->likes()->where('user_id', $userId)->count();
    }
}
