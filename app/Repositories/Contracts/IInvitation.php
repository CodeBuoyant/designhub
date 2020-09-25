<?php

namespace App\Repositories\Contracts;

interface IInvitation
{
    public function addUserToTeam($team, $userId);

    public function removeUserFromTeam($team, $userId);
}
