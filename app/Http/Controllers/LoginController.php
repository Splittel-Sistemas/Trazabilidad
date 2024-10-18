<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    //
    public function register(Request $request){
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();
        Auth::login($user);
        return redirect(route('privada'));
    }
    public function login(Request $request){
        //validacion
        $credentials =[
            "email" => $request->email,
            "password" => $request->password,
            //"active => true
        ];
        $remember = ($request->Has('remember') ? true : false);
        if(Auth::attempt($credentials,$remember)){

            $request->session()->regenerate();

            return redirect()->intended(route('menu'));

        }else{
            return redirect('login');
        }
    }
    public function logout(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect(route('login'));
        
    }
    public function login_view(){
        return view('layouts.login');
    }

}
