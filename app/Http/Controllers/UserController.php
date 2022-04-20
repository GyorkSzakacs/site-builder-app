<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    
    /**
     * Update user account details. 
     * 
     * @param Request $request
     * @param User $user
     * @return void
     */
    public function update(Request $request, User $user)
    {
        if($request->user()->id != $user->id)
        {
            abort(403);
        }
        
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
    }

    /**
     * Change a user access level.
     * 
     * @param Request $request
     * @param User $user
     * 
     * @return void
     */
    public function updateAccess(Request $request, User $user)
    {
        
        if(!$request->user()->hasAdminAccess() || $request->user()->id == $user->id)
        {
            abort(403);
        }

        $user->update([
            'access_level' => $request->access_level
        ]);
    }

    /**
     * Delete user.
     * 
     * @param Request $request
     * @param User $user
     * 
     * @return void
     */
    public function destroy(Request $request, User $user)
    {
        if(!$request->user()->hasAdminAccess() || $request->user()->id == $user->id)
        {
            abort(403);
        }

        $user->delete();
    }
}
