<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *   path="/auth/register",
     *   tags={"Auth"},
     *   summary="Registro de usuário e criação de tenant",
     *   @OA\RequestBody(required=true, @OA\JsonContent(
     *     required={"name","email","password"},
     *     @OA\Property(property="name", type="string"),
     *     @OA\Property(property="email", type="string"),
     *     @OA\Property(property="password", type="string", format="password")
     *   )),
     *   @OA\Response(response=201, description="Registrado")
     * )
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'tenant_name' => 'nullable|string|max:255',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'is_active' => true,
        ]);

        $tenant = Tenant::create([
            'name' => $data['tenant_name'] ?? ($user->name . " Workspace"),
            'plan' => 'free',
        ]);

        $member = Member::create([
            'user_id' => $user->id,
            'tenant_id' => $tenant->id,
            'role' => 'OWNER',
            'invited_at' => now(),
            'joined_at' => now(),
            'is_active' => true,
        ]);

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
            'tenant' => $tenant,
        ], 201);
    }

    /**
     * @OA\Post(
     *   path="/auth/login",
     *   tags={"Auth"},
     *   summary="Login",
     *   @OA\RequestBody(required=true, @OA\JsonContent(
     *     required={"email","password"},
     *     @OA\Property(property="email", type="string"),
     *     @OA\Property(property="password", type="string", format="password")
     *   )),
     *   @OA\Response(response=200, description="OK"),
     *   @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = $request->user();
        $user->forceFill(['last_login_at' => now()])->save();
        $token = $user->createToken('api')->plainTextToken;

        return response()->json(['token' => $token, 'user' => $user]);
    }

    /**
     * @OA\Post(
     *   path="/auth/refresh",
     *   tags={"Auth"},
     *   security={{"sanctum":{}}},
     *   summary="Refresh do token (rota emite um novo e revoga o atual)",
     *   @OA\Response(response=200, description="OK")
     * )
     */
    public function refresh(Request $request)
    {
        $user = $request->user();
        $request->user()->currentAccessToken()?->delete();
        $token = $user->createToken('api')->plainTextToken;
        return response()->json(['token' => $token]);
    }

    /**
     * @OA\Post(
     *   path="/auth/forgot",
     *   tags={"Auth"},
     *   summary="Solicita e-mail de reset de senha",
     *   @OA\RequestBody(@OA\JsonContent(@OA\Property(property="email", type="string"))),
     *   @OA\Response(response=200, description="OK")
     * )
     */
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        Password::sendResetLink($request->only('email'));
        return response()->json(['message' => 'OK']);
    }

    /**
     * @OA\Post(
     *   path="/auth/reset",
     *   tags={"Auth"},
     *   summary="Reseta senha",
     *   @OA\RequestBody(@OA\JsonContent(
     *     required={"email","token","password"},
     *     @OA\Property(property="email", type="string"),
     *     @OA\Property(property="token", type="string"),
     *     @OA\Property(property="password", type="string")
     *   )),
     *   @OA\Response(response=200, description="OK")
     * )
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return response()->json(['message' => __($status)]);
    }
}

