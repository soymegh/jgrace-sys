<?php

namespace App\Http\Controllers\Admon;

use App\Http\Controllers\Controller;
use App\Models\Admon\Roles;
use App\Models\Admon\Sucursales;
use App\Models\Admon\UsuariosEmpresas;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use PHPJasper\PHPJasper;
use App\Models\Admon\Ajustes;
use Illuminate\Http\Request;

class SucursalesController extends Controller
{
    public function buscar(Request $request, Sucursales $sucursales)
    {
        $sucursales = $sucursales->buscar($request);
        return response()->json([
            'results' => $sucursales
        ]);
    }
    /**
     * Obtener una lista de Roles (con opcion de busqueda y paginacion)
     *
     * @access  public
     * @param
     * @return JsonResponse
     */

    public function obtener(Request $request, Sucursales $sucursales)
    {
        $sucursales = $sucursales->obtenerSucursales($request);
        return response()->json([
            'status' => 'success',
            'result' => [
                'total' => $sucursales->total(),
                'rows' => $sucursales->items()
            ],
            'messages' => null
        ]);
    }

    /**
     * Obtener una lista de Roles sin paginacion
     *
     * @access  public
     * @param
     * @return JsonResponse
     */

    public function obtenerTodas(Request $request, Sucursales $sucursales)
    {
        $sucursales = Sucursales::orderby('id_sucursal')->get();
        return response()->json([
            'status' => 'success',
            'result' => $sucursales,
            'messages' => null
        ]);
    }

    /**
     * obtener Rol Especifico
     *
     * @access  public
     * @param
     * @return JsonResponse
     */

    public function obtenerSucursal(Request $request)
    {
        $rules = [
            'id_sucursal'=> 'required|integer|min:1'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $sucursales = Sucursales::find($request->id_sucursal);

            if(!empty($sucursales)){
                return response()->json([
                    'status' => 'success',
                    'result' => $sucursales,
                    'messages' => null
                ]);

            }
            else{
                return response()->json([
                    'status' => 'error',
                    'result' => array('id_sucursal'=>["Datos no encontrados"]),
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
     * @param
     * @return JsonResponse
     */

    public function registrar(Request $request)
    {
        $rules = [
            'descripcion' => 'required|string|max:100|unique:pgsql.public.sucursales,descripcion',
            'serie' => 'required|string|max:2|unique:pgsql.public.sucursales,serie',
            'telefonos' => 'required|string|max:50',
            'direcciones' => 'required|string|max:50',
            'numero_autorizacion' => 'required|string|max:50',
            'numeracion_facturacion' => 'required|integer|min:0',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $sucursal = new Sucursales;
            $sucursal->descripcion = $request->descripcion;
            $sucursal->serie = $request->serie;
            $sucursal->telefonos = $request->telefonos;
            $sucursal->numeracion_facturacion = $request->numeracion_facturacion;
            $sucursal->direccion = $request->direcciones;
            $sucursal->numero_autorizacion = $request->numero_autorizacion;
            $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
            $sucursal->id_empresa= $usuario_empresa->id_empresa;
            $sucursal->u_creacion = Auth::user()->name;
            $sucursal->estado = 1;
            $sucursal->save();

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
     * @param
     * @return JsonResponse
     */

    public function actualizar(Request $request)
    {
        $request['serie'] =  strtoupper($request->serie);
        $rules = [
            'id_sucursal' => 'required|integer|min:1',
            // 'descripcion' => 'required|string|max:100|unique:pgsql.admon.roles,descripcion',
            'descripcion' => [
                'required',
                'string',
                'max:100',
                Rule::unique('pgsql.public.sucursales')->ignore($request->id_sucursal,'id_sucursal')
            ],

            'serie' => [
                'required',
                'string',
                'max:2',
                Rule::unique('pgsql.public.sucursales')->ignore($request->id_sucursal,'id_sucursal')
            ],
            'numeracion_facturacion' => 'required|integer|min:0',
            'telefonos' => 'required|string|max:50',
            'direcciones' => 'required|string|max:50',
            'numero_autorizacion' => 'required|string|max:50',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $sucursal = Sucursales::find($request->id_sucursal);
            $sucursal->descripcion = $request->descripcion;
            $sucursal->serie = $request->serie;
            $sucursal->telefonos = $request->telefonos;
            $sucursal->numero_facturacion = $request->numeracion_facturacion;
            $sucursal->direccion = $request->direcciones;
            $sucursal->numero_autorizacion = $request->numero_autorizacion;
            $sucursal->u_modificacion = Auth::user()->name;
            $sucursal->save();

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
     */

    public function desactivar(Request $request)
    {
        $rules = [
            'id_sucursal' => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $sucursal = Sucursales::find($request->id_sucursal);
            $sucursal->u_modificacion =Auth::user()->name;
            $sucursal->estado = 0;
            $sucursal->save();

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
     */

    public function activar(Request $request)
    {
        $rules = [
            'id_sucursal' => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $rol = Sucursales::find($request->id_sucursal);
            $rol->u_modificacion =Auth::user()->name;
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

    public function generarReporte($ext)
    {
        // echo $ext;
        // $ext = 'pdf';
        $os = array("xls", "pdf");
        if (in_array($ext, $os)) {
            $hora_actual = time() ;
            //$input = 'C:/xampp/htdocs/resources/reports/ReporteSucursales';
            //$output = 'C:/xampp/htdocs/resources/reports/' .$hora_actual . 'ReporteSucursales';
            $input = '/var/www/html/resources/reports/ReporteSucursales';
            $output = '/var/www/html/resources/reports/'.$hora_actual.'ReporteSucursales';

            $options = [
                'format' => [$ext],
                'locale' => 'en',
                'params' => [],
                'db_connection' => [
                    'driver' => 'postgres',
                    'username' => env('DB_USERNAME'),
                    'password' => env('DB_PASSWORD'),
                    'host' => env('DB_HOST'),
                    'database' => env('DB_DATABASE'),
                    'port' => env('DB_PORT')
                ]
            ];

            $jasper = new PHPJasper;

            $jasper->process($input, $output, $options)->execute();
            /*header('Content-Description: File Transfer');
            header('Content-Type: multipart/form-data;boundary="boundary"');
            header('Content-Disposition: form-data; filename=' . $hora_actual. 'Accesos.' . $ext);
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Content-Length: ' . filesize($output . '.' . $ext));
            flush();
            readfile($output . '.' . $ext);*/
            // unlink($output . '.' . $ext);

            $headers = [
                'Content-Type' => 'application/pdf',
            ];

            return response()->download($output . '.' . $ext ,$hora_actual. 'ReporteSucursales.' . $ext, $headers);

            /*exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
               print_r($output);*/
        }
    }
}
