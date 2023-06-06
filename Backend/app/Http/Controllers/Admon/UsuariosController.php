<?php

namespace App\Http\Controllers\Admon;

use App\Http\Controllers\Controller;
use App\Models\Admon\UsuariosEmpresas;
use App\Models\User;
use App\Models\Admon\Roles;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Admon\Ajustes;
use Illuminate\Http\Request;

class UsuariosController extends Controller
{
    /**
     * Obtener usuario especifico
     *
     * @access  public
     * @param
     * @return JsonResponse
     * @author octaviom
     */

    public function obtenerUsuario(Request $request)
    {
        $rules = [
            'id'=> 'required|integer|min:1'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $conf = session()->get('id_empresa');
            $usuario = User::where('id',$request->id)->with('rol')->first();
            $roles = Roles::select(['id_rol','descripcion'])->where('id_empresa', '=', $conf)->orderby('id_rol')->get();
            if(!empty($usuario)){
                return response()->json([
                    'status' => 'success',
                    'result' => [
                        'usuario'=>$usuario,
                        'roles'=>$roles
                    ],
                    'messages' => null
                ]);

            }
            else{
                return response()->json([
                    'status' => 'error',
                    'result' => array('id'=>["Datos no encontrados"]),
                    'messages' => null
                ]);
            }

        } else {
            return response()->json([
                'status' => 'error',
                'result' => $validator->messages(),
                'messages' => null
            ]);
        }
    }

    /**
     * Actualizar contraseña de usuario - usuario con menor privilegio
     * @access 	public
     * @param
     * @return JsonResponse
     */

    public function obtenerUserLogin(Request $request)
    {
        $rules = [
        ];
        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $conf = session()->get('id_empresa');
            $usuario = User::where('id_usuario',Auth::user()->id)->first();
            $roles = Roles::select(['id_rol','descripcion'])->orderby('id_rol')->get();
            if(!empty($usuario)){
                if(!empty($twoFactorEnabled)){
                    return response()->json([
                        'status' => 'success',
                        'result' => [
                            'usuario'=>$usuario,
                            'roles'=>$roles,
                          ],
                        'messages' => null
                    ]);
                }else {
                    return response()->json([
                        'status' => 'success',
                        'result' => ['usuario' => $usuario,
                            'roles' => $roles,
                        ],
                        'messages' => null
                    ]);
                }

            }
            else{
                return response()->json([
                    'status' => 'error',
                    'result' => array('id_usuario'=>["Datos no encontrados"]),
                    'messages' => null
                ]);
            }

        } else {
            return response()->json([
                'status' => 'error',
                'result' => $validator->messages(),
                'messages' => null
            ]);
        }
    }
    /**
     * Registrar Nuevo Usuario
     *
     * @access 	public
     * @param
     * @return JsonResponse
     * @author octaviom
     */

    public function registrar(Request $request)
    {
        $rules = [
            'rol' => 'required|array',
            'rol.id_rol'=>'required|integer|min:1',
            'name' => 'required|string|max:50|unique:pgsql.public.users,name',
            'password' => 'required|confirmed|string|max:60',
            'email' => 'required|email'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=',Auth::user()->id)->first();
            $conf = $usuario_empresa->id_empresa;
            $usuario = new User();
            $usuario->password = Hash::make($request->password);
            $usuario->name = $request->name;
            $usuario->id_rol = $request->rol['id_rol'];
            $usuario->remember_token = '';
            $usuario->email = $request->email;
            $usuario->estado = 1; //estado activo
            $usuario->id_empresa = $conf;
            $usuario->save();

            /**
             * Regitramos la relación del usuario con la empresa
            */
            $usuario_empresa = new UsuariosEmpresas();
            $usuario_empresa->id_empresa = $conf;
            $usuario_empresa->id_usuario = $usuario->id;
            $usuario_empresa->save();

            return response()->json([
                'status' => 'success',
                'result' => null,
                'messages' => null
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'result' => $validator->messages(),
                'messages' => null
            ]);
        }
    }


    /**
     * Obtener Lista de Usuarios
     *
     * @access  public
     * @param
     * @return JsonResponse
     */

    public function obtener(Request $request, User $user)
    {
        $users = $user->obtenerUsuarios($request);
        return response()->json([
            'status' => 'success',
            'result' => [
                'total' => $users->total(),
                'rows' => $users->items()
            ],
            'messages' => null
        ]);
    }

    /**
     * Cambiar Contrasena de usuario
     *
     * @access 	public
     * @param
     * @return JsonResponse
     */

    public function cambiarContrasena(Request $request)
    {
        $rules = [
            'id' => 'required|integer|exists:pgsql.public.users,id',
            'cambiar_contrasena' => 'required|boolean',
            'password' => 'required_if:cambiar_contrasena,true|confirmed|string|max:60|nullable',
            'rol' => 'array|required',
            'rol.id_rol' => 'required|integer|min:1'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $user = User::find($request->id);
            if($request->cambiar_contrasena){

                $user->password = Hash::make($request->password);

            }
            $user->name = $request->name;
            $user->email = $request->email;
            $user->id_rol = $request->rol['id_rol'];
            $user->save();

            return response()->json([
                'status' => 'success',
                'result' => null,
                'messages' => null
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'result' => $validator->messages(),
                'messages' => null
            ]);
        }
    }
}
