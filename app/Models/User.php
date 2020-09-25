<?php

namespace App\Models;

use App\Http\Controllers\Auth\ResetPasswordController;
use App\Notifications\ResetPassword;
use App\Notifications\VerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;

class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use Notifiable, SpatialTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'tagline',
        'about',
        'username',
        'location',
        'formatted_address',
        'available_to_hire'
    ];

    protected $spatialFields = [
        'location',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Rest omitted for brevity

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier() {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * @param $password
     */
    public function setPasswordAttribute($password)
    {
        if ( !empty($password) ) {
            $this->attributes['password'] = Hash::make($password);
        }
    }

    /**
     *
     */
    public function sendEmailVerificationNotification() {
        $this->notify(new VerifyEmail);
    }

    /**
     * @param string $token
     */
    public function sendPasswordResetNotification($token) {
        $this->notify(new ResetPassword($token));
    }

    /**
     * @return HasMany
     */
    public function designs() {
        return $this->hasMany(Design::class);
    }

    /**
     * @return HasMany
     */
    public function comments() {
        return $this->hasMany(Comment::class);
    }

    /**
     * @return BelongsToMany
     */
    public function teams() {
        return $this->belongsToMany(Team::class)->withTimestamps();
    }

    /**
     * @return BelongsToMany
     */
    public function ownedTeams() {
        return $this->teams()->where('owner_id', $this->id);
    }

    /**
     * @param $team
     * @return bool
     */
    public function isOwnerOfTeam($team) {
        return (bool)$this->teams()
                        ->where('id', $team->id)
                        ->where('owner_id', $this->id)
                        ->count();
    }

    /**
     * @return HasMany
     */
    public function invitations() {
        return $this->hasMany(Invitation::class, 'recipient_email', 'email');
    }

    /**
     * @return BelongsToMany
     */
    public function chats() {
        return $this->belongsToMany(Chat::class, 'participants');
    }

    /**
     * @return HasMany
     */
    public function messages() {
        return $this->hasMany(Message::class);
    }

    // Helper methods for chat

    /**
     * @param $user_id
     * @return Builder|Model|BelongsToMany|mixed|object|null
     */
    public function getChatWithUser($user_id) {
        $chat = $this->chats()->whereHas('participants', function ($query) use($user_id){
            $query->where('user_id', $user_id);
        })->first();

        return $chat;
    }
}
