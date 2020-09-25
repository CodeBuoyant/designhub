<?php

namespace App\Repositories\Eloquent;

use App\Models\Invitation;
use App\Repositories\Contracts\IInvitation;

/**
 * Class InvitationRepository
 * @package App\Repositories\Eloquent
 */
class InvitationRepository extends BaseRepository implements IInvitation
{
    /**
     * @return string
     */
    public function model() {
        return Invitation::class;
    }

    /**
     * @param $team
     * @param $userId
     */
    public function addUserToTeam($team, $userId) {
        $team->members()->attach($userId);
    }

    /**
     * @param $team
     * @param $userId
     */
    public function removeUserFromTeam($team, $userId) {
        $team->members()->detach($userId);
    }
}
