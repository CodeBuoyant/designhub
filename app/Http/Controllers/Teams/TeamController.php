<?php

namespace App\Http\Controllers\Teams;

use App\Http\Controllers\Controller;
use App\Http\Resources\TeamResource;
use App\Models\Team;
use App\Repositories\Contracts\IInvitation;
use App\Repositories\Contracts\ITeam;
use App\Repositories\Contracts\IUser;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TeamController extends Controller
{
    protected $teams;
    protected $users;
    protected $invitations;

    public function __construct(ITeam $teams, IUser $users, IInvitation $invitations) {
        $this->teams = $teams;
        $this->users = $users;
        $this->invitations = $invitations;
    }

    public function index(Request $request) {
        //
    }

    public function findById($id) {
        return new TeamResource($this->teams->find($id));
    }

    public function findBySlug($slug) {
        $team = $this->teams->findWhereFirst('slug', $slug);
        return new TeamResource($team);
    }

    public function store(Request $request) {
        $this->validate($request, [
            'name' => ['required', 'string', 'max:80', 'unique:teams,name']
        ]);

        $team = $this->teams->create([
            'owner_id' => auth()->id(),
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);

        return new TeamResource($team);
    }

    public function fetchUserTeams() {
        return TeamResource::collection($this->teams->fetchUserTeams());
    }

    public function update(Request $request, $id) {
        $team = $this->teams->find($id);
        $this->authorize('update', $team);

        $this->validate($request, [
            'name' => ['required', 'string', 'max:80', 'unique:teams,name,'.$id]
        ]);

        $team = $this->teams->update($id, [
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);

        return new TeamResource($team);
    }

    public function destroy($id) {
        $team = $this->teams->find($id);
        $this->authorize('delete', $team);

        $team->delete();

        return response()->json(['message' => 'Deleted'], 200);
    }

    public function removeFromTeam($teamId, $userId) {
        // Get the team
        $team = $this->teams->find($teamId);

        // Get the user
        $user = $this->users->find($userId);

        // Check that the user is not the owner
        if ($user->isOwnerOfTeam($team)) {
            return response()->json(['message' => 'You are the team owner'], 401);
        }

        /**
         * Check that the person sending the request is either
         * the owner of the team or
         * the person who wants to leave the team
        **/
        if (!auth()->user()->isOwnerOfTeam($team) && auth()->id() !== $user->id) {
            return response()->json(['message' => 'You are not authorized for this action'], 401);
        }

        $this->invitations->removeUserFromTeam($team, $user);
        return response()->json(['message' => 'Success'], 200);
    }
}
