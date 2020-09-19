<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\Contracts\IUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Events\Verified;
use Illuminate\Validation\ValidationException;

class VerificationController extends Controller
{
    protected $users;

    /**
     * Create a new controller instance.
     *
     * @param IUser $users
     */
    public function __construct(IUser $users)
    {
        $this->middleware('throttle:6,1')->only('verify', 'resend');
        $this->users = $users;
    }

    /**
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function verify(Request $request, User $user) {
        // Check URL is a valid signed URL
        if (! URL::hasValidSignature($request)) {
            return response()->json(["errors" => [
                "message" => "Invalid verification link"
            ]], 422);
        }

        // Check if the user has already verified account
        if ($user->hasVerifiedEmail()) {
            return response()->json(["errors" => [
                "message" => "Email address already verified"
            ]], 422);
        }

        $user->markEmailAsVerified();
        event(new Verified($user));

        return response()->json(["message" => "Email successfully verified"], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function resend(Request $request) {
        $this->validate($request, [
            'email' => ['email', 'required']
        ]);

//        $user = User::where('email', $request->email)->first();
        $user = $this->users->findWhereFirst('email', $request->email);

        if (!$user) {
            return response()->json(["errors" => [
                "email" => "No user associated with this email address"
            ]], 422);
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['status' => 'Verification link resent']);
    }
}
