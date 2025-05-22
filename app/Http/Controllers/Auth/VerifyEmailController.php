<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;

class VerifyEmailController extends Controller
{
    public function __invoke(Request $request, $id, $hash): JsonResponse
    {
        $user = User::findOrFail($id);

        // ✅ Vérification que le hash correspond bien à l'email de l'utilisateur
        if (!hash_equals((string) $hash, sha1($user->email))) {
            return response()->json(['status' => 'invalid-link'], 403);
        }

        // ✅ Si déjà vérifié
        if ($user->hasVerifiedEmail()) {
            return response()->json(['status' => 'verification-link-already']);
        }

        // ✅ Vérifie l'e-mail
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return response()->json(['status' => 'verification-link-success']);
    }
}
