<?php

namespace App\Http\Controllers\Teams;

use App\Http\Controllers\Controller;
use App\Mail\SendInvitationToJoinTeam;
use App\Models\Team;
use App\Repositories\Contracts\IInvitation;
use App\Repositories\Contracts\ITeam;
use App\Repositories\Contracts\IUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class InvitationController extends Controller
{
    protected $invitations;
    protected $teams;
    protected $users;

    public function __construct(IInvitation $invitations, ITeam $teams, IUser $users) {
        $this->invitations = $invitations;
        $this->teams = $teams;
        $this->users = $users;
    }

    public function invite(Request $request, $teamId) {
        // Get the team
        $team = $this->teams->find($teamId);

        $this->validate($request, [
            'email' => ['required', 'email']
        ]);

        $user = auth()->user();

        // Check if user owns the team
        if ( ! $user->isOwnerOfTeam($team) ) {
            return response()->json(['email' => 'You are not the team owner'], 401);
        }

        // Check if the email has a pending invitation
        if ($team->hasPendingInvite($request->email)) {
            return response()->json(['email' => 'Email already has a pending invite'], 422);
        }

        // Get the recipient by email
        $recipient = $this->users->findByEmail($request->email);

        // If the recipient does not exist, send invitation to join the team
        if (! $recipient) {
            $this->createInvitation(false, $team, $request->email);
            return response()->json(['email' => 'Invitation sent to user'], 200);
        }

        // If user exist, check if the team already has the user
        if ($team->hasUser($recipient)) {
            return response()->json(['email' => 'User already a member'], 422);
        }

        // If user exist, send the invitation to user
        $this->createInvitation(true, $team, $request->email);
        return response()->json(['email' => 'Invitation sent to user'], 200);
    }

    public function resend($id) {
        $invitation = $this->invitations->find($id);

        // Get the recipient by email
        $recipient = $this->users->findByEmail($invitation->recipient_email);

        Mail::to($invitation->recipient_email)
            ->send(new SendInvitationToJoinTeam($invitation, !is_null($recipient)));

        return response()->json(['message' => 'Invitation resent'], 200);
    }

    public function respond(Request $request, $id) {
        //
    }

    public function destroy($id) {
        //
    }

    protected function createInvitation( bool $user_exists, Team $team, string $email ) {
        $invitation = $this->invitations->create([
            'team_id' => $team->id,
            'sender_id' => auth()->id(),
            'recipient_email' => $email,
            'token' => md5(uniqid(microtime()))
        ]);

        Mail::to($email)->send(new SendInvitationToJoinTeam($invitation, $user_exists));
    }
}
