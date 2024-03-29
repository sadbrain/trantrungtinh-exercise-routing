<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::prefix('/user')->group(function () {
    Route::get("/", function () {
        global $users;
        $string = "";
        foreach ($users as $user) {
            $string .= $user["name"] . ", ";
        }
        return "The users are: $string";
    });

    Route::get("/", function () {
        global $users;
        return response()->json($users);
    });

    Route::get("/{index}", function ($index) {
        global $users;
        if (array_key_exists($index, $users)) {
            return response()->json($users[$index]);
        } else {
            return response()->json(['error' => "Can not find user with index $index"], 404);
        }
    })->whereNumber("index");

    Route::get('/{userName}', function ($userName) {
        global $users;
        
        // Search for the user by name
        $user = null;
        foreach ($users as $u) {
            if ($u['name'] === $userName) {
                $user = $u;
                break;
            }
        }

        // If user found, return user data. Otherwise, return an error message.
        if ($user !== null) {
            return response()->json($user);
        } else {
            return response()->json(['error' => "Can not find user with name $userName"], 404);
        }
    })->whereAlpha("userName");
    
    // Fallback route
    Route::fallback(function () {
        return response()->json(['error' => 'You can not get a user like this!'], 400);
    });

    Route::get("/{userIndex}/post/{postIndex}", function ($userIndex, $postIndex) {
        global $users;

        if (!array_key_exists($userIndex, $users)) {
            return response()->json(['error' => "Cannot find the post with id $postIndex for user $userIndex"], 404);
        }

        $user = $users[$userIndex];

        if (!isset($user['posts'][$postIndex])) {
            return response()->json(['error' => "Cannot find the post with id $postIndex for user $userIndex"], 404);
        }

        return response()->json(['post' => $user['posts'][$postIndex]]);
    })->whereNumber(['userIndex','postIndex']);
});