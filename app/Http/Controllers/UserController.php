<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use HasApiTokens;
    public function registration(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        } else {
            $user = new User();
            $user->fname = $request->input('firstName');
            $user->lname = $request->input('lastName');
            $user->email = $request->input('email');
            $user->password = bcrypt($request->input('password'));
            $user->phone = $request->input('phone');
            $user->country = $request->input('country');
            $user->city = $request->input('city');
            $user->save();
    
            // Generate a new API token for the user
            $token = $user->createToken('api-token');
    
            return response()->json(['token' => $token->plainTextToken], 200);
        }
    }
    public function login(Request $request)
    {
        // Validate the request data
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        // Attempt to authenticate the user
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // Authentication successful

            // Get the authenticated user
            $user = Auth::user();

            // Revoke existing tokens
            $user->tokens()->delete();

            // Generate a new API token for the user
            $token = $user->createToken('api-token');

            // Return the token as a response
            return response()->json(['token' => $token->plainTextToken], 200);
        } else {
            // Authentication failed
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
    }
    



    
    public function user(Request $request)
    {
        // Authenticate the user using the provided token
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Get the authenticated user
        $user = Auth::user();

        // Return the user information
        return response()->json(['user' => $user], 200);
    }

  public function logout(Request $request)
    {
        // Authenticate the user using the provided token
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Revoke the user's token
        $user = Auth::user();
        $user->tokens()->delete();

        // Return a success message
        return response()->json(['message' => 'Logged out successfully'], 200);
    }


function displayAllUser (){
    $users = User::all();

return $users;
}

function displaySpecificUser($id){
    $user= User::find($id);
    return $user;
}

    function updateUser( Request $request, $id){

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email,' . $id,
       ]);

       if ($validator->fails()) {
           return response()->json(['errors' => $validator->errors()], 422);
       }

       $user = User::findOrFail($id);
    $user->fname = $request->input('firstName');
    $user->lname = $request->input('lastName');
    $user->email = $request->input('email');
    $user->phone = $request->input('phone');
    $user->country = $request->input('country');
    $user->is_admin = $request->input('isAdmin');
    $user->city = $request->input('city');
    $user->save();

       return response()->json(["success"], 200);
}

function deleteUser($id) {
    $result = User::where('id', $id)->delete();
    
    if ($result) {
        return response()->json(['message' => 'User deleted successfully'], 200);
    } else {
        return response()->json(['message' => 'User not found'], 404);
    }
}

public function addUser(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|email|unique:users',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 400);
    }

    $user = new User();
    $user->fname = $request->input('firstName');
    $user->lname = $request->input('lastName');
    $user->email = $request->input('email');
    $user->password = bcrypt($request->input('password'));
    $user->phone = $request->input('phone');
    $user->country = $request->input('country');
    $user->city = $request->input('city');
    $user->save();

    return response()->json(['message' => "User added successfully!"], 200);
}

function SearchUser($key) {
    return User::where('fname', 'LIKE', '%' . $key . '%')
                ->orWhere('lname', 'LIKE', '%' . $key . '%')
                ->get();
}

}
