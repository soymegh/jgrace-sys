<?php

namespace App\Http\Controllers\Admon;

use App\Http\Controllers\Controller;
use App\Models\Admon\Permisos;
use App\Models\Admon\Roles;
use App\Models\Admon\UsuariosEmpresas;
use App\Models\User;
use http\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Validator;
use App\Models\Admon\Menus;
use Illuminate\Http\Request;

class ModulosController extends Controller
{

    /**
     *  Función para obtener listado de modulos
     * @param Request $request
     * @return JsonResponse
     * @author octaviom
     * @copyright csoft
     */

    public function obtenerModulos(Request $request){

        $id_empresa = session()->get('id_empresa');
        $modulos = Menus::select('title','route','icon')->get;

        return response()->json([
            'status' => 'success',
            'result' => [
                'modulos' => $modulos
            ],
            'messages' => null
        ]);
    }

    public function verificarPermisoVista(Request $request, Menus $menus)
    {
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
            $permiso = Permisos::where('id_menu', $request->id_menu)->where('id_rol', Auth::user()->id_rol)->where('id_empresa',$usuario_empresa->id_empresa)->first();
            //$conf = session()->get('id_empresa');
            //$permiso = AdmonPermisos::where('id_menu',$request->id_menu)->where('id_rol',Auth::user()->id_rol)->first();
            if (!empty($permiso)) {
                return response()->json([
                    'status' => 'success',
                    'result' => '',
                    'messages' => null
                ]);
            }

//Auth::logout();
        return response()->json([
            'status' => 'error',
            'result' => 'sin permiso',
            'messages' => null
        ]);

    }

    /**
     * Obtener una lista de menus sin ningun filtro
     *
     * @access  public
     * @param Request $request
     * @param Menus $menus
     * @return JsonResponse
     */

    public function obtenerMenusTodos(Request $request)
    {
        // $menus = AdmonMenus::orderBy('id_menu_padre','asc')->get();
        //$conf = session()->get('id_empresa');
        $menus = Menus::orderBy('id_menu','asc')->whereNotIn('tipo_menu', array(1))->orderBy('parent_id','asc')->get();

        $menus_childs = DB::select("select * from admon.menus_childs() order by id_modulo, id_menu");

        $roles = Roles::orderby('id_rol')->get();
        /*$menus = AdmonMenus::select('admon.menus.id_menu', 'admon.menus.id_menu_padre','admon.menus.nombre_menu','admon.menus.menu_seccion',
        'admon.menus.nombre_route','admon.menus.icon','admon.menus.f_creacion','admon.menus.f_modificacion','admon.menus.tipo_menu',
        DB::raw('case when id_menu_padre = 0 then null else id_menu_padre end as id_menu_padrex'))
        ->where('admon.menus.activo',1)
        ->whereIn('admon.menus.tipo_menu', array(1,2,3))
        ->orderBy('secuencia')->get();*/

        return response()->json([
            'status' => 'success',
            'result' => [
                'roles'=>$roles,
                'menus'=>$menus,
                'menus_childs' => $menus_childs
            ]
            ,
            'messages' => null
        ]);
    }
}
