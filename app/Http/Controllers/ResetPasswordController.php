<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
class ResetPasswordController extends Controller
{
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required',
        ]);

        $passwordReset = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$passwordReset) {
            return response()->json(['message' => 'Invalid token.'], 400);
        }

        $user = User::where('email', $passwordReset->email)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        // Update the user's password using the 'newPassword' from the request
        $user->update(['password' => Hash::make($request->password)]);

        // Delete the used token from the 'password_reset_tokens' table
        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        return response()->json(['message' => 'Password reset successfully.']);
    }
}