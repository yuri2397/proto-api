<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Models\PasswordResetToken;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;
class UserController extends Controller
{
    /**
     * Enregistrer un nouvel utilisateur.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User successfully registered',
            'token' => $token,
            'user' => $user,
        ], 201);
    }

    /**
     * Connexion d'un utilisateur existant.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required']]
        );


        $user = User::whereEmail($request->email)->first();

            if ($user && Hash::check($request->password, $user->password) ) {

            $token = $user->createToken('auth_token', $user->permissions->toArray())->plainTextToken;
           
            return response()->json([
                'message' => 'User successfully logged in',
                'token' => $token,
                'user' => $user,
                'permissions' => $user->getAllPermissions(),
                'roles' => $user->getRoleNames(),
            ]);
        }
        return response()->json(['message' => 'Invalid login credentials'], 401);
    }

    /**
     * Demander un OTP pour la réinitialisation du mot de passe.
     */
    public function requestOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $otp = Str::random(6);
        $token = PasswordResetToken::updateOrCreate(
            ['email' => $request->email],
            ['token' => $otp, 'created_at' => Carbon::now()]
        );

        // Envoyer l'OTP par email
        Mail::raw("Votre code OTP est: $otp", function ($message) use ($request) {
            $message->to($request->email)
                ->subject('Code de réinitialisation de mot de passe');
        });

        return response()->json(['message' => 'OTP sent successfully']);
    }

    /**
     * Valider l'OTP reçu par email.
     */
    public function validateOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:password_reset_tokens,email',
            'otp' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $token = PasswordResetToken::where('email', $request->email)
            ->where('token', $request->otp)
            ->first();

        if (!$token || $token->created_at->addMinutes(15)->isPast()) {
            return response()->json(['message' => 'Invalid or expired OTP'], 400);
        }

        return response()->json(['message' => 'OTP validated successfully']);
    }

    /**
     * Mettre à jour le mot de passe.
     */
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $token = PasswordResetToken::where('email', $request->email)
            ->where('token', $request->otp)
            ->first();

        if (!$token || $token->created_at->addMinutes(15)->isPast()) {
            return response()->json(['message' => 'Invalid or expired OTP'], 400);
        }

        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        $token->delete(); // Supprimer le token après utilisation

        return response()->json(['message' => 'Password updated successfully']);
    }

    /**
     * Déconnexion de l'utilisateur.
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'User successfully logged out']);
    }

    /**
     * Obtenir les détails du profil de l'utilisateur.
     */
    public function profile(Request $request)
    {
        $user = $request->user();
        return $user->load( $request->input('with') ?? ['roles', 'permissions']);
    }


    public function verifyEmail($id, $hash)
    {

        $user = User::findOrFail($id);

        $tokenRecord = PasswordResetToken::where('email', $user->email)
            ->where('token', $hash)
            ->first();

        if ($tokenRecord) {
            $password = Str::random(8);
            $user->update(['email_verified_at' => now(), 'status' => User::STATUS_ACTIVE, 'password' => Hash::make($password)]);
            $user->save();
            
            Mail::raw("Votre mot de passe est: " . $password, function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Informations de connexion');
            });
            DB::table('password_reset_tokens')
                ->where('email', $user->email)
                ->where('token', $hash)
                ->delete();

            return view('account-verify')->with('message', 'Votre compte a été activé avec succès.');
        }

        return view('account-verify')->with('message', 'Le lien d\'activation est invalide ou expiré.');
    }
}
