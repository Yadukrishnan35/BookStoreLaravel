<?php

namespace App\Http\Controllers;

use App\Exceptions\BookStoreException;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
class UserController extends Controller
{
    function register(Request $request) {
        
        try {
            $credentials = $request->only('role','first_name', 'last_name', 'phone_no', 'email', 'password', 'confirm_password');
            $validator = Validator::make($credentials, [
                'role' => 'required|string|between:2,10',
                'first_name' => 'required|string|between:2,50',
                'last_name' => 'required|string|between:2,50',
                'phone_no' => 'required|string|min:10',
                'email' => 'required|string|email|max:100',
                'password' => 'required|string|min:6',
                'confirm_password' => 'required|same:password',
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 400);
            }
            $userCheck = User::getUserByEmail($request->email);
            if ($userCheck) {
                Log::info('The email has already been taken: ');
                throw new BookStoreException('The email has already been taken.', 401);
            }

            $user = User::create([
                'role' => $request->role,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone_no' => $request->phone_no,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);
            Cache::remember('users', 3600, function () {
                return DB::table('users')->get();
            });

            $token = JWTAuth::attempt($credentials);


            $data = array(
                'name' => $user->first_name, "VerificationLink" => $token,
                "email" => $request->email,
                "fromMail" => env('MAIL_USERNAME'),
                "fromName" => env('APP_NAME'),
            );

            Mail::send('verifyEmail', $data, function ($message) use ($data) {

                $message->to($data['email'], $data['name'])->subject('Verify Email');
                $message->from('yadulive333@gmail.com', 'Yadu');
            });

            return response()->json([
                'message' => 'User successfully registered',
                'user' => $user
            ], 201);

        }catch(BookStoreException $exception) {
            return response()->json([
                'message' => $exception->message()
            ], $exception->statusCode());
        }
        
    }
    public function login(Request $request)
    {
        try {

            $credentials = $request->only('email', 'password');

            $validator = Validator::make($credentials, [
                'email' => 'required|email',
                'password' => 'required|string|min:6|max:50'
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return response()->json(['error' => 'Invalid credentials entered'], 400);
            }

            Cache::remember('users', 3600, function () {
                return DB::table('users')->get();
            });

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                Log::error('Not a Registered Email');
                throw new bookstoreexception('Not a Registered Email', 404);
                return response()->json([
                    'message' => 'Email is not registered',
                ], 404);
            }

            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Login credentials are invalid.',
                ], 400);
            }

             //Token created, return with success response and jwt token
             Log::info('Login Successful');
             return response()->json([
                 'success' => true,
                 'message' => 'Login successful',
                 'token' => $token,
             ], 200);


        }catch(BookStoreException $exception) {
            return response()->json([
                'message' => $exception->message()
            ], $exception->statusCode());
        }
    }
    
    public function logout(Request $request)
    {
        $user = JWTAuth::authenticate($request->token);

        if (!$user) {
            log::warning('Invalid Authorisation ');
            return response()->json([
                'message' => 'Invalid token'
            ], 401);
        } else {
            JWTAuth::invalidate($request->token);
            log::info('User successfully logged out');
            return response()->json([
                'success' => true,
                'message' => 'User has been logged out'
            ], 200);
        }
    }

    public function verifyMail(Request $request)
    {

        $this->validate($request, [
            'token' => 'required'
        ]);

        $user = JWTAuth::authenticate($request->token);
        if (!$user) {
            log::warning('Invalid Authorisation Token ');
        }

        $time = $user->email_verified_at;
        if (!$time) {
            if (!$user) {
                return response()->json(['not found'], 220);
            }

            $user->email_verified_at = now();
            $user->save();
            return response()->json(['verified successfully'], 201);
        } else {
            return response()->json(['already verified'], 222);
        }
    }

    public function get_user(Request $request)
    {
        $this->validate($request, [
            'token' => 'required'
        ]);

        $user = JWTAuth::authenticate($request->token);

        return response()->json(['user' => $user]);
    }

}
