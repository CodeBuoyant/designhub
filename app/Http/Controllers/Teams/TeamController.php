<?php

namespace App\Http\Controllers\Teams;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\ITeam;
use Illuminate\Http\Request;

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
        //
    }

    public function findBySlug($slug) {
        //
    }

    public function store(Request $request) {
        //
    }

    public function fetchUserTeams() {
        //
    }

    public function update(Request $request, $id) {
        //
    }

    public function destroy($id) {
        //
    }
}
