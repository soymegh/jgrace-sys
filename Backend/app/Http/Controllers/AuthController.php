<?php

namespace App\Http\Controllers;

use App\Models\Admon\BitacoraAccesos;
use App\Models\Admon\UsuariosEmpresas;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;


class AuthController extends Controller
{

    protected $maxAttempts = 3;
    protected $decayMinutes = 1;

    /**
     * Mostrar listado de usuarios conectados
     */
    public function userActivity(Request $request)
    {
        $users = User::select("*")
            ->whereNotNull('last_seen')
            ->where('estado',1)
            ->orderBy('last_seen', 'DESC')->get();

        foreach ($users as $user){
            if (Cache::has('user-is-online-' . $user->id)){
                $message = "En Línea.";
//                $message = $user->name . " is online. Last seen: " . Carbon::parse($user->last_seen)->diffForHumans();
            }else{
                $message = "Desconectado.";
//                $message = $user->name . " is offline. Last seen: " . Carbon::parse($user->last_seen)->diffForHumans();
            }
            if($message === "En Línea."){ //Llenar arreglo solamente si los usuarios están en línea
                $active_users[] = ['name' =>$user->name,'email' =>$user->email,'estado' =>$message,'last_seen' =>$user->last_seen];
            }
        }

        if(!empty($users)){
            return response()->json([
                'status' => 'success',
                'users' => $users,
                'active_users' => $active_users,
                'message' => 'Datos cargados correctamente.'
            ], 200);
        }
        return response()->json([
            'status' => 'failed',
            'message' => 'Ha ocurrido un error inesperado'
        ], 500);
    }

    /**
     * Create user
     *
     * @param Request $request
     * @return JsonResponse [string] message
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users',
            'password' => 'required|string',
            'c_password' => 'required|same:password'
        ]);

        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if ($user->save()) {
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->plainTextToken;

            return response()->json([
                'message' => 'Successfully created user!',
                'accessToken' => $token,
            ], 201);
        } else {
            return response()->json(['error' => 'Provide proper details']);
        }
    }

    /**
     * Login user and create token
     *
     * @param Request $request
     * @return JsonResponse
     */

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);

        $credentials = request(['email', 'password']);
        $remember_me = request(['remember_me']);
        if (!Auth::attempt($credentials, $remember_me)) {

            return response()->json([
                'message' => 'Unauthorized',
                'status' => 401
            ], 401);
        }

        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->plainTextToken;

        // Search company of user and save it on session
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        session()->put('id_empresa', $usuario_empresa->id_empresa);
        session()->save();

        // Save access user
        $acceso = new BitacoraAccesos;
        $acceso->alias = Auth::user()->name;
        $acceso->id_user = Auth::user()->id;
        $acceso->direccion_ip = $this->getUserIpAddr();
        $acceso->f_acceso = date("Y-m-d H:i:s");
        $acceso->dispositivo = $request->header('User-agent', null);
        $acceso->id_empresa = $usuario_empresa->id_empresa;
        $acceso->save();

        /*$agent = new Agent();
         $agent->setUserAgent( $request->header('User-agent',null));
         $acceso->dispositivo =$agent->platform().' '.$agent->version($agent->platform()) .' '.$agent->browser().' '. (int) $agent->version($agent->browser()).' '. $agent->device();
         */


        return response()->json([
            'accessToken' => $token,
            'token_type' => 'Bearer',
            'status' => 200,
            'statusText' => 'success',
        ], 200);
    }

    /**
     * Get the authenticated User
     *
     * @param Request $request
     * @return JsonResponse [json] user object
     */
    public function me(Request $request)
    {

        $user = Auth::user();
        $userData = User::select('public.users.name', 'admon.roles.descripcion as rol')->join('admon.roles', 'admon.roles.id_rol', 'public.users.id_rol')->where('public.users.id', '=', Auth::user()->id)->get();


        if (Auth::check()) {
            return response()->json([
                'status' => 'success',
                'result' => [
                    'user' => $user,
                    'userData' => $userData
                ]

            ], 200);
        } else {
            return response()->json([
                'status' => 'null',
            ], 401);
        }
    }

    /**
     * Logout user (Revoke the token)
     *
     * @return JsonResponse
     */

    public function logout()
    {
        if (auth()->check()) {
//            request()->session()->flush();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
            session()->forget('id_empresa');
            Auth::logout();
        }
        return response()->json([
            'msg' => 'Logged out succesfully',
            'status' => 200
        ], 200);
    }

    /**
     * The user has been authenticated
     *
     * @param Request $request
     * @param mixed $user
     * @return mixed
     * */
    protected function authenticated(Request $request, $user)
    {
        return response()->json($user);
    }

    protected function loggedOut(Request $request)
    {
        return response()->json((null));
    }

    function getUserIpAddr()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            //ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            //ip pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;

    }

    /**
     * Send password reset link.
     * @param Request $request
     * @return
     */
    public function sendPasswordResetLink(Request $request)
    {
        return $this->sendResetLinkEmail($request);
    }

    /**
     * Get the response for a successful password reset link.
     *
     * @param string $response
     * @return RedirectResponse|JsonResponse
     */
    protected function sendResetLinkResponse($response)
    {
        return response()->json([
            'message' => 'Password reset email sent.',
            'data' => $response
        ]);
    }

    /**
     * Get the response for a failed password reset link.
     *
     * @param Request $request
     * @param string $response
     * @return RedirectResponse|JsonResponse
     */
    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        return response()->json(['message' => 'Email could not be sent to this email address.']);
    }

    /**
     * Handle reset password
     */
    public function callResetPassword(Request $request)
    {
        return $this->reset($request);
    }

    /**
     * Reset the given user's password.
     *
     * @param \Illuminate\Contracts\Auth\CanResetPassword $user
     * @param string $password
     * @return void
     */
    protected function resetPassword($user, $password)
    {
        $user->password = Hash::make($password);
        $user->save();
        event(new PasswordReset($user));
    }

    /**
     * Get the response for a successful password reset.
     *
     * @param string $response
     * @return RedirectResponse|JsonResponse
     */
    protected function sendResetResponse($response)
    {
        return response()->json(['message' => 'Password reset successfully.']);
    }

    /**
     * Get the response for a failed password reset.
     *
     * @param Request $request
     * @param string $response
     * @return RedirectResponse|JsonResponse
     */
    protected function sendResetFailedResponse(Request $request, $response)
    {
        return response()->json(['message' => 'Failed, Invalid Token.']);
    }
}
