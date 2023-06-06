<?php 

namespace App\Http\Controllers;


use App\Models\AdmonAjustes;
use App\Models\CajaBancoAfectaciones;
use App\Models\CajaBancoBancos;
use App\Models\CajaBancoFacturaViaPagos;
use App\Models\CajaBancoMonedas;
use App\Models\CajaBancoTasasCambios;
use App\Models\ContabilidadDocumentosContables;
use App\Models\ContabilidadDocumentosMovimientos;
use App\Models\ContabilidadPeriodoFiscal;
use App\Models\ContabilidadPeriodoMeses;
use App\Models\ContabilidadTiposDocumentos;
use App\Models\CuentasXCobrarCuentasXCobrar;
use App\Models\InventarioBodegas;
use App\Models\InventarioConsignacionProductos;
use App\Models\InventarioSalidaProductos;
use App\Models\InventarioSalidas;
use App\Models\PublicDepartamentos;
use App\Models\PublicSucursales;
use App\Models\PublicViaPagos;
use App\Models\PublicZonas;
use App\Models\VentaVendedores;
use DateTime;
use Hash, Validator,Auth;
use App\Models\InventarioBodegaProductos;
use App\Models\CajaBancoFacturaProductos;
use App\Models\CajaBancoFacturas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PHPJasper\PHPJasper;

class CajaBancoReportesController extends Controller
{

    public function generarReporteVentasClienteDetallado(Request $request)
    {
        // echo $ext;
        $rules = [
            'agrupacion' => 'required|string',
            'fechaFinal' => 'required|date',
            'fechaInicial' => 'required|date',
            'id_cliente' => 'required|integer|min:0',
            'id_sucursal' => 'required|integer|min:0',
            'extension' => 'required|string|max:4',
            'tipo_facturax' => 'required|integer'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {

            $repos = array("ReporteVentasPorCliente", "ReporteVentasPorSucursal","ReporteVentasPorVendedor");
            $os = array("xls", "pdf","html");
            //echo gethostname();
            if (in_array($request->extension, $os) && in_array($request->agrupacion, $repos)) {

                $hora_actual = time();
                //$input = '/Applications/XAMPP/xamppfiles/htdocs/resources/reports/ReporteVentasPorCliente';
                //$output = '/Applications/XAMPP/xamppfiles/htdocs/resources/reports/'.$hora_actual.'ReporteVentasPorCliente';

                $input = '/var/www/html/resources/reports/'.$request->agrupacion;
                $output = '/var/www/html/resources/reports/' .$hora_actual .$request->agrupacion;

                //$input = 'C:/xampp7/htdocs/resources/reports/'.$request->agrupacion;
                //$output = 'C:/xampp7/htdocs/resources/reports/' .$hora_actual .$request->agrupacion;

                //$input = 'C:/xampp7/htdocs/resources/reports/ReporteVentasPorSucursal';
                //$output = 'C:/xampp7/htdocs/resources/reports/' .$hora_actual . 'ReporteVentasPorSucursal';

                //$input = 'C:/xampp7/htdocs/resources/reports/ReporteVentasPorVendedor';
                //$output = 'C:/xampp7/htdocs/resources/reports/' .$hora_actual . 'ReporteVentasPorVendedor';


                $nombre_empresa = AdmonAjustes::where('id_ajuste',4)->select('valor')->first();
                $logo_empresa = AdmonAjustes::where('id_ajuste',3)->select(DB::raw("substr(valor, 2, length(valor) - 2)::json->>'file_thumbnail' as file_thumbnail"))->first();
                $url = '/var/www/html/resources/reports/';
                //$url = 'C:/xampp7/htdocs/resources/reports/';

                $options = [
                    'format' => [$request->extension],
                    'locale' => 'en',
                    'params' => [
                        'fechaFinal' => $request->fechaFinal,
                        'id_clientex' => $request->id_cliente,
                        'tipo_facturax' => $request->tipo_facturax,
                        'id_vendedorx' => $request->id_vendedor,
                        'id_bodegax' => 0,
                        'id_sucursalx' =>$request->id_sucursal,
                        'fechaInicial' => $request->fechaInicial,
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

                return response()->download($output . '.' . $request->extension, $hora_actual . $request->agrupacion.'.' . $request->extension, $headers);

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


    public function generarReporteVentasSucursalDetallado(Request $request)
    {
        // echo $ext;
        $rules = [
            'fechaFinal' => 'required|date',
            'fechaInicial' => 'required|date',
            'id_sucursal' => 'required|integer|min:0',
            'extension' => 'required|string|max:4',
            'tipo_facturax' => 'required|integer'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {

            $os = array("xls", "pdf","html");
            //echo gethostname();
            if (in_array($request->extension, $os)) {

                $hora_actual = time();
                //$input = '/Applications/XAMPP/xamppfiles/htdocs/resources/reports/ReporteVentasPorSucursal';
                //$output = '/Applications/XAMPP/xamppfiles/htdocs/resources/reports/'.$hora_actual.'ReporteVentasPorSucursal';

                $input = '/var/www/html/resources/reports/ReporteVentasPorSucursal';
                $output = '/var/www/html/resources/reports/' . $hora_actual . 'ReporteVentasPorSucursal';

                //$input = 'C:/xampp7/htdocs/resources/reports/ReporteVentasPorSucursal';
                //$output = 'C:/xampp7/htdocs/resources/reports/' .$hora_actual . 'ReporteVentasPorSucursal';

                $nombre_empresa = AdmonAjustes::where('id_ajuste',4)->select('valor')->first();
                $logo_empresa = AdmonAjustes::where('id_ajuste',3)->select(DB::raw("substr(valor, 2, length(valor) - 2)::json->>'file_thumbnail' as file_thumbnail"))->first();
                $url = '/var/www/html/resources/reports/';
                //$url = 'C:/xampp7/htdocs/resources/reports/';

                $options = [
                    'format' => [$request->extension],
                    'locale' => 'en',
                    'params' => [
                        'fechaFinal' => $request->fechaFinal,
                        'id_clientex' => 0,
                        'tipo_facturax' => $request->tipo_facturax,
                        'id_bodegax' => 0,
                        'id_sucursalx' => $request->id_sucursal,
                        'fechaInicial' => $request->fechaInicial,
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

                return response()->download($output . '.' . $request->extension, $hora_actual . 'ReporteVentasPorSucursal.' . $request->extension, $headers);

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

    public function reporteRetenciones(Request $request)
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
                //$input = 'C:/xampp/htdocs/resources/reports/ReporteRetenciones';
                //$output = 'C:/xampp/htdocs/resources/reports/' . $hora_actual . 'ReporteRetenciones';
                $input = '/var/www/html/resources/reports/ReporteRetenciones';
                $output = '/var/www/html/resources/reports/' . $hora_actual . 'ReporteRetenciones';
                $nombre_empresa = AdmonAjustes::where('id_ajuste',4)->select('valor')->first();
                $logo_empresa = AdmonAjustes::where('id_ajuste',3)->select(DB::raw("substr(valor, 2, length(valor) - 2)::json->>'file_thumbnail' as file_thumbnail"))->first();
                $url = '/var/www/html/resources/reports/';

                $options = [
                    'format' => [$request->extension],
                    'locale' => 'en',
                    'params' => [
                        'fechaInicio' => $request->fechaInicio,
                        'fechaFinal' => $request->fechaFinal,
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

                return response()->download($output . '.' . $request->extension, $hora_actual . 'ReporteRetenciones.' . $request->extension, $headers);
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
    public function reporteCheques(Request $request)
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
                //$input = 'C:/xampp/htdocs/resources/reports/ReporteGeneralCheque';
                //$output = 'C:/xampp/htdocs/resources/reports/' . $hora_actual . 'ReporteGeneralCheque';
                $input = '/var/www/html/resources/reports/ReporteGeneralCheque';
                $output = '/var/www/html/resources/reports/' . $hora_actual . 'ReporteGeneralCheque';
                $nombre_empresa = AdmonAjustes::where('id_ajuste',4)->select('valor')->first();
                $logo_empresa = AdmonAjustes::where('id_ajuste',3)->select(DB::raw("substr(valor, 2, length(valor) - 2)::json->>'file_thumbnail' as file_thumbnail"))->first();
                $url = '/var/www/html/resources/reports/';

                $options = [
                    'format' => [$request->extension],
                    'locale' => 'en',
                    'params' => [
                        'fecha_inicio' => $request->fechaInicio,
                        'fecha_final' => $request->fechaFinal,
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

                return response()->download($output . '.' . $request->extension, $hora_actual . 'ReporteGeneralCheque.' . $request->extension, $headers);
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

    public function reporteChequesEstados(Request $request)
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
                //$input = 'C:/xampp/htdocs/resources/reports/ReporteEstadoSolicitudCheque';
                //$output = 'C:/xampp/htdocs/resources/reports/' . $hora_actual . 'ReporteEstadoSolicitudCheque';
                $input = '/var/www/html/resources/reports/ReporteEstadoSolicitudCheque';
                $output = '/var/www/html/resources/reports/' . $hora_actual . 'ReporteEstadoSolicitudCheque';
                $nombre_empresa = AdmonAjustes::where('id_ajuste',4)->select('valor')->first();
                $logo_empresa = AdmonAjustes::where('id_ajuste',3)->select(DB::raw("substr(valor, 2, length(valor) - 2)::json->>'file_thumbnail' as file_thumbnail"))->first();
                $url = '/var/www/html/resources/reports/';

                $options = [
                    'format' => [$request->extension],
                    'locale' => 'en',
                    'params' => [
                        'fecha_inicio' => $request->fechaInicio,
                        'fecha_final' => $request->fechaFinal,
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

                return response()->download($output . '.' . $request->extension, $hora_actual . 'ReporteEstadoSolicitudCheque.' . $request->extension, $headers);
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

    public function reportePagoBeneficiario(Request $request)
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
                //$input = 'C:/xampp/htdocs/resources/reports/ReportePagoBeneficiarios';
                //$output = 'C:/xampp/htdocs/resources/reports/' . $hora_actual . 'ReportePagoBeneficiarios';
                $input = '/var/www/html/resources/reports/ReportePagoBeneficiarios';
                $output = '/var/www/html/resources/reports/' . $hora_actual . 'ReportePagoBeneficiarios';
                $nombre_empresa = AdmonAjustes::where('id_ajuste',4)->select('valor')->first();
                $logo_empresa = AdmonAjustes::where('id_ajuste',3)->select(DB::raw("substr(valor, 2, length(valor) - 2)::json->>'file_thumbnail' as file_thumbnail"))->first();
                $url = '/var/www/html/resources/reports/';

                $options = [
                    'format' => [$request->extension],
                    'locale' => 'en',
                    'params' => [
                        'fecha_inicio' => $request->fechaInicio,
                        'fecha_final' => $request->fechaFinal,
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

                return response()->download($output . '.' . $request->extension, $hora_actual . 'ReportePagoBeneficiarios.' . $request->extension, $headers);
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

    public function reporteChequesAnulados(Request $request)
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
                //$input = 'C:/xampp/htdocs/resources/reports/ReporteSolicitudChequeAnulado';
                //$output = 'C:/xampp/htdocs/resources/reports/' . $hora_actual . 'ReporteSolicitudChequeAnulado';
                $input = '/var/www/html/resources/reports/ReporteSolicitudChequeAnulado';
                $output = '/var/www/html/resources/reports/' . $hora_actual . 'ReporteSolicitudChequeAnulado';
                $nombre_empresa = AdmonAjustes::where('id_ajuste',4)->select('valor')->first();
                $logo_empresa = AdmonAjustes::where('id_ajuste',3)->select(DB::raw("substr(valor, 2, length(valor) - 2)::json->>'file_thumbnail' as file_thumbnail"))->first();
                $url = '/var/www/html/resources/reports/';

                $options = [
                    'format' => [$request->extension],
                    'locale' => 'en',
                    'params' => [
                        'fecha_inicio' => $request->fechaInicio,
                        'fecha_final' => $request->fechaFinal,
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

                return response()->download($output . '.' . $request->extension, $hora_actual . 'ReporteSolicitudChequeAnulado.' . $request->extension, $headers);
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


    public function generarReporteComisiones(Request $request)
    {
        // echo $ext;
        $rules = [
            'agrupacion' => 'required|string',
            'fechaFinal' => 'required|date',
            'fechaInicial' => 'required|date',
            'id_vendedor' => 'required|integer|min:0',
            'extension' => 'required|string|max:4',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {

            $repos = array("ReporteNotasCreditoPorVendedorComisiones", "ReporteNotasDebitoPorVendedorComisiones",
                "ReporteRecibosPorVendedorComisiones","ReporteVentasPorVendedorComisiones","ReporteVendedorComisionesConsolidado");
            $os = array("xls", "pdf","html");
            //echo gethostname();
            if (in_array($request->extension, $os) && in_array($request->agrupacion, $repos)) {

                $hora_actual = time();
                //$input = '/Applications/XAMPP/xamppfiles/htdocs/resources/reports/ReporteVentasPorCliente';
                //$output = '/Applications/XAMPP/xamppfiles/htdocs/resources/reports/'.$hora_actual.'ReporteVentasPorCliente';

                $input = '/var/www/html/resources/reports/'.$request->agrupacion;
                $output = '/var/www/html/resources/reports/' .$hora_actual .$request->agrupacion;

                //$input = 'C:/xampp7/htdocs/resources/reports/'.$request->agrupacion;
                //$output = 'C:/xampp7/htdocs/resources/reports/' .$hora_actual .$request->agrupacion;


                $nombre_empresa = AdmonAjustes::where('id_ajuste',4)->select('valor')->first();
                $logo_empresa = AdmonAjustes::where('id_ajuste',3)->select(DB::raw("substr(valor, 2, length(valor) - 2)::json->>'file_thumbnail' as file_thumbnail"))->first();
                $url = '/var/www/html/resources/reports/';
                //$url = 'C:/xampp7/htdocs/resources/reports/';

                $options = [
                    'format' => [$request->extension],
                    'locale' => 'en',
                    'params' => [
                        'fechaFinal' => $request->fechaFinal,
                        'id_vendedorx' => $request->id_vendedor,
                        'fechaInicial' => $request->fechaInicial,
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

                return response()->download($output . '.' . $request->extension, $hora_actual . $request->agrupacion.'.' . $request->extension, $headers);

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









}