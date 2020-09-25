<?php

namespace App\Repositories\Eloquent;

use App\Models\Invitation;
use App\Repositories\Contracts\IInvitation;

class InvitationRepository extends BaseRepository implements IInvitation
{
    public function model() {
        return Invitation::class;
    }

    public function addUserToTeam($team, $userId) {
        $team->members()->attach($userId);
    }

    public function removeUserFromTeam($team, $userId) {
        $team->members()->detach($userId);
    }
}
