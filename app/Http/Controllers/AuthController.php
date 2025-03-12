<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\TryCatch;

class AuthController extends Controller
{
    public function login() {
        return view('login');
    }

    public function loginSubmit(Request $request) {
        //form validation
        $request->validate(
            //rules
            [
                'text_username' => 'required|email',
                'text_password' => 'required|min:6|max:16'
            ],
            //error messages
            [
                'text_username.required' => 'Username é obrigatório',
                'text_username.email' => 'Username deve ser um email válido',
                'text_password.required' => 'Password é obrigatório',
                'text_password.min' => 'Password deve ter pelo menos :min caracteres',
                'text_password.max' => 'Password deve possuir no máximo :max caracteres',

            ]
        );

        //get user input
        $username = $request->input('text_username');
        $password = $request->input('text_password');
        
        //testing database connection
        /*try {
            DB::connection()->getPdo();
            echo 'Connection success!';
        } catch (\PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }*/

        //get all the users from database
        //$users = User::all()->toArray();

        //as an object instance of the model's class
        /*$userModel = new User();
        $users = $userModel->all()->toArray();
        echo '<pre>';
        print_r($users);*/

        //check if users exist
        $user = User::where('username', $username)
                      ->where('deleted_at', NULL)
                      ->first();
        if(!$user) {
            return redirect()
                    ->back()
                    ->withInput()
                    ->with('loginError', 'Username incorreto.');
        }

        //check if password is correct
        if(!password_verify($password, $user->password)) {
            return redirect()
                    ->back()
                    ->withInput()
                    ->with('loginError', 'Password incorreto.');
        }

        //update last login
        $user->last_login = date('Y-m-d H:i:s');
        $user->save();

        //login user in session
        session([
            'user' => [
                'id' => $user->id,
                'username' => $user->username
            ]
        ]);

        echo 'LOGIN COM SUCESSO!';

    }

    public function logout() {
        //logout form the application
        session()->forget('user');
        return redirect()->to('/login');
    }
}
