<?php

namespace App\Http\Controllers\Admon;

use App\Http\Controllers\Controller;
use App\Models\Admon\Permisos;
use App\Models\Admon\UsuariosEmpresas;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Validator;
use App\Models\Admon\Ajustes;
use Illuminate\Http\Request;

class PermisosController extends Controller
{
    /**
     * Obtener una lista de permisos
     *
     * @access  public
     * @param Request $request
     * @param Permisos $roles
     * @return JsonResponse
     * @author octaviom
     */

    public function obtenerPermisos(Request $request)
    {
        $rules = [
            'id_rol' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            // $conf = session()->get('id_empresa');
            //$permisos = AdmonPermisos::where('id_rol', '=', Auth::user()->id_rol)->get();
            $permisos = Permisos::where('id_rol', '=', $request->id_rol)
                ->join('admon.menus','admon.menus.id_menu','admon.permisos.id_menu')->get();
            $permisos = $permisos->map(function($permiso, $key) {
                return (int)$permiso['id_menu'];
            });

            return response()->json([
                'status' => 'success',
                'result' => $permisos,
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
     * Obtener una lista de Roles sin paginacion
     *
     * @access  public
     * @param
     * @return JsonResponse
     * @author octaviom
     */

    public function guardarPermiso(Request $request)
    {
        $rules = [
            'id_rol' => 'required|integer|min:1'
        ];

        try{
            DB::beginTransaction();
            $validator = Validator::make($request->all(), $rules);
            $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
            if (!$validator->fails()) {

                //RRHHGruposFamiliares::where('id_trabajador',$trabajador->id_trabajador)->delete();
                Permisos::where('id_rol', '=', $request->id_rol)->delete();


                foreach ($request->menus as $id_menu) {
                    $permisos = new Permisos();
                    $permisos->id_rol = $request->id_rol;
                    $permisos->id_menu  = $id_menu;
                    //$conf = session()->get('id_empresa');
                    //$permisos->id_empresa = $conf;
                    $permisos->id_empresa = $usuario_empresa->id_empresa;
                    $permisos->save();
                }
                if(Permisos::where('id_menu',1)->where('id_rol',$request->id_rol)->exists()){
                    DB::commit();
                }else{
                    $permisos_principal = new Permisos();
                    $permisos_principal->id_rol = $request->id_rol;
                    $permisos_principal->id_menu  = 1;
                    $permisos_principal->id_empresa = $usuario_empresa->id_empresa;
                    $permisos_principal->save();
                    DB::commit();
                }

                DB::commit();


                return response()->json([
                    'status' => 'success',
                    'result' => null,
                    'messages' => null
                ]);

            } else {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'result' => $validator->messages(),
                    'messages' => null
                ]);
            }

        } catch (Exception $e){
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'result' => 'Error de base de datos',
                'messages' => null
            ]);
        }
    }

    /**
     * obtener Rol Especifico
     *
     * @access  public
     * @param
     * @return JsonResponse
     * @author octaviom
     */

    public function obtenerRolEspecifico(Request $request)
    {
        $rules = [
            'id_rol'=> 'required|integer|min:1'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $roles = Roles::find($request->id_rol);

            if(!empty($roles)){
                return response()->json([
                    'status' => 'success',
                    'result' => $roles,
                    'messages' => null
                ]);

            }
            else{
                return response()->json([
                    'status' => 'error',
                    'result' => array('id_rol'=>["Datos no encontrados"]),
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
     * Crear un nuevo rol
     *
     * @access  public
     * @param Request $request
     * @return JsonResponse
     * @author octaviom
     */

    public function crearRol(Request $request)
    {
        $rules = [
            'descripcion' => 'required|string|max:100|unique:pgsql.admon.roles,descripcion',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $conf = session()->get('id_empresa');
            $rol = new Roles;
            $rol->descripcion = $request->descripcion;
            $rol->estado = 1; // Estado 1 -> Activo
            $rol->id_empresa = $conf;
            $rol->save();

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
     * Actualizar Rol existente
     *
     * @access  public
     * @param Request $request
     * @return JsonResponse
     * @author octaviom
     */

    public function actualizarRol(Request $request)
    {
        $rules = [
            'id_rol' => 'required|integer',
            // 'descripcion' => 'required|string|max:100|unique:pgsql.admon.roles,descripcion',
            'descripcion' => [
                'required',
                'string',
                'max:100',
                Rule::unique('pgsql.admon.roles')->ignore($request->id_rol,'id_rol')
            ],
        ];



        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $conf = session()->get('id_empresa');
            $rol = Roles::find($request->id_rol);
            $rol->descripcion = $request->descripcion;
            $rol->id_empresa = $conf;
            $rol->save();

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
     * Desactiva rol
     *
     * @access  public
     * @param
     * @return JsonResponse
     * @author octaviom
     */

    public function desactivaRol(Request $request)
    {
        $rules = [
            'id_rol' => 'required|integer'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $rol = Roles::find($request->id_rol);
            $rol->estado = 0;
            $rol->save();

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
     * Activa rol
     *
     * @access  public
     * @param
     * @return JsonResponse
     * @author octaviom
     */

    public function activaRol(Request $request)
    {
        $rules = [
            'id_rol' => 'required|integer'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $rol = Roles::find($request->id_rol);
            $rol->estado = 1;
            $rol->save();

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
