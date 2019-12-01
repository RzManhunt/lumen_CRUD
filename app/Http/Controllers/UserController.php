<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\HTTP\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    function index(Request $request)
    {
        if($request->isJson()){
            $user = User::paginate(10);

            return response()->json(['Usuarios' => $user], 200);
        }

        return response()->json(['error' => 'Unauthorized'], 401, []);
    }

    function create(Request $request)
    {
        if($request->isJson()){
            $user = new User();
            $exists = $user->where('email', $request->email)
                            ->where('username', $request->username)
                            ->first();

            if($exists == null){
                $user->name = $request->name;
                $user->username = $request->username;
                $user->email = $request->email;
                $user->api_token = User::keyGenerator(60);
                $user->password = Hash::make($request->password);
                $user->save();

                return response()->json($user, 201);
            }

            return response()->json(['error' => 'User already exists'], 401);
        }

        return response()->json(['error' => 'Unauthorized'], 401, []);
    }

    function getToken(Request $request)
    {
        if($request->isJson()){
            try{
                $data = $request->json()->all();
                $user = User::where('email', $data['email'])->first();

                if($user && Hash::check($data['password'], $user->password)){
                    return response()->json($user, 200);
                } else{
                    return response()->json(['error' => 'No content'], 406);
                }

            } catch(ModelNotFoundException $e){
                return response()->json(['error' => 'No content'], 406);
            }
        }

        return response()->json(['error' => 'Unauthorized'], 401, []);
    }

    function delete(Request $request)
    {
        if($request->isJson()){
            $data = $request->json()->all();
            
            if(
                User::where('email', $data['email'])
                    ->where('id', $data['id'])
                    ->delete()
            ){
                return response()->json(['messagge' => 'User deleted successfully'], 200);
            } else {
                return response()->json(['error' => 'User does not exists'], 404);
            }
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    function update(Request $request)
    {
        if($request->isJson()){
            $data = $request->json()->all();
            $user = User::where('email', $data['email'])->where('id', $data['id'])->first();

            $user->name = empty($data['name']) ? $user->name : $data['name'];
            $user->username = empty($data['username']) ? $user->username : $data['username'];
            $user->password = empty($data['password']) ? $user->password : Hash::make($data['password']);

            if($user->save()){
                return response()->json(['messagge' => 'User updated successfully'], 200);
            } else {
                return response()->json(['error' => 'User does not exists'], 404);
            }
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
