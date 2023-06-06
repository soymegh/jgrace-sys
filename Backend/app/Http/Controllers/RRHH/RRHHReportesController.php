<?php 

namespace App\Http\Controllers;

use App\Models\AdmonAjustes;
use Hash, Validator,Auth;
use App\Models\InventarioKardex;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PHPJasper\PHPJasper;

class RRHHReportesController extends Controller
{


    public function ReportePersonalSucursal(Request $request)
    {
        // echo $ext;
        $rules = [
            'extension' => 'required|string|max:3'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $os = array("pdf", "xls");
            if (in_array($request->extension, $os)) {
                $hora_actual = time();
                //$input = 'C:/xampp/htdocs/resources/reports/ReportePersonalPorArea';
                //$output = 'C:/xampp/htdocs/resources/reports/' . $hora_actual . 'ReportePersonalPorArea';
                $input = '/var/www/html/resources/reports/ReportePersonalPorArea';
                $output = '/var/www/html/resources/reports/' . $hora_actual . 'ReportePersonalPorArea';
                $nombre_empresa = AdmonAjustes::where('id_ajuste',4)->select('valor')->first();
                $logo_empresa = AdmonAjustes::where('id_ajuste',3)->select(DB::raw("substr(valor, 2, length(valor) - 2)::json->>'file_thumbnail' as file_thumbnail"))->first();
                $url = '/var/www/html/resources/reports/';

                $options = [
                    'format' => [$request->extension],
                    'locale' => 'en',
                    'params' => [
                        'fechaInicio' => $request->fechaInicio,
                        'fechaFinal' => $request->fechaFinal,
                        'id_sucursal' => $request->id_sucursal,
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

                /*exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
              print_r($output);*/

                $headers = [
                    'Content-Type' => 'application/pdf',
                ];

                return response()->download($output . '.' . $request->extension, $hora_actual . 'ReportePersonalPorArea.' . $request->extension, $headers);
            } else {
                return response()->json([
                    'status' => 'error',
                    'result' => $validator->messages(),
                    'messages' => null
                ]);
            }
        } else {
            return '';
        }
    }


    public function reporteInventarioExistencias(Request $request)
    {
        $rules = [
            'extension' => 'required|string|max:3'
        ];
        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $os = array("pdf", "xls");
            if (in_array($request->extension, $os)) {
                $hora_actual = time();
                $input = 'C:/xampp/htdocs/resources/reports/ProductoExistencia';
                $output = 'C:/xampp/htdocs/resources/reports/' . $hora_actual . 'ProductoExistencia';
                //$input = '/var/www/html/resources/reports/ProductoExistencia';
                //$output = '/var/www/html/resources/reports/'.$hora_actual.'ProductoExistencia';
                //$input = '/Applications/XAMPP/xamppfiles/htdocs/resources/reports/ProductoExistencia';
                //$output = '/Applications/XAMPP/xamppfiles/htdocs/resources/reports/' .$hora_actual . 'ProductoExistencia';
                $nombre_empresa = AdmonAjustes::where('id_ajuste',4)->select('valor')->first();
                $logo_empresa = AdmonAjustes::where('id_ajuste',3)->select(DB::raw("substr(valor, 2, length(valor) - 2)::json->>'file_thumbnail' as file_thumbnail"))->first();
                $url = '/var/www/html/resources/reports/';

                $options = [
                    'format' => [$request->extension],
                    'locale' => 'en',
                    'params' => [
                        'id_bodega' => $request->id_bodega,
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

              /*  exec($jasper->process($input, $output, $options)->output() . ' 2>&1', $output);
                print_r($output);*/

                $headers = [
                    'Content-Type' => 'application/pdf',
                ];

                return response()->download($output . '.' . $request->extension, $hora_actual . 'FormatoExistencia.' . $request->extension, $headers);

            } else {
                return response()->json([
                    'status' => 'error',
                    'result' => $validator->messages(),
                    'messages' => null
                ]);
            }
           }    else {
                    return '';
                      }
        }

    public function reporteRequisasBodegas(Request $request)
    {
        $rules = [
            'extension' => 'required|string|max:3',
            'id_bodega' => 'required|integer',
            'fechaInicio' => 'required|date',
            'fechaFinal' => 'required|date',
        ];
        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $os = array("pdf", "xls");
            if (in_array($request->extension, $os)) {
                $hora_actual = time();
                //$input = 'C:/xampp/htdocs/resources/reports/ReporteRequisas';
                //$output = 'C:/xampp/htdocs/resources/reports/' . $hora_actual . 'ReporteRequisas';
                $input = '/var/www/html/resources/reports/ReporteRequisas';
                $output = '/var/www/html/resources/reports/'.$hora_actual.'ReporteRequisas';
                $nombre_empresa = AdmonAjustes::where('id_ajuste',4)->select('valor')->first();
                $logo_empresa = AdmonAjustes::where('id_ajuste',3)->select(DB::raw("substr(valor, 2, length(valor) - 2)::json->>'file_thumbnail' as file_thumbnail"))->first();
                $url = '/var/www/html/resources/reports/';

                $options = [
                    'format' => [$request->extension],
                    'locale' => 'en',
                    'params' => [
                        'fechaInicio' => $request->fechaInicio,
                        'fechaFinal' => $request->fechaFinal,
                        'id_bodega' => $request->id_bodega,
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

                /*  exec($jasper->process($input, $output, $options)->output() . ' 2>&1', $output);
                  print_r($output);*/

                $headers = [
                    'Content-Type' => 'application/pdf',
                ];

                return response()->download($output . '.' . $request->extension, $hora_actual . 'ReporteRequisas.' . $request->extension, $headers);

            } else {
                return response()->json([
                    'status' => 'error',
                    'result' => $validator->messages(),
                    'messages' => null
                ]);
            }
        }    else {
            return '';
        }
    }
    public function reporteINSS(Request $request)
    {
        $rules = [
            'extension' => 'required|string|max:3',
            'mes' => 'required|integer',
        ];
        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $os = array("pdf", "xls");
            if (in_array($request->extension, $os)) {
                $hora_actual = time();
                //$input = 'C:/xampp/htdocs/resources/reports/ReporteINSS';
                //$output = 'C:/xampp/htdocs/resources/reports/' . $hora_actual . 'ReporteINSS';
                $input = '/var/www/html/resources/reports/ReporteINSS';
                $output = '/var/www/html/resources/reports/'.$hora_actual.'ReporteINSS';
                $nombre_empresa = AdmonAjustes::where('id_ajuste',4)->select('valor')->first();
                $logo_empresa = AdmonAjustes::where('id_ajuste',3)->select(DB::raw("substr(valor, 2, length(valor) - 2)::json->>'file_thumbnail' as file_thumbnail"))->first();
                $url = '/var/www/html/resources/reports/';

                $options = [
                    'format' => [$request->extension],
                    'locale' => 'en',
                    'params' => [
                        'anio' => $request->anio,
                        'mes' => $request->mes,
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

                /*  exec($jasper->process($input, $output, $options)->output() . ' 2>&1', $output);
                  print_r($output);*/

                $headers = [
                    'Content-Type' => 'application/pdf',
                ];

                return response()->download($output . '.' . $request->extension, $hora_actual . 'ReporteINSS.' . $request->extension, $headers);

            } else {
                return response()->json([
                    'status' => 'error',
                    'result' => $validator->messages(),
                    'messages' => null
                ]);
            }
        }    else {
            return '';
        }
    }
    public function reporteIR(Request $request)
    {
        $rules = [
            'extension' => 'required|string|max:3',
            'mes' => 'required|integer',
        ];
        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $os = array("pdf", "xls");
            if (in_array($request->extension, $os)) {
                $hora_actual = time();
                //$input = 'C:/xampp/htdocs/resources/reports/ReporteIR';
                //$output = 'C:/xampp/htdocs/resources/reports/' . $hora_actual . 'ReporteIR';
                $input = '/var/www/html/resources/reports/ReporteIR';
                $output = '/var/www/html/resources/reports/'.$hora_actual.'ReporteIR';
                $nombre_empresa = AdmonAjustes::where('id_ajuste',4)->select('valor')->first();
                $logo_empresa = AdmonAjustes::where('id_ajuste',3)->select(DB::raw("substr(valor, 2, length(valor) - 2)::json->>'file_thumbnail' as file_thumbnail"))->first();
                $url = '/var/www/html/resources/reports/';

                $options = [
                    'format' => [$request->extension],
                    'locale' => 'en',
                    'params' => [
                        'anio' => $request->anio,
                        'mes' => $request->mes,
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

                /*  exec($jasper->process($input, $output, $options)->output() . ' 2>&1', $output);
                  print_r($output);*/

                $headers = [
                    'Content-Type' => 'application/pdf',
                ];

                return response()->download($output . '.' . $request->extension, $hora_actual . 'ReporteIR.' . $request->extension, $headers);

            } else {
                return response()->json([
                    'status' => 'error',
                    'result' => $validator->messages(),
                    'messages' => null
                ]);
            }
        }    else {
            return '';
        }
    }
    public function reporteEntradaSalidaPersonal(Request $request)
    {
        $rules = [
            'extension' => 'required|string|max:3',
            'fecha_inicio' => 'required|date',
            'fecha_final' => 'required|date',
        ];
        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $os = array("pdf", "xls");
            if (in_array($request->extension, $os)) {
                $hora_actual = time();
                //$input = 'C:/xampp/htdocs/resources/reports/ReporteEntradaSalidaPersonal';
                //$output = 'C:/xampp/htdocs/resources/reports/' . $hora_actual . 'ReporteEntradaSalidaPersonal';
                $input = '/var/www/html/resources/reports/ReporteEntradaSalidaPersonal';
                $output = '/var/www/html/resources/reports/'.$hora_actual.'ReporteEntradaSalidaPersonal';
                $nombre_empresa = AdmonAjustes::where('id_ajuste',4)->select('valor')->first();
                $logo_empresa = AdmonAjustes::where('id_ajuste',3)->select(DB::raw("substr(valor, 2, length(valor) - 2)::json->>'file_thumbnail' as file_thumbnail"))->first();
                $url = '/var/www/html/resources/reports/';

                $options = [
                    'format' => [$request->extension],
                    'locale' => 'en',
                    'params' => [
                        'fecha_inicio' => $request->fecha_inicio,
                        'fecha_final' => $request->fecha_final,
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

                  /*exec($jasper->process($input, $output, $options)->output() . ' 2>&1', $output);
                  print_r($output);*/

                $headers = [
                    'Content-Type' => 'application/pdf',
                ];

                return response()->download($output . '.' . $request->extension, $hora_actual . 'ReporteControlEmpleado.' . $request->extension, $headers);

            } else {
                return response()->json([
                    'status' => 'error',
                    'result' => $validator->messages(),
                    'messages' => null
                ]);
            }
        }    else {
            return '';
        }
    }

    public function reporteConstanciaRetencion(Request $request)
    {
        $rules = [
            'extension' => 'required|string|max:3',
            'id_trabajador' => 'required|integer',
        ];
        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $os = array("pdf", "xls");
            if (in_array($request->extension, $os)) {
                $hora_actual = time();
                //$input = 'C:/xampp/htdocs/resources/reports/ConstanciaRetencion';
                //$output = 'C:/xampp/htdocs/resources/reports/' . $hora_actual . 'ConstanciaRetencion';
                $input = '/var/www/html/resources/reports/ConstanciaRetencion';
                $output = '/var/www/html/resources/reports/'.$hora_actual.'ConstanciaRetencion';
                $nombre_empresa = AdmonAjustes::where('id_ajuste',4)->select('valor')->first();
                $logo_empresa = AdmonAjustes::where('id_ajuste',3)->select(DB::raw("substr(valor, 2, length(valor) - 2)::json->>'file_thumbnail' as file_thumbnail"))->first();
                $url = '/var/www/html/resources/reports/';

                $options = [
                    'format' => [$request->extension],
                    'locale' => 'en',
                    'params' => [
                        'id_trabajador' => $request->id_trabajador,
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

                /*exec($jasper->process($input, $output, $options)->output() . ' 2>&1', $output);
                print_r($output);*/

                $headers = [
                    'Content-Type' => 'application/pdf',
                ];

                return response()->download($output . '.' . $request->extension, $hora_actual . 'ConstanciaRetencion.' . $request->extension, $headers);

            } else {
                return response()->json([
                    'status' => 'error',
                    'result' => $validator->messages(),
                    'messages' => null
                ]);
            }
        }    else {
            return '';
        }
    }

    public function reporteColillaPago(Request $request)
    {
        $rules = [
            'extension' => 'required|string|max:3',
            'id_trabajador' => 'required|integer',
        ];
        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $os = array("pdf", "xls");
            if (in_array($request->extension, $os)) {
                $hora_actual = time();
                //$input = 'C:/xampp/htdocs/resources/reports/ColillaPago';
                //$output = 'C:/xampp/htdocs/resources/reports/' . $hora_actual . 'ColillaPago';
                $input = '/var/www/html/resources/reports/ColillaPago';
                $output = '/var/www/html/resources/reports/'.$hora_actual.'ColillaPago';
                $nombre_empresa = AdmonAjustes::where('id_ajuste',4)->select('valor')->first();
                $logo_empresa = AdmonAjustes::where('id_ajuste',3)->select(DB::raw("substr(valor, 2, length(valor) - 2)::json->>'file_thumbnail' as file_thumbnail"))->first();
                $url = '/var/www/html/resources/reports/';

                $options = [
                    'format' => [$request->extension],
                    'locale' => 'en',
                    'params' => [
                        'id_trabajador' => $request->id_trabajador,
                        'anio' => $request->anio,
                        'mes' => $request->mes,
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

                /*exec($jasper->process($input, $output, $options)->output() . ' 2>&1', $output);
                print_r($output);*/

                $headers = [
                    'Content-Type' => 'application/pdf',
                ];

                return response()->download($output . '.' . $request->extension, $hora_actual . 'ColillaPago.' . $request->extension, $headers);

            } else {
                return response()->json([
                    'status' => 'error',
                    'result' => $validator->messages(),
                    'messages' => null
                ]);
            }
        }    else {
            return '';
        }
    }
    public function reporteAntiguedad(Request $request)
    {
        $rules = [
            'extension' => 'required|string|max:3',
            'id_trabajador' => 'required|integer',
        ];
        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $os = array("pdf", "xls");
            if (in_array($request->extension, $os)) {
                $hora_actual = time();
                //$input = 'C:/xampp/htdocs/resources/reports/ReporteIndemnizacion';
                //$output = 'C:/xampp/htdocs/resources/reports/' . $hora_actual . 'ReporteIndemnizacion';
                $input = '/var/www/html/resources/reports/ReporteIndemnizacion';
                $output = '/var/www/html/resources/reports/'.$hora_actual.'ReporteIndemnizacion';
                $nombre_empresa = AdmonAjustes::where('id_ajuste',4)->select('valor')->first();
                $logo_empresa = AdmonAjustes::where('id_ajuste',3)->select(DB::raw("substr(valor, 2, length(valor) - 2)::json->>'file_thumbnail' as file_thumbnail"))->first();
                $url = '/var/www/html/resources/reports/';

                $options = [
                    'format' => [$request->extension],
                    'locale' => 'en',
                    'params' => [
                        'id_trabajador' => $request->id_trabajador,
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

                /*exec($jasper->process($input, $output, $options)->output() . ' 2>&1', $output);
                print_r($output);*/

                $headers = [
                    'Content-Type' => 'application/pdf',
                ];

                return response()->download($output . '.' . $request->extension, $hora_actual . 'ReporteIndemnizacion.' . $request->extension, $headers);

            } else {
                return response()->json([
                    'status' => 'error',
                    'result' => $validator->messages(),
                    'messages' => null
                ]);
            }
        }    else {
            return '';
        }
    }
    public function reporteAntiguedadProyectada(Request $request)
    {
        $rules = [
            'extension' => 'required|string|max:3',
            'id_trabajador' => 'required|integer',
            'fecha_corte' => 'required|date'
        ];
        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $os = array("pdf", "xls");
            if (in_array($request->extension, $os)) {
                $hora_actual = time();
                //$input = 'C:/xampp/htdocs/resources/reports/ReporteIndemnizacionProyectada';
                //$output = 'C:/xampp/htdocs/resources/reports/' . $hora_actual . 'ReporteIndemnizacionProyectada';
                $input = '/var/www/html/resources/reports/ReporteIndemnizacionProyectada';
                $output = '/var/www/html/resources/reports/'.$hora_actual.'ReporteIndemnizacionProyectada';
                $nombre_empresa = AdmonAjustes::where('id_ajuste',4)->select('valor')->first();
                $logo_empresa = AdmonAjustes::where('id_ajuste',3)->select(DB::raw("substr(valor, 2, length(valor) - 2)::json->>'file_thumbnail' as file_thumbnail"))->first();
                $url = '/var/www/html/resources/reports/';

                $options = [
                    'format' => [$request->extension],
                    'locale' => 'en',
                    'params' => [
                        'id_trabajador' => $request->id_trabajador,
                        'fecha_corte' => $request->fecha_corte,
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

                /*exec($jasper->process($input, $output, $options)->output() . ' 2>&1', $output);
                print_r($output);*/

                $headers = [
                    'Content-Type' => 'application/pdf',
                ];

                return response()->download($output . '.' . $request->extension, $hora_actual . 'ReporteIndemnizacion.' . $request->extension, $headers);

            } else {
                return response()->json([
                    'status' => 'error',
                    'result' => $validator->messages(),
                    'messages' => null
                ]);
            }
        }    else {
            return '';
        }
    }

    public function reporteAguinaldo(Request $request)
    {
        $rules = [
            'extension' => 'required|string|max:3',
            'id_trabajador' => 'required|integer',
        ];
        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $os = array("pdf", "xls");
            if (in_array($request->extension, $os)) {
                $hora_actual = time();
                //$input = 'C:/xampp/htdocs/resources/reports/ReporteDetalleAguinaldo';
                //$output = 'C:/xampp/htdocs/resources/reports/' . $hora_actual . 'ReporteDetalleAguinaldo';
                $input = '/var/www/html/resources/reports/ReporteDetalleAguinaldo';
                $output = '/var/www/html/resources/reports/'.$hora_actual.'ReporteDetalleAguinaldo';
                $nombre_empresa = AdmonAjustes::where('id_ajuste',4)->select('valor')->first();
                $logo_empresa = AdmonAjustes::where('id_ajuste',3)->select(DB::raw("substr(valor, 2, length(valor) - 2)::json->>'file_thumbnail' as file_thumbnail"))->first();
                $url = '/var/www/html/resources/reports/';

                $options = [
                    'format' => [$request->extension],
                    'locale' => 'en',
                    'params' => [
                        'id_trabajador' => $request->id_trabajador,
                        'periodo' => $request->periodo,
                        'fecha_corte' => $request->fecha_corte,
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

                /*exec($jasper->process($input, $output, $options)->output() . ' 2>&1', $output);
                print_r($output);*/

                $headers = [
                    'Content-Type' => 'application/pdf',
                ];

                return response()->download($output . '.' . $request->extension, $hora_actual . 'ColillaPago.' . $request->extension, $headers);

            } else {
                return response()->json([
                    'status' => 'error',
                    'result' => $validator->messages(),
                    'messages' => null
                ]);
            }
        }    else {
            return '';
        }
    }

}



