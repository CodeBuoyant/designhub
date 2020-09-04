<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;

class MeController extends Controller
{
    /**
     * Return current authenticated user
     *
     * @return UserResource|JsonResponse
     */
    public function getMe() {
        if (auth()->check()) {
            $user = auth()->user();
            return new UserResource($user);
        }

        return response()->json(null, 200);
    }
}
