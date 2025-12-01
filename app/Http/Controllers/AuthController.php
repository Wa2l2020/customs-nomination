<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $input = $request->input('email_prefix'); // Keeping the name from view for now to avoid breaking view immediately
        $password = $request->input('password');

        // Always use 'email' column for login, as it stores both emails and usernames (like 'Wa2l')
        $field = 'email';

        if (Auth::attempt([$field => $input, 'password' => $password])) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            $user->last_login_at = now();
            $user->save();
            
            return redirect($user->role === 'admin' ? '/admin/dashboard' : '/dashboard');
        }

        return back()->withErrors(['msg' => 'بيانات الدخول غير صحيحة']);
    }
    
    public function logout() {
        Auth::logout();
        return redirect('/login');
    }

    public function searchUsers(Request $request)
    {
        $query = $request->get('query');
        if (strlen($query) < 3) {
            return response()->json([]);
        }

        $emails = User::where('email', 'LIKE', "{$query}%")
                      ->limit(10)
                      ->pluck('email');

        return response()->json($emails);
    }
}
