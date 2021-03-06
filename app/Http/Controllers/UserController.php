<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

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
        $this->authorize('update', $user);
        
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255']
        ]);

        if(!$this->IsEmailUniqueForUpdating($request->email, $user->id))
        {
            return back()->withErrors(['email' => 'Ezzel az email címmel már létezik regisztrált felhasználó!']);
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email
        ]);

        return redirect('/dashboard');
    }

    /**
     * Update user password. 
     * 
     * @param Request $request
     * @param User $user
     * @return void
     */
    public function updatePassword(Request $request, User $user)
    {
        $this->authorize('updatePassword', $user);
        
        if(! Hash::check($request->old_password, $request->user()->password))
        {
            return back()->withErrors(['old_password' => 'Hibás jelszó!']);
        }

        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()]
        ]);
        
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect('/dashboard');
    }

    /**
     * Render access level edit screen.
     * 
     * @param Request $request
     * @param User $user
     * 
     * @return void
     */
    public function editAccess(Request $request, User $user)
    {
        $this->authorize('updateAccess', $user);
        
        return view('auth.update-access', ['user' => $user]);
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
        $this->authorize('updateAccess', $user);
        
        $request->validate([
            'access_level' => ['required', 'integer', 'max:3']
        ]);

        $user->update([
            'access_level' => $request->access_level
        ]);

        return redirect('/dashboard');
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
        $this->authorize('delete', $user);
        
        $user->delete();

        return redirect('/dashboard');
    }

    /**
     * Determine the given email is unique for updating.
     * 
     * @param string $email
     * @param int $id
     * @return bool
     */
    protected function IsEmailUniqueForUpdating($email, $id)
    {
        $sameEmails = User::Where([
                                    ['id', '<>', $id],
                                    ['email', $email]
                                ])->get();

        if($sameEmails->count() > 0)
        {
            return false;
        }

        return true;
    }
}
