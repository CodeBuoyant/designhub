<?php

namespace App\Http\Controllers\Teams;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\IInvitation;
use Illuminate\Http\Request;

class InvitationController extends Controller
{
    protected $invitations;

    public function __construct(IInvitation $invitations) {
        $this->invitations = $invitations;
    }

    public function invite() {
        //
    }

    public function resend() {
        //
    }

    public function respond() {
        //
    }

    public function destroy() {
        //
    }
}
