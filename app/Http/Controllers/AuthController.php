<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    // Di method authenticated() atau di __construct()
    public function authenticated(Request $request, $user)
    {
        $user->logActivity('logged_in', 'User logged into the system');
        return redirect()->intended($this->redirectPath());
    }
}
