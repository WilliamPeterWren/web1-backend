<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Address;
// use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
// use PHPOpenSourceSaver\JWTAuth\JWTAuth;
// use PHPOpenSourceSaver\JWTAuth\JWT;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException as TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException as TokenInvalidException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException as JWTException;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->json()->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max: 255|unique:users',
            'password' => 'required|string|min:6',
            'password_confirmation' => 'required|string|same:password',
        ]);
        
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create([
            'name' => $request->json()->get('name'),
            'email' => $request->json()->get('email'),
            'password' => Hash::make($request->json()->get('password')),
        ]);

        if($request->json()->get('address')){
            $address = Address::create([
                'user_id' => $user->id,
                'address' => $request->json()->get('address'),
                'firstname' => $request->json()->get('firstname'),
                'lastname' => $request->json()->get('lastname'),
                'city' => $request->json()->get('city'),
                'zip' => $request->json()->get('zip'),
                'country' => $request->json()->get('country'),
                'telephone' => $request->json()->get('telephone'),
            ]);
        }
        else{
            $address = Address::create([
                'user_id' => $user->id,
                'address' => 'address default',
                'firstname' => 'first name by default',
                'lastname' => 'last name by default',
                'city' => 'Vũng Tàu by default',
                'zip' => '+84 by default',
                'country' => 'Việt Nam by default',
                'telephone' => '982000444',
            ]);
        }
        
        $u = User::find($user->id);
        $u->address_id = $address->id;
        $u->save();

        $credentials = request(['email', 'password']);
        $token = auth()->attempt($credentials);
        return response()->json(compact('user', 'token'), 201);
    }

    public function login(Request $request)
    {
        
        
        // $credentials = $request->json()->all();
        $credentials = request(['email', 'password']);
        
        try {

            // if (!$token = JWTAuth::attempt($credentials)) {
            //     return response()->json(['error' => 'invalid credentials'], 400);
            // }

            if (!$token = auth()->attempt($credentials)) {
                return response()->json(['error' => 'aa invalid credentials'], 400);
            }

        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        $user = auth()->user();
        return response()->json(compact('user', 'token'));
    }

    public function getAuthenticatedUser()
    {

        try {

            // if (!$user = JWTAuth::parseToken()->authenticate()) {
            //     return response()->json(['user_not_found'], 404);
            // }

            if (!$user = auth()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

        } catch (TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (JWTException $e) {
            return response()->json(['token_absent'], $e->getStatusCode());
        }

        return response()->json(compact('user'));
    }

    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ]);
    }

    public function index(){
        $users = User::with('addresses')->get();
        return response()->json($users);
    }
    
}