<?php

namespace App\Http\Controllers\CuentasXCobrar;

use App\Http\Controllers\Controller;
use App\Models\Admon\Ajustes;
//use App\Models\CuentasXCobrarCuentasXCobrarImportData;
//use PHPJasper\PHPJasper;
use App\Models\Admon\UsuariosEmpresas;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator, Illuminate\Support\Facades\Auth, Illuminate\Support\Facades\DB;
use App\Models\CuentasXCobrar\CuentasXCobrar;
use Illuminate\Http\Request;
use PHPJasper\PHPJasper;

//use Maatwebsite\Excel\Facades\Excel as Excel;
class CuentasXCobrarController extends Controller
{
    /**
     * Obtener una lista de Cuentas Por Cpbrar
     *
     * @access  public
     * @param Request $request
     * @param CuentasXCobrar $cuenta_x_cobrar
     * @return JsonResponse
     */

    public function obtener(Request $request, CuentasXCobrar $cuenta_x_cobrar)
    {
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        $currency_id = Ajustes::where('id_ajuste',1)->where('id_empresa',$usuario_empresa->id_empresa)->select('valor')->first(); //
        $cuenta_x_cobrar = $cuenta_x_cobrar->obtener($request);
        return response()->json([
            'status' => 'success',
            'result' => [
                'total' => $cuenta_x_cobrar->total(),
                'rows' => $cuenta_x_cobrar->items(),
                'currency_id' => $currency_id->valor
            ],
            'messages' => null
        ]);
    }

    public function obtenerCuentasCliente(Request $request, CuentasXCobrar $cuentas)
    {
        $cuentas = $cuentas->obtenerCuentasCliente($request);

        return response()->json([
            'status' => 'success',
            'result' => $cuentas,
            'messages' => null
        ]);
    }


    public function obtenerCuentasTrabajador(Request $request, CuentasXCobrar $cuentas)
    {
        $cuentas = $cuentas->obtenerCuentasTrabajador($request);

        return response()->json([
            'status' => 'success',
            'result' => $cuentas,
            'messages' => null
        ]);
    }



    /**
     * obtener Cuenta Por Cpbrar
     *
     * @access  public
     * @param
     * @return JsonResponse
     */

    public function obtenerCuentasXCobrar(Request $request)
    {
        $rules = [
            'id_cuentaxcobrar' => 'required|integer|min:1'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $cuenta_x_cobrar = CuentasXCobrar::find($request->id_cuentaxcobrar);
            return response()->json([
                'status' => 'success',
                'result' => $cuenta_x_cobrar,
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



    public function generarReporteAntiguedad(Request $request)
    {
        // echo $ext;
        $rules = [
            'fechaCorte' => 'required|date',
            'id_zona' => 'required|integer',
            'extension' => 'required|string|max:3'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {

        $os = array("xls", "pdf");
        //echo gethostname();
        if (in_array($request->extension, $os)) {

            $hora_actual = time();
            //$input = '/Applications/XAMPP/xamppfiles/htdocs/resources/reports/AntiguedadSaldos';
            //$output = '/Applications/XAMPP/xamppfiles/htdocs/resources/reports/'.$hora_actual.'AntiguedadSaldos';
            //$input = 'C:/xampp/htdocs/resources/reports/AntiguedadSaldos';
            //$output = 'C:/xampp/htdocs/resources/reports/' . $hora_actual . 'AntiguedadSaldos';
            $input = '/var/www/html/resources/reports/AntiguedadSaldos';
            $output = '/var/www/html/resources/reports/' . $hora_actual . 'AntiguedadSaldos';
            $nombre_empresa = Ajustes::where('id_ajuste',4)->select('valor')->first();
            $logo_empresa = Ajustes::where('id_ajuste',3)->select(DB::raw("substr(valor, 2, length(valor) - 2)::json->>'file_thumbnail' as file_thumbnail"))->first();
            $url = '/var/www/html/resources/reports/';

            $options = [
                'format' => [$request->extension],
                'locale' => 'en',
                'params' => [
                 'fechaCorte' => $request->fechaCorte,
                    'id_zona' => $request->id_zona,
                         'empresa_nombre' => $nombre_empresa->valor,
                         'empresa_logo' =>  env('APP_URL_REPORTS').$logo_empresa->file_thumbnail
                ],
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
            $headers = [
                'Content-Type' => 'application/pdf',
            ];

            return response()->download($output . '.' . $request->extension, $hora_actual . 'AntiguedadSaldos.' . $request->extension, $headers);

        }
        else {
                return response()->json([
                    'status' => 'error',
                    'result' => $validator->messages(),
                    'messages' => null
                ]);
            }
            /* exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
                print_r($output);*/
        }else{
            return '';
        }
    }





    public function generarReporteEstadoCuentadetallado(Request $request)
    {
        // echo $ext;
        $rules = [
            'fechaCorte' => 'required|date',
            'fechaInicial' => 'required|date',
            'id_cliente' => 'required|integer|min:1|exists:pgsql.venta.clientes,id_cliente',
            'extension' => 'required|string|max:4'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {

            $os = array("xls", "pdf","html");
            //echo gethostname();
            if (in_array($request->extension, $os)) {

                $hora_actual = time();
                //$input = '/Applications/XAMPP/xamppfiles/htdocs/resources/reports/EstadoCuentaClienteDetalle';
                //$output = '/Applications/XAMPP/xamppfiles/htdocs/resources/reports/'.$hora_actual.'EstadoCuentaClienteDetalle';

                //$input = '/var/www/html/resources/reports/EstadoCuentaClienteDetalle';
                //$output = '/var/www/html/resources/reports/' . $hora_actual . 'EstadoCuentaClienteDetalle';

                $input = 'C:/xampp/htdocs/resources/reports/EstadoCuentaClienteDetalle';
                $output = 'C:/xampp/htdocs/resources/reports/' .$hora_actual . 'EstadoCuentaClienteDetalle';

                $nombre_empresa = Ajustes::where('id_ajuste',4)->select('valor')->first();
                $logo_empresa = Ajustes::where('id_ajuste',3)->select(DB::raw("substr(valor, 2, length(valor) - 2)::json->>'file_thumbnail' as file_thumbnail"))->first();
                //$url = '/var/www/html/resources/reports/';
                $url = 'C:/xampp/htdocs/resources/reports/';

                $options = [
                    'format' => [$request->extension],
                    'locale' => 'en',
                    'params' => [
                        'fechaCorte' => $request->fechaCorte,
                        'id_cliente' => $request->id_cliente,
                        'fechaInicial' => $request->fechaInicial,
                        'directorioReports'=>$url,
                        'empresa_nombre' => $nombre_empresa->valor,
                        'empresa_logo' =>  env('APP_URL_REPORTS').$logo_empresa->file_thumbnail
                    ],
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

                $headers = [
                    'Content-Type' => 'application/pdf',
                ];

                if($request->extension == 'html'){
                    $headers = [
                        'Content-Type' => 'text/html',
                    ];
                }

                /*exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
                print_r($output);*/

                return response()->download($output . '.' . $request->extension, $hora_actual . 'EstadoCuentaClienteDetalle.' . $request->extension, $headers);

            }
            else {
                return response()->json([
                    'status' => 'error',
                    'result' => $validator->messages(),
                    'messages' => null
                ]);
            }

        }else{
            return '';
        }
    }


    public function generarReporteEstadoCuentaConsolidado(Request $request)
    {
        // echo $ext;
        $rules = [
            'fechaCorte' => 'required|date',
            'extension' => 'required|string|max:3'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {

            $os = array("xls", "pdf");
            //echo gethostname();
            if (in_array($request->extension, $os)) {

                $hora_actual = time();
                //$input = '/Applications/XAMPP/xamppfiles/htdocs/resources/reports/EstadoCuentaClienteConsolidado';
                //$output = '/Applications/XAMPP/xamppfiles/htdocs/resources/reports/'.$hora_actual.'EstadoCuentaClienteConsolidado';
                $input = '/var/www/html/resources/reports/EstadoCuentaClienteConsolidado';
                $output = '/var/www/html/resources/reports/' . $hora_actual . 'EstadoCuentaClienteConsolidado';

                $nombre_empresa = Ajustes::where('id_ajuste',4)->select('valor')->first();
                $logo_empresa = Ajustes::where('id_ajuste',3)->select(DB::raw("substr(valor, 2, length(valor) - 2)::json->>'file_thumbnail' as file_thumbnail"))->first();
                $url = '/var/www/html/resources/reports/';

                $options = [
                    'format' => [$request->extension],
                    'locale' => 'en',
                    'params' => [
                        'fechaCorte' => $request->fechaCorte,
                         'empresa_nombre' => $nombre_empresa->valor,
                         'empresa_logo' =>  env('APP_URL_REPORTS').$logo_empresa->file_thumbnail
                    ],
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
                $headers = [
                    'Content-Type' => 'application/pdf',
                ];

                return response()->download($output . '.' . $request->extension, $hora_actual . 'EstadoCuentaClienteConsolidado.' . $request->extension, $headers);

            }
            else {
                return response()->json([
                    'status' => 'error',
                    'result' => $validator->messages(),
                    'messages' => null
                ]);
            }
            /* exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
                print_r($output);*/
        }else{
            return '';
        }
    }




    public function generarReporteEstadoCuentaDetalladoEmpleado(Request $request)
    {
        // echo $ext;
        $rules = [
            'fechaCorte' => 'required|date',
            'fechaInicial' => 'required|date',
            'id_trabajador' => 'required|integer|min:1|exists:pgsql.rrhh.trabajadores,id_trabajador',
            'extension' => 'required|string|max:4'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {

            $os = array("xls", "pdf","html");
            //echo gethostname();
            if (in_array($request->extension, $os)) {

                $hora_actual = time();
                //$input = '/Applications/XAMPP/xamppfiles/htdocs/resources/reports/EstadoCuentaEmpleadoDetalle';
                //$output = '/Applications/XAMPP/xamppfiles/htdocs/resources/reports/'.$hora_actual.'EstadoCuentaEmpleadoDetalle';
                $input = '/var/www/html/resources/reports/EstadoCuentaEmpleadoDetalle';
                $output = '/var/www/html/resources/reports/' . $hora_actual . 'EstadoCuentaEmpleadoDetalle';

                $nombre_empresa = Ajustes::where('id_ajuste',4)->select('valor')->first();
                $logo_empresa = Ajustes::where('id_ajuste',3)->select(DB::raw("substr(valor, 2, length(valor) - 2)::json->>'file_thumbnail' as file_thumbnail"))->first();
                $url = '/var/www/html/resources/reports/';


                $options = [
                    'format' => [$request->extension],
                    'locale' => 'en',
                    'params' => [
                        'fechaCorte' => $request->fechaCorte,
                        'id_trabajador' => $request->id_trabajador,
                        'fechaInicial' => $request->fechaInicial,
                        'directorioReports'=>$url,
                         'empresa_nombre' => $nombre_empresa->valor,
                         'empresa_logo' =>  env('APP_URL_REPORTS').$logo_empresa->file_thumbnail
                    ],
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

                $headers = [
                    'Content-Type' => 'application/pdf',
                ];

                if($request->extension == 'html'){
                    $headers = [
                        'Content-Type' => 'text/html',
                    ];
                }

                /*exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
                print_r($output);*/

                return response()->download($output . '.' . $request->extension, $hora_actual . 'EstadoCuentaEmpleadoDetalle.' . $request->extension, $headers);

            }
            else {
                return response()->json([
                    'status' => 'error',
                    'result' => $validator->messages(),
                    'messages' => null
                ]);
            }

        }else{
            return '';
        }
    }




    public function generarReporteEstadoCuentaConsolidadoEmpleado(Request $request)
    {
        // echo $ext;
        $rules = [
            'fechaCorte' => 'required|date',
            'extension' => 'required|string|max:3'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {

            $os = array("xls", "pdf");
            //echo gethostname();
            if (in_array($request->extension, $os)) {

                $hora_actual = time();
                //$input = '/Applications/XAMPP/xamppfiles/htdocs/resources/reports/EstadoCuentaEmpleadoConsolidado';
                //$output = '/Applications/XAMPP/xamppfiles/htdocs/resources/reports/'.$hora_actual.'EstadoCuentaEmpleadoConsolidado';
                $input = '/var/www/html/resources/reports/EstadoCuentaEmpleadoConsolidado';
                $output = '/var/www/html/resources/reports/' . $hora_actual . 'EstadoCuentaEmpleadoConsolidado';

                $nombre_empresa = Ajustes::where('id_ajuste',4)->select('valor')->first();
                $logo_empresa = Ajustes::where('id_ajuste',3)->select(DB::raw("substr(valor, 2, length(valor) - 2)::json->>'file_thumbnail' as file_thumbnail"))->first();
                $url = '/var/www/html/resources/reports/';

                $options = [
                    'format' => [$request->extension],
                    'locale' => 'en',
                    'params' => [
                        'fechaCorte' => $request->fechaCorte,
                        'empresa_nombre' => $nombre_empresa->valor,
                        'empresa_logo' =>  env('APP_URL_REPORTS').$logo_empresa->file_thumbnail
                    ],
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
                $headers = [
                    'Content-Type' => 'application/pdf',
                ];

                return response()->download($output . '.' . $request->extension, $hora_actual . 'EstadoCuentaEmpleadoConsolidado.' . $request->extension, $headers);

            }
            else {
                return response()->json([
                    'status' => 'error',
                    'result' => $validator->messages(),
                    'messages' => null
                ]);
            }
            /* exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
                print_r($output);*/
        }else{
            return '';
        }
    }




    public function generarReporteEstadoCuentadetalladoOCC(Request $request)
    {
        // echo $ext;
        $rules = [
            'fechaCorte' => 'required|date',
            'fechaInicial' => 'required|date',
            'id_cliente' => 'required|integer|min:1|exists:pgsql.venta.clientes,id_cliente',
            'extension' => 'required|string|max:4'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {

            $os = array("xls", "pdf","html");
            //echo gethostname();
            if (in_array($request->extension, $os)) {

                $hora_actual = time();
                //$input = '/Applications/XAMPP/xamppfiles/htdocs/resources/reports/EstadoCuentaClienteDetalleOtras';
                //$output = '/Applications/XAMPP/xamppfiles/htdocs/resources/reports/'.$hora_actual.'EstadoCuentaClienteDetalleOtras';
                $input = '/var/www/html/resources/reports/EstadoCuentaClienteDetalleOtras';
                $output = '/var/www/html/resources/reports/'.$hora_actual.'EstadoCuentaClienteDetalleOtras';

                $nombre_empresa = Ajustes::where('id_ajuste',4)->select('valor')->first();
                $logo_empresa = Ajustes::where('id_ajuste',3)->select(DB::raw("substr(valor, 2, length(valor) - 2)::json->>'file_thumbnail' as file_thumbnail"))->first();
                $url = '/var/www/html/resources/reports/';

                $options = [
                    'format' => [$request->extension],
                    'locale' => 'en',
                    'params' => [
                        'fechaCorte' => $request->fechaCorte,
                        'id_cliente' => $request->id_cliente,
                        'fechaInicial' => $request->fechaInicial,
                        'directorioReports'=>$url,
                        'empresa_nombre' => $nombre_empresa->valor,
                        'empresa_logo' =>  env('APP_URL_REPORTS').$logo_empresa->file_thumbnail
                    ],
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

                $headers = [
                    'Content-Type' => 'application/pdf',
                ];

                if($request->extension == 'html'){
                    $headers = [
                        'Content-Type' => 'text/html',
                    ];
                }

                /*exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
                print_r($output);*/

                return response()->download($output . '.' . $request->extension, $hora_actual . 'EstadoCuentaClienteDetalleOtras.' . $request->extension, $headers);

            }
            else {
                return response()->json([
                    'status' => 'error',
                    'result' => $validator->messages(),
                    'messages' => null
                ]);
            }

        }else{
            return '';
        }
    }


    public function generarReporteEstadoCuentaConsolidadoOCC(Request $request)
    {
        // echo $ext;
        $rules = [
            'fechaCorte' => 'required|date',
            'extension' => 'required|string|max:3'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {

            $os = array("xls", "pdf");
            //echo gethostname();
            if (in_array($request->extension, $os)) {

                $hora_actual = time();
                //$input = '/Applications/XAMPP/xamppfiles/htdocs/resources/reports/EstadoCuentaClienteConsolidadoOtras';
                //$output = '/Applications/XAMPP/xamppfiles/htdocs/resources/reports/'.$hora_actual.'EstadoCuentaClienteConsolidadoOtras';
                $input = '/var/www/html/resources/reports/EstadoCuentaClienteConsolidadoOtras';
                $output = '/var/www/html/resources/reports/'.$hora_actual .'EstadoCuentaClienteConsolidadoOtras';

                $nombre_empresa = Ajustes::where('id_ajuste',4)->select('valor')->first();
                $logo_empresa = Ajustes::where('id_ajuste',3)->select(DB::raw("substr(valor, 2, length(valor) - 2)::json->>'file_thumbnail' as file_thumbnail"))->first();
                $url = '/var/www/html/resources/reports/';

                $options = [
                    'format' => [$request->extension],
                    'locale' => 'en',
                    'params' => [
                        'fechaCorte' => $request->fechaCorte,
                        'empresa_nombre' => $nombre_empresa->valor,
                        'empresa_logo' =>  env('APP_URL_REPORTS').$logo_empresa->file_thumbnail
                    ],
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
                $headers = [
                    'Content-Type' => 'application/pdf',
                ];

                return response()->download($output . '.' . $request->extension, $hora_actual . 'EstadoCuentaClienteConsolidadoOtras.' . $request->extension, $headers);

            }
            else {
                return response()->json([
                    'status' => 'error',
                    'result' => $validator->messages(),
                    'messages' => null
                ]);
            }
            /* exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
                print_r($output);*/
        }else{
            return '';
        }
    }

    public function reporteRecibos($ext, $id_recibo)
    {
        // echo $ext;
        //$ext = 'pdf';
        $os = array("pdf");
        if (in_array($ext, $os, true)) {
            $hora_actual = time();
            // Rutas para descarga Reportes local
            $input = env('APP_URL_REPORTES') . 'ReciboCaja';
            $output = env('APP_URL_REPORTES') . $hora_actual . 'ReciboCaja';

            // Rutas de descarga de Reportes en servidor
//            $input = env('APP_URL_REPORTES') . 'InventarioFacturasProducto';
//            $output = env('APP_URL_REPORTES') . $hora_actual .  'InventarioFacturasProducto';

            //Ajustes generales del sistema

            $logo_empresa = Ajustes::where('id_ajuste', 3)->select('valor')->first();
            $nombre_empresa = Ajustes::where('id_ajuste', 4)->select('valor')->first();

            $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
            $options = [
                'format' => [$ext],
                'locale' => 'es',
                'params' => [
                    'id_recibo' => $id_recibo,
                    /*'logo_empresa' => env('APP_URL_IMAGES') . $logo_empresa->valor,
                    'nombre_empresa' => $nombre_empresa->valor,*/
                    'id_empresa' => $usuario_empresa->id_empresa,
                ],
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


            //print_r( env('APP_URL_REPORTS').$logo_empresa->valor);
            $headers = [
                'Content-Type' => 'application/pdf',
            ];

            return response()->download($output . '.' . $ext, $hora_actual . 'ReporteRecibo.' . $ext, $headers);

            /*            exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
                        print_r($output);*/
        } else {
            return '';
        }
    }

    public function importar_datos(Request $request)
    {
        $messages = [
            'file.required' => 'Debe seleccionar un archivo válido.',
            //'id_cuenta_bancaria.integer' => 'Debe seleccionar una cuenta bancaria válida.',
            //'id_cuenta_bancaria.required' => 'Debe seleccionar una cuenta bancaria válida.',
        ];
        $rules = [
            'file'  => 'required|mimes:xls,xlsx',
            //'id_cuenta_bancaria' => 'required|integer|exists:pgsql.contabilidad.cuentas_bancarias,id_cuenta_bancaria',
            //'nombre_cuenta' => 'string|max:100',
        ];

        $validator = Validator::make($request->all(), $rules,$messages);
        if (!$validator->fails()) {
            $path = $request->file('file')->getRealPath();

            $data = Excel::load($path)->get();
            $total_registros=0;
            $numero_importado=0;
            $numero_fallido=0;

            $secuencia = CuentasXCobrarCuentasXCobrarImportData::select([DB::raw("COALESCE(max(cod_import),0)+1 as secuencia")])->first();
            $cod_import = $secuencia['secuencia'];
            //print_r($data->toArray());
            $numero_hojax= 1;
            if($data->count() > 0)
            {
                foreach ($data->toArray() as $hoja => $datosHoja)
                {

                        //Validar que los no se importen vacíos
                        if(!empty($datosHoja['no_documento'])
                            && (!empty($datosHoja['tipo_documento']))
                            && (!empty($datosHoja['debe'])) || (float)$datosHoja['debe']>=0
                            && (!empty($datosHoja['haber'])) || (float)$datosHoja['haber']>=0
                            && (!empty($datosHoja['fecha_movimiento'])))
                        {
                            //validar que el debe y haber no tenga valor null

                            $debe = $datosHoja['debe']?$datosHoja['debe']:0;
                            $haber = $datosHoja['haber']?$datosHoja['haber']:0;

                            //Asignar id_tipo_documento según las siglas importadas en el excel
                            if(!empty(trim($datosHoja['tipo_documento'])))
                            {
                                $factura = array("FACT");
                                if(in_array(trim($datosHoja['tipo_documento']),$factura))
                                {
                                    $tipo_documento = 1;
                                }
                                $recibo = array("ROC");
                                if(in_array(trim($datosHoja['tipo_documento']),$recibo))
                                {
                                    $tipo_documento = 2;
                                }
                                $nota_credito = array("NC","NCRE");
                                if(in_array(trim($datosHoja['tipo_documento']),$nota_credito))
                                {
                                    $tipo_documento = 3;
                                }
                                $nota_debito = array("ND","NDEB");
                                if(in_array(trim($datosHoja['tipo_documento']),$nota_debito))
                                {
                                    $tipo_documento = 4;
                                }

                                //Guardar en saldo actual el valor de la factura

                                $factura = array("FACT");
                                if(in_array(trim($datosHoja['tipo_documento']),$factura))
                                {
                                    $saldo_actual = $datosHoja['debe'];
                                }else{
                                    $saldo_actual = 0;
                                }
                            }

                            //Darle formato a las fechas extraídas del archivo ~ fecha_movimiento
                            $fecha_movimiento=substr(trim($datosHoja['fecha_movimiento']), 0,10);
                            //echo 'lengt '.strlen($fecha_movimiento);
                            if(strlen($fecha_movimiento)===10){
                                if (strpos($fecha_movimiento, '-') !== false) {

                                    $dia = substr($fecha_movimiento, 0,2);
                                    $mes = substr($fecha_movimiento, 3,2);
                                    $anio = substr($fecha_movimiento, 6,4);


                                    if(substr($fecha_movimiento, 2, 1)==='-'){
                                        // echo $fecha_movimiento;
                                        $dia = substr($fecha_movimiento, 0,2);
                                        $mes = substr($fecha_movimiento, 3,2);
                                        $anio = substr($fecha_movimiento, 6,4);

                                    }

                                    if(substr($fecha_movimiento, 1, 1)==='-'){
                                        // echo $fecha_movimiento;
                                        $dia = substr($fecha_movimiento, 0,1);
                                        $mes = substr($fecha_movimiento, 2,1);
                                        $anio = substr($fecha_movimiento, 4,4);

                                    }

                                    if(substr($fecha_movimiento, 4, 1)=='-'){
                                        //echo $fecha_movimiento.' ';
                                        $anio = substr($fecha_movimiento, 0,4);
                                        $mes = substr($fecha_movimiento, 5,2);
                                        $dia= substr($fecha_movimiento, 8,2);
                                        //echo $mes.'/'.$dia.'/'.$anio . '  ';
                                    }

                                    /*echo ' fechaorg: '.$fecha_movimiento;
                                    echo 'dia: '.$dia;
                                    echo 'mes: '.$mes;
                                    echo 'ani: '.$anio;
        */
                                    $fecha_movimiento=$dia.'/'.$mes.'/'.$anio;
                                    //echo ' fechax '.$fecha_movimiento2;
                                }else {
                                    //echo $fecha_movimiento.' | '.$value['descripcion'];
                                    //echo substr(trim($value['fecha']), 3, 1) . ' divisor';
                                    //echo $fecha_movimiento.' ';
                                    //if (trim($value['descripcion']) == '35750789') {
                                    $dia = substr(trim($datosHoja['fecha_movimiento']), 0,2);
                                    $mes = substr(trim($datosHoja['fecha_movimiento']), 3,2);
                                    $anio = substr(trim($datosHoja['fecha_movimiento']), 6,4);
                                    $fecha_movimiento=$dia.'/'.$mes.'/'.$anio;
                                    //}
                                }
                            }elseif(strlen($fecha_movimiento)===8){
                                $mes = substr($fecha_movimiento, 0,2);
                                $dia = substr($fecha_movimiento, 3,2);
                                $anio = substr($fecha_movimiento, 6,4);
                                //echo $fecha_movimiento. ' ';
                                $fecha_movimiento=$dia.'/'.$mes.'/'.(((int)$anio)+2000);
                                //echo $fecha_movimiento. ' ';
                            }

                            $insert_data[] = array(
                                'no_documento' => $datosHoja['no_documento'],
                                'tipo_documento' => $datosHoja['tipo_documento'],
                                'id_tipo_documento' => $tipo_documento,
                                'identificador_origen' => 0,
                                'debe' => $debe?$debe:0,
                                'haber' => $haber?$haber:0,
                                'saldo_actual' => $saldo_actual?$saldo_actual:0,
                                'fecha_movimiento' => $fecha_movimiento,
                                'fecha_vencimiento' => $datosHoja['fecha_vencimiento'],
                                'codigo_cliente' => $datosHoja['codigo_cliente'],
                                'descripcion_movimiento' => '-',
                                'usuario_registra' => Auth::user()->usuario,
                                'es_registro_importado' => true,
                                'estado' => 1, //estados: 1- registrado | 2: importado (proceso de importación a la tabla principal)
                                'id_trabajador' => '',
                                'cod_import' => $cod_import
                            );
                            $numero_importado++;
                        }else
                            {
                                $numero_fallido++;
                            }
                        $total_registros++;

                    $numero_hojax++;
                }
                if(!empty($insert_data))
                {
                    //DB::table('cuentasxcobrar.cuentasxcobrar_import_data')->insert($insert_data);
                    $response = response()->json([
                        'status' => 'success',
                        'result' =>[
                            'datos'=> $insert_data,
                            'numero_hojas'=>$numero_hojax-1,
                            'numero_importado'=>$numero_importado,
                            'numero_fallido'=>$numero_fallido,
                            'total_registros'=>$total_registros
                        ],
                        'messages' => null
                    ]);
                }else{
                    $response = response()->json([
                        'status' => 'error',
                        'result' => 'Los datos del archivo no son validos',
                        'messages' => null
                    ]);
                }
            }else{
                $response = response()->json([
                    'status' => 'error',
                    'result' => 'El archivo no contiene datos validos',
                    'messages' => null
                ]);
            }
        }else {
            $response =  response()->json([
                'status' => 'error',
                'result' => $validator->messages(),
                'messages' => null
            ]);
        }
        return $response;
    }



}
