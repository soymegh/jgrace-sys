<?php

namespace App\Http\Controllers\Contabilidad;

use App\Http\Controllers\Controller;
use PHPJasper\PHPJasper;
use Validator,Auth;
use App\Models\Contabilidad\TiposCuentas;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class TiposCuentasController extends Controller
{
    /**
     * Obtener una lista de Tipos de cuenta
     *
     * @access  public
     * @param
     * @return \Illuminate\Http\JsonResponse
     */

    public function obtenerTiposCuenta(Request $request, TiposCuentas $tipos_cuenta)
    {
        $tipos_cuenta = $tipos_cuenta->obtenerTiposCuenta($request);
        return response()->json([
            'status' => 'success',
            'result' => [
                'total' => $tipos_cuenta->total(),
                'rows' => $tipos_cuenta->items()
            ],
            'messages' => null
        ]);
    }

    /**
     * Obtener una lista de TIPOS DE CUENTA sin ningun filtro
     *
     * @access  public
     * @param
     * @return  json(array)
     */

    public function obtenerTodosTiposCuenta(Request $request, TiposCuentas $tipos_cuenta)
    {
        $tipos_cuenta = TiposCuentas::all();
        return response()->json([
            'status' => 'success',
            'result' => $tipos_cuenta,
            'messages' => null
        ]);
    }

    /**
     * obtener Tipo de cuenta Especifico
     *
     * @access  public
     * @param
     * @return  json(array)
     */

    public function obtenerTipoCuenta(Request $request)
    {
        $rules = [
            'id_tipo_cuenta' => 'required|integer|min:1'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $tipo_cuenta = TiposCuentas::where('id_tipo_cuenta',$request->id_tipo_cuenta)->with('tipoCuentaEstadoFinanciero')->get();
            if(!empty($tipo_cuenta[0])){
                return response()->json([
                    'status' => 'success',
                    'result' => $tipo_cuenta[0],
                    'messages' => null
                ]);

            }
            else{
                return response()->json([
                    'status' => 'error',
                    'result' => array('id_tipo_cuenta'=>["Datos no encontrados"]),
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
     * Actualizar Tipo de cuenta existente
     *
     * @access  public
     * @param
     * @return  json(string)
     */

    public function actualizar(Request $request)
    {
        $rules = [
            'id_tipo_cuenta'  => 'required|integer|min:1',
            'tipo_abreviado' => [
                'required',
                'string',
                'max:2',
                Rule::unique('pgsql.contabilidad.tipos_cuentas')->ignore($request->id_tipo_cuenta,'id_tipo_cuenta')
            ],
            'descripcion' => [
                'required',
                'string',
                'max:50',
                Rule::unique('pgsql.contabilidad.tipos_cuentas')->ignore($request->id_tipo_cuenta,'id_tipo_cuenta')
            ]
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $tipo_Cuenta = TiposCuentas::find($request->id_tipo_cuenta);
            $tipo_Cuenta->descripcion = $request->descripcion;
            $tipo_Cuenta->tipo_abreviado = $request->tipo_abreviado;
            $tipo_Cuenta->save();

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
            //$input = 'C:/xampp/htdocs/resources/reports/ReporteTiposCuentas';
            //$output = 'C:/xampp/htdocs/resources/reports/' .$hora_actual . 'ReporteTiposCuentas';
            $input = '/var/www/html/resources/reports/ReporteTiposCuentas';
            $output = '/var/www/html/resources/reports/'.$hora_actual.'ReporteTiposCuentas';

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

            return response()->download($output . '.' . $ext ,$hora_actual. 'ReporteTiposCuentas.' . $ext, $headers);

            /*exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
               print_r($output);*/
        }
    }
}
