<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\GoogleAPI;
use Google\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'social', 'socialRegister']]);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     tags={"Auth"},
     *     summary="Login",
     *     operationId="login",
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found"
     *     ),
     * )
     *
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $email = Redis::get('gUser');
        if ($email) {
            Redis::del('gUser');
            $user = User::where('email', $email)->first();

            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            return $this->respondWithToken(auth()->login($user));
        }

        $credentials = request(['email', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function user()
    {
        return response()->json(['data' => auth()->user()]);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Client $client)
    {
        auth()->logout();
        $client->revokeToken();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * @OA\Get(
     *     path="/api/auth/refresh",
     *     tags={"Auth"},
     *     summary="Refresh token",
     *     operationId="refresh",
     *     security={{"token":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(
     *             @OA\Items(
     *                 type="array",
     *                 @OA\Items(
     *                     ref="#/components/schemas/Order"
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(response=400, description="Bad request"),
     * )
     *
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * @OA\Post(
     *     path="/api/auth/register",
     *     tags={"Auth"},
     *     summary="Register",
     *     operationId="register",
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Account is already exists"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found"
     *     ),
     * )
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $email = $request->get('email');

        if (User::where('email', $email)->exists()) {
            return response()->json(['error' => 'Account is already exists'], 401);
        }

        $user = new User();
        $user->name = $request->get('name');
        $user->email = $email;
        $user->password = Hash::make($request->get('password'));
        $user->verified = true; // temp hack

        $user->save();

        $token = auth()->login($user);

        return $this->respondWithToken($token);
    }

    /**
     * @param Request $request
     * @param GoogleAPI $googleAPI
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function social(Request $request, GoogleAPI $googleAPI)
    {
        $code = $request->get('code');

        if (!$code) {
            return response()->json(['error' => 'Auth failed'], 401);
        }

        $token = $googleAPI->getToken($code);

        if (!$token['access_token']) {
            return response()->json(['error' => 'Auth failed'], 401);
        }

        $userinfo = $googleAPI->getUserInfo();

        if (User::where('email', $userinfo->getEmail())->exists()) {
            $user = User::where('email', $userinfo->getEmail())->first();
        } else {
            $user = new User();
            $user->name = $userinfo->getName();
            $user->email = $userinfo->getEmail();
            $user->password = Hash::make(Str::random());
            $user->verified = true; // temp hack
            $user->save();
        }

        return $this->respondWithToken(auth()->login($user));
    }

    /**
     * @param Request $request
     * @param GoogleAPI $googleAPI
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function socialOld(Request $request, GoogleAPI $googleAPI)
    {
        $response = $googleAPI->getToken($request->get('code'));

        if (! $response['access_token']) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = User::where('email', $googleAPI->getUserInfo()->email)->first();

        if (!$user) {
            return $this->socialRegister($request, $googleAPI);
        }

        return $this->respondWithToken(auth()->login($user));
    }

    /**
     * @param Request $request
     * @param GoogleAPI $googleAPI
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function socialRegister(Request $request, GoogleAPI $googleAPI)
    {
        $response = $googleAPI->getToken($request->get('code'));

        if (! $response['access_token']) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if (User::where('email', $googleAPI->getUserInfo()->email)->exists()) {
            return $this->social($request, $googleAPI);
        }

        $user = new User();
        $user->name = $googleAPI->getUserInfo()->name;
        $user->email = $googleAPI->getUserInfo()->email;
        $user->password = Hash::make(Str::random());

//        if (in_array($googleAPI->getUserInfo()->hd, Config::get('google.accepted_domains'))) {
            $user->verified = true; // temp hack
//        }

        $user->save();

        $token = auth()->login($user);

        return $this->respondWithToken($token);
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ])
            ->header('authorization', 'Bearer ' . $token);
    }
}
