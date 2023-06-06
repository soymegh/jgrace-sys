<?php

namespace App\Http\Controllers\Admon;

use App\Http\Controllers\Controller;
use App\Models\Admon\UsuariosEmpresas;
use App\Models\Admon\Impuestos;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ImpuestosController extends Controller
{
    /**
     * Obtener una lista de Roles (con opcion de busqueda y paginacion)
     *
     * @access  public
     * @param
     * @return JsonResponse
     */

    public function obtener(Request $request, Impuestos $impuestos)
    {
        $impuestos = $impuestos->obtenerImpuestos($request);
        return response()->json([
            'status' => 'success',
            'result' => [
                'total' => $impuestos->total(),
                'rows' => $impuestos->items()
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

    public function obtenerTodos(Request $request, Impuestos $impuestos)
    {
        $impuestos = Impuestos::orderby('id_impuesto')->get();
        return response()->json([
            'status' => 'success',
            'result' => $impuestos,
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

    public function obtenerImpuesto(Request $request)
    {
        $rules = [
            'id_impuesto'=> 'required|integer|min:1'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $impuestos = Impuestos::find($request->id_impuesto);

            if(!empty($impuestos)){
                return response()->json([
                    'status' => 'success',
                    'result' => $impuestos,
                    'messages' => null
                ]);

            }
            else{
                return response()->json([
                    'status' => 'error',
                    'result' => array('id_impuesto'=>["Datos no encontrados"]),
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
            'descripcion' => 'required|string|max:100|unique:pgsql.public.impuestos,descripcion',
            'tasa' => 'required|numeric|regex:/^\d*(\.\d{1,2})?$/'
        ];
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $impuesto = new Impuestos;
            $impuesto->descripcion = $request->descripcion;
            $impuesto->tasa = $request->tasa;
            $impuesto->u_grabacion = Auth::user()->name;
            $impuesto->id_empresa = $usuario_empresa->id_empresa;
            $impuesto->estado = 1;
            $impuesto->save();

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
        $rules = [
            'id_impuesto' => 'required|integer|min:1',
            // 'descripcion' => 'required|string|max:100|unique:pgsql.admon.roles,descripcion',
            'descripcion' => [
                'required',
                'string',
                'max:100',
                Rule::unique('pgsql.public.impuestos')->ignore($request->id_impuesto,'id_impuesto')
            ],
            'tasa' => [
                'required',
                'numeric',
                'regex:/^\d*(\.\d{1,2})?$/'
            ],
        ];



        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $impuesto = Impuestos::find($request->id_impuesto);
            $impuesto->descripcion = $request->descripcion;
            $impuesto->tasa = $request->tasa;
            $impuesto->u_modificacion = Auth::user()->name;
            $impuesto->save();

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
            'id_impuesto' => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $impuesto = Impuestos::find($request->id_impuesto);
            $impuesto->u_modificacion = Auth::user()->name;
            $impuesto->estado = 0;
            $impuesto->save();

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
            'id_impuesto' => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $impuesto = Impuestos::find($request->id_impuesto);
            $impuesto->u_modificacion = Auth::user()->name;
            $impuesto->estado = 1;
            $impuesto->save();

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
            //$input = 'C:/xampp/htdocs/resources/reports/ReporteImpuestos';
            //$output = 'C:/xampp/htdocs/resources/reports/' .$hora_actual . 'ReporteImpuestos';
            $input = '/var/www/html/resources/reports/ReporteImpuestos';
            $output = '/var/www/html/resources/reports/'.$hora_actual.'ReporteImpuestos';

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

            return response()->download($output . '.' . $ext ,$hora_actual. 'ReporteImpuestos.' . $ext, $headers);

            /*exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
               print_r($output);*/
        }
    }
}
