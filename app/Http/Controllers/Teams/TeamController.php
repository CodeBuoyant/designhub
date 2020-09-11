<?php

namespace App\Http\Controllers\Teams;

use App\Http\Controllers\Controller;
use App\Http\Resources\TeamResource;
use App\Models\Team;
use App\Repositories\Contracts\ITeam;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TeamController extends Controller
{
    protected $teams;

    public function __construct(ITeam $teams) {
        $this->teams = $teams;
    }

    public function index(Request $request) {
        //
    }

    public function findById($id) {
        return new TeamResource($this->teams->find($id));
    }

    public function findBySlug($slug) {
        //
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
        //
    }
}
