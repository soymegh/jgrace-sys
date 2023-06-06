<?php



namespace App\Http\Controllers\Contabilidad;

use App\Http\Controllers\Controller;
use App\Models\Admon\Ajustes;
use App\Models\Admon\UsuariosEmpresas;
use App\Models\Inventario\Bodegas;
use App\Models\Inventario\Kardex;
use App\Models\Inventario\Productos;
use App\Models\Ventas\Clientes;
use App\Models\Ventas\Vendedores;
use http\Env\Response;
use Illuminate\Http\JsonResponse;
use PHPJasper\Exception\ErrorCommandExecutable;
use PHPJasper\Exception\InvalidCommandExecutable;
use PHPJasper\Exception\InvalidFormat;
use PHPJasper\Exception\InvalidInputFile;
use PHPJasper\Exception\InvalidResourceDirectory;
use PHPJasper\PHPJasper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use  Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportesContabilidadController extends Controller
{
    /**
     * @param $ext - pdf |  xls
     * @param $type 1 = consolidado | 2 = Detallado
     * @param $fecha_inicial
     * @param $fecha_final
     * @return string|BinaryFileResponse
     * @throws ErrorCommandExecutable
     * @throws InvalidCommandExecutable
     * @throws InvalidFormat
     * @throws InvalidInputFile
     * @throws InvalidResourceDirectory
     */
    public function generarReporteDocumentosContables($ext, $type,$fecha_inicial, $fecha_final)
    {
        // echo $ext;

        $os = array("xls", "pdf");
        //echo gethostname();
        if (in_array($ext, $os, true)) {
            /*$input = 'C:/xampp7/htdocs/resources/reports/Blank_A4.jrxml';
              echo $input;
              $jasper = new PHPJasper;
              $jasper->compile($input)->execute();
            */
            $hora_actual = time() ;
            $reportName = '';
            if($type == 1) {
                $reportName = 'DocumentosContables';
            }else {
                $reportName='DocumentosContablesDetallado';
            }

            $input = env('APP_URL_REPORTES') . $reportName;
            $output = env('APP_URL_REPORTES') . $hora_actual . $reportName;
            // Rutas de descarga de Reportes en servidor
//            $input = env('APP_URL_REPORTES') . 'DocumentosContables';
//            $output = env('APP_URL_REPORTES') . $hora_actual .  'DocumentosContables';

            if($ext === 'xls'){
                $input .= '.jasper';
            }

            //Ajustes generales del sistema
            $logo_empresa = Ajustes::where('id_ajuste', 3)->select('valor')->first();
            $nombre_empresa = Ajustes::where('id_ajuste', 4)->select('valor')->first();

            $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
            $options = [
                'format' => [$ext],
                'locale' => 'es',
                'params' => [
                    'logo_empresa' => env('APP_URL_IMAGES').$logo_empresa->valor,
                    'nombre_empresa' => $nombre_empresa->valor,
                    'fecha_inicial' => $fecha_inicial,
                    'fecha_final' => $fecha_final,
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
            /*header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . $hora_actual. 'CuentasContables.' . $ext);
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Content-Length: ' . filesize($output . '.' . $ext));
            flush();
            readfile($output . '.' . $ext);
            unlink($output . '.' . $ext);*/

            /*print_r( env('APP_URL_REPORTS').$logo_empresa->valor);*/
            $headers = [
                'Content-Type' => 'application/pdf',
            ];

            return response()->download($output . '.' . $ext ,$hora_actual. 'DocumentosContables.' . $ext, $headers)->deleteFileAfterSend();

            /* exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
                print_r($output);*/
        }

        return '';
    }

    public function generarReporteDocumentoContableEspecifico($ext, $id_documento)
    {
        // echo $ext;

        $os = array("xls", "pdf");

        if (in_array($ext, $os, true)) {

            $hora_actual = time() ;
            $input = env('APP_URL_REPORTES') . 'DocumentoContableEspecifico';
            $output = env('APP_URL_REPORTES') . $hora_actual . 'DocumentoContableEspecifico';

            // Rutas de descarga de Reportes en servidor
//            $input = env('APP_URL_REPORTES') . 'DocumentosContables';
//            $output = env('APP_URL_REPORTES') . $hora_actual .  'DocumentosContables';

            if($ext === 'xls'){
                $input .= '.jasper';
            }

            //Ajustes generales del sistema
            $logo_empresa = Ajustes::where('id_ajuste', 3)->select('valor')->first();
            $nombre_empresa = Ajustes::where('id_ajuste', 4)->select('valor')->first();

            $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
            $options = [
                'format' => [$ext],
                'locale' => 'es',
                'params' => [
                    'logo_empresa' => env('APP_URL_IMAGES').$logo_empresa->valor,
                    'nombre_empresa' => $nombre_empresa->valor,
                    'id_documento' => $id_documento
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

            try {
                $jasper->process($input, $output, $options)->execute();
            } catch (ErrorCommandExecutable $e) {
            } catch (InvalidCommandExecutable $e) {
            } catch (InvalidFormat $e) {
            } catch (InvalidInputFile $e) {
            } catch (InvalidResourceDirectory $e) {
            }

            $headers = [
                'Content-Type' => 'application/pdf',
            ];
/*                        exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
                        print_r($output);*/
            return response()->download($output . '.' . $ext ,$hora_actual. 'DocumentoContableEspecifico.' . $ext, $headers)->deleteFileAfterSend();

        }
    }

    public function generarReporteMovimientosPorCuenta($extension,$id_cuenta_contable, $fecha_inicial, $fecha_final)
    {
        $os = array("pdf", "xls");
        //echo gethostname();
        if (in_array($extension, $os, true)) {

            $hora_actual = time();
            $nombre_reporte = 'MovimientoPorCtacontable';

            $input = env('APP_URL_REPORTES') . $nombre_reporte;
            $output = env('APP_URL_REPORTES') . $hora_actual . $nombre_reporte;

            if($extension === 'xls'){
                $input .= '.jasper';
            }

            $logo_empresa = Ajustes::where('id_ajuste', 3)->select('valor')->first();
            $nombre_empresa = Ajustes::where('id_ajuste', 4)->select('valor')->first();
            $url = ENV('URL_REPORTES');

            $options = [
                'format' => [$extension],
                'locale' => 'en',
                'params' => [
                    'id_cuenta_contable' => $id_cuenta_contable,
                    'fecha_inicial' => $fecha_inicial,
                    'fecha_final' => $fecha_final,
                    'logo_empresa' => env('APP_URL_IMAGES').$logo_empresa->valor,
                    'nombre_empresa' => $nombre_empresa->valor,
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

            try {
                $jasper->process($input, $output, $options)->execute();
            } catch (ErrorCommandExecutable $e) {
            } catch (InvalidCommandExecutable $e) {
            } catch (InvalidFormat $e) {
            } catch (InvalidInputFile $e) {
            } catch (InvalidResourceDirectory $e) {
            }

            /*                    exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
                                print_r($output);*/

            $headers = [
                'Content-Type' => 'application/pdf',
            ];

            return response()->download($output . '.' . $extension, $hora_actual . $nombre_reporte . $extension, $headers)->deleteFileAfterSend();

        }


        /*             exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
                        print_r($output);*/

        return '';
    }

    public function generarReporteIngresoCostoRubro($extension, $fecha_inicial, $fecha_final)
    {
        $os = array("pdf", "xls");
        //echo gethostname();
        if (in_array($extension, $os, true)) {

            $hora_actual = time();
            $nombre_reporte = 'ReporteIngresoCostoRubro';

            $input = env('APP_URL_REPORTES') . $nombre_reporte;
            $output = env('APP_URL_REPORTES') . $hora_actual . $nombre_reporte;

            if($extension === 'xls'){
                $input .= '.jasper';
            }

            $logo_empresa = Ajustes::where('id_ajuste', 3)->select('valor')->first();
            $nombre_empresa = Ajustes::where('id_ajuste', 4)->select('valor')->first();
            $url = ENV('URL_REPORTES');

            $options = [
                'format' => [$extension],
                'locale' => 'en',
                'params' => [
                    'fecha_inicial' => $fecha_inicial,
                    'fecha_final' => $fecha_final,
                    /*'logo_empresa' => env('APP_URL_IMAGES').$logo_empresa->valor,
                    'nombre_empresa' => $nombre_empresa->valor,*/
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

            try {
                $jasper->process($input, $output, $options)->execute();
            } catch (ErrorCommandExecutable $e) {
            } catch (InvalidCommandExecutable $e) {
            } catch (InvalidFormat $e) {
            } catch (InvalidInputFile $e) {
            } catch (InvalidResourceDirectory $e) {
            }

            /*                    exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
                                print_r($output);*/

            $headers = [
                'Content-Type' => 'application/pdf',
            ];

            return response()->download($output . '.' . $extension, $hora_actual . $nombre_reporte . $extension, $headers)->deleteFileAfterSend();

        }


        /*             exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
                        print_r($output);*/

        return '';
    }

    public function generarReporteCatalogoCuentasContables($ext)
    {
        // echo $ext;

        $os = array("xls", "pdf");
        //echo gethostname();
        if (in_array($ext, $os)) {
            /*$input = 'C:/xampp7/htdocs/resources/reports/Blank_A4.jrxml';
              echo $input;
              $jasper = new PHPJasper;
              $jasper->compile($input)->execute();
            */
            $hora_actual = time() ;
            $input = env('APP_URL_REPORTES') . 'CatalogoCuentasContables';
            $output = env('APP_URL_REPORTES') . $hora_actual . 'CatalogoCuentasContables';

            // Rutas de descarga de Reportes en servidor
//            $input = env('APP_URL_REPORTES') . 'Vendedores';
//            $output = env('APP_URL_REPORTES') . $hora_actual .  'Vendedores';

            if($ext == 'xls'){
                $input = $input.'.jasper';
            }

            //Ajustes generales del sistema
            $logo_empresa = Ajustes::where('id_ajuste', 3)->select('valor')->first();
            $nombre_empresa = Ajustes::where('id_ajuste', 4)->select('valor')->first();

            $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', \Illuminate\Support\Facades\Auth::user()->id)->first();
            $options = [
                'format' => [$ext],
                'locale' => 'es',
                'params' => [
                    'logo_empresa' => env('APP_URL_IMAGES').$logo_empresa->valor,
                    'nombre_empresa' => $nombre_empresa->valor,
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
            /*header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . $hora_actual. 'CuentasContables.' . $ext);
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Content-Length: ' . filesize($output . '.' . $ext));
            flush();
            readfile($output . '.' . $ext);
            unlink($output . '.' . $ext);*/

            /*print_r( env('APP_URL_REPORTS').$logo_empresa->valor);*/
            $headers = [
                'Content-Type' => 'application/pdf',
            ];

            return response()->download($output . '.' . $ext ,$hora_actual. 'CatalogoCuentasContables.' . $ext, $headers);

            /* exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
                print_r($output);*/
        }
    }

    public function generarReporteTipoCuentas($ext)
    {
        // echo $ext;

        $os = array("xls", "pdf");
        //echo gethostname();
        if (in_array($ext, $os)) {
            /*$input = 'C:/xampp7/htdocs/resources/reports/Blank_A4.jrxml';
              echo $input;
              $jasper = new PHPJasper;
              $jasper->compile($input)->execute();
            */
            $hora_actual = time() ;
            $input = env('APP_URL_REPORTES') . 'TipoCuentas';
            $output = env('APP_URL_REPORTES') . $hora_actual . 'TipoCuentas';

            // Rutas de descarga de Reportes en servidor
//            $input = env('APP_URL_REPORTES') . 'TipoCuentas';
//            $output = env('APP_URL_REPORTES') . $hora_actual .  'TipoCuentas';

            if($ext == 'xls'){
                $input = $input.'.jasper';
            }

            //Ajustes generales del sistema
            $logo_empresa = Ajustes::where('id_ajuste', 3)->select('valor')->first();
            $nombre_empresa = Ajustes::where('id_ajuste', 4)->select('valor')->first();

            $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
            $options = [
                'format' => [$ext],
                'locale' => 'es',
                'params' => [
                    'logo_empresa' => env('APP_URL_IMAGES').$logo_empresa->valor,
                    'nombre_empresa' => $nombre_empresa->valor,
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
            /*header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . $hora_actual. 'CuentasContables.' . $ext);
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Content-Length: ' . filesize($output . '.' . $ext));
            flush();
            readfile($output . '.' . $ext);
            unlink($output . '.' . $ext);*/

            /*print_r( env('APP_URL_REPORTS').$logo_empresa->valor);*/
            $headers = [
                'Content-Type' => 'application/pdf',
            ];

            return response()->download($output . '.' . $ext ,$hora_actual. 'TipoCuentas.' . $ext, $headers);

            /* exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
                print_r($output);*/
        }
    }

    public function generarReporteNivelesCuentas($ext, Request $request)
    {
        // echo $ext;

        $os = array("xls", "pdf");
        //echo gethostname();
        if (in_array($ext, $os)) {
            /*$input = 'C:/xampp7/htdocs/resources/reports/Blank_A4.jrxml';
              echo $input;
              $jasper = new PHPJasper;
              $jasper->compile($input)->execute();
            */
            $hora_actual = time() ;
            $input = env('APP_URL_REPORTES') . 'NivelesCuentas';
            $output = env('APP_URL_REPORTES') . $hora_actual . 'NivelesCuentas';

            // Rutas de descarga de Reportes en servidor
//            $input = env('APP_URL_REPORTES') . 'NivelesCuentas';
//            $output = env('APP_URL_REPORTES') . $hora_actual .  'NivelesCuentas';

            if($ext == 'xls'){
                $input = $input.'.jasper';
            }

            //Ajustes generales del sistema
            $logo_empresa = Ajustes::where('id_ajuste', 3)->select('valor')->first();
            $nombre_empresa = Ajustes::where('id_ajuste', 4)->select('valor')->first();

            $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
            $options = [
                'format' => [$ext],
                'locale' => 'es',
                'params' => [
                    'logo_empresa' => env('APP_URL_IMAGES').$logo_empresa->valor,
                    'nombre_empresa' => $nombre_empresa->valor,
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
            /*header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . $hora_actual. 'CuentasContables.' . $ext);
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Content-Length: ' . filesize($output . '.' . $ext));
            flush();
            readfile($output . '.' . $ext);
            unlink($output . '.' . $ext);*/

            /*print_r( env('APP_URL_REPORTS').$logo_empresa->valor);*/
            $headers = [
                'Content-Type' => 'application/pdf',
            ];

            return response()->download($output . '.' . $ext ,$hora_actual. 'NivelesCuentas.' . $ext, $headers);

            /* exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
                print_r($output);*/
        }
    }

    public function generarReporteTipoDocumentos($ext)
    {
        // echo $ext;

        $os = array("xls", "pdf");
        //echo gethostname();
        if (in_array($ext, $os)) {
            /*$input = 'C:/xampp7/htdocs/resources/reports/Blank_A4.jrxml';
              echo $input;
              $jasper = new PHPJasper;
              $jasper->compile($input)->execute();
            */
            $hora_actual = time() ;
            $input = env('APP_URL_REPORTES') . 'TipoDocumentos';
            $output = env('APP_URL_REPORTES') . $hora_actual . 'TipoDocumentos';

            // Rutas de descarga de Reportes en servidor
//            $input = env('APP_URL_REPORTES') . 'TipoDocumentos';
//            $output = env('APP_URL_REPORTES') . $hora_actual .  'TipoDocumentos';

            if($ext == 'xls'){
                $input = $input.'.jasper';
            }

            //Ajustes generales del sistema
            $logo_empresa = Ajustes::where('id_ajuste', 3)->select('valor')->first();
            $nombre_empresa = Ajustes::where('id_ajuste', 4)->select('valor')->first();

            $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
            $options = [
                'format' => [$ext],
                'locale' => 'es',
                'params' => [
                    'logo_empresa' => env('APP_URL_IMAGES').$logo_empresa->valor,
                    'nombre_empresa' => $nombre_empresa->valor,
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
            /*header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . $hora_actual. 'CuentasContables.' . $ext);
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Content-Length: ' . filesize($output . '.' . $ext));
            flush();
            readfile($output . '.' . $ext);
            unlink($output . '.' . $ext);*/

            /*print_r( env('APP_URL_REPORTS').$logo_empresa->valor);*/
            $headers = [
                'Content-Type' => 'application/pdf',
            ];

            return response()->download($output . '.' . $ext ,$hora_actual. 'TipoDocumentos.' . $ext, $headers);

            /* exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
                print_r($output);*/
        }
    }

    public function generarReporteTasaCambio($ext, $periodo, $mes)
    {
        // echo $ext;

        $os = array("xls", "pdf");
        //echo gethostname();
        if (in_array($ext, $os)) {
            /*$input = 'C:/xampp7/htdocs/resources/reports/Blank_A4.jrxml';
              echo $input;
              $jasper = new PHPJasper;
              $jasper->compile($input)->execute();
            */
            $hora_actual = time() ;
            $input = env('APP_URL_REPORTES') . 'TasasCambio';
            $output = env('APP_URL_REPORTES') . $hora_actual . 'TasasCambio';

            // Rutas de descarga de Reportes en servidor
//            $input = env('APP_URL_REPORTES') . 'TasasCambio';
//            $output = env('APP_URL_REPORTES') . $hora_actual .  'TasasCambio';

            if($ext == 'xls'){
                $input = $input.'.jasper';
            }

            //Ajustes generales del sistema
            $logo_empresa = Ajustes::where('id_ajuste', 3)->select('valor')->first();
            $nombre_empresa = Ajustes::where('id_ajuste', 4)->select('valor')->first();

            $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
            $options = [
                'format' => [$ext],
                'locale' => 'es',
                'params' => [
                    'logo_empresa' => env('APP_URL_IMAGES').$logo_empresa->valor,
                    'nombre_empresa' => $nombre_empresa->valor,
                    'id_empresa' => $usuario_empresa->id_empresa,
                    'mes'=>$mes,
                    'anio'=>$periodo
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
            /*header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . $hora_actual. 'CuentasContables.' . $ext);
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Content-Length: ' . filesize($output . '.' . $ext));
            flush();
            readfile($output . '.' . $ext);
            unlink($output . '.' . $ext);*/

            /*print_r( env('APP_URL_REPORTS').$logo_empresa->valor);*/
            $headers = [
                'Content-Type' => 'application/pdf',
            ];

            return response()->download($output . '.' . $ext ,$hora_actual. 'TasasCambio.' . $ext, $headers);

            /* exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
                print_r($output);*/
        }
    }

    public function generarReporteComprobantesDescuadrados($extension, $anio, $mes)
    {
        $os = array("pdf", "xls");
        //echo gethostname();
        if (in_array($extension, $os, true)) {

            $hora_actual = time();
            $nombre_reporte = 'ReporteComprobantesDescuadrados';

            $input = env('APP_URL_REPORTES') . $nombre_reporte;
            $output = env('APP_URL_REPORTES') . $hora_actual . $nombre_reporte;

            if($extension === 'xls'){
                $input .= '.jasper';
            }

            $logo_empresa = Ajustes::where('id_ajuste', 3)->select('valor')->first();
            $nombre_empresa = Ajustes::where('id_ajuste', 4)->select('valor')->first();
            $url = env('URL_REPORTES');

            $options = [
                'format' => [$extension],
                'locale' => 'en',
                'params' => [
                    'nombre_empresa' => $nombre_empresa->valor,
                    'logo_empresa' => env('APP_URL_IMAGES').$logo_empresa->valor,
                    'mes' => $mes,
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

            try {
                $jasper->process($input, $output, $options)->execute();
            } catch (ErrorCommandExecutable $e) {
            } catch (InvalidCommandExecutable $e) {
            } catch (InvalidFormat $e) {
            } catch (InvalidInputFile $e) {
            } catch (InvalidResourceDirectory $e) {
            }

            /*                    exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
                                print_r($output);*/

            $headers = [
                'Content-Type' => 'application/pdf',
            ];

            return response()->download($output . '.' . $extension, $hora_actual . $nombre_reporte . $extension, $headers)->deleteFileAfterSend();

        }


        /*             exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
                        print_r($output);*/

        return '';
    }

    public function generarReporteLibroDiario($ext, $periodo, $id_periodo , $mes)
    {
        // echo $ext;

        $os = array("xls", "pdf");
        //echo gethostname();
        if (in_array($ext, $os)) {
            /*$input = 'C:/xampp7/htdocs/resources/reports/Blank_A4.jrxml';
              echo $input;
              $jasper = new PHPJasper;
              $jasper->compile($input)->execute();
            */
            $hora_actual = time() ;
            $input = env('APP_URL_REPORTES') . 'LibroDiario';
            $output = env('APP_URL_REPORTES') . $hora_actual . 'LibroDiario';

            // Rutas de descarga de Reportes en servidor
//            $input = env('APP_URL_REPORTES') . 'LibroDiario';
//            $output = env('APP_URL_REPORTES') . $hora_actual .  'LibroDiario';

            if($ext == 'xls'){
                $input = $input.'.jasper';
            }

            //Ajustes generales del sistema
            $logo_empresa = Ajustes::where('id_ajuste', 3)->select('valor')->first();
            $nombre_empresa = Ajustes::where('id_ajuste', 4)->select('valor')->first();

            $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
            $options = [
                'format' => [$ext],
                'locale' => 'es',
                'params' => [
                    'logo_empresa' => env('APP_URL_IMAGES').$logo_empresa->valor,
                    'nombre_empresa' => $nombre_empresa->valor,
                    'id_empresa' => $usuario_empresa->id_empresa,
                    'mesx'=>$mes,
                    'aniox'=>$periodo,
                    'id_periodox' => $id_periodo
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
            /*header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . $hora_actual. 'CuentasContables.' . $ext);
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Content-Length: ' . filesize($output . '.' . $ext));
            flush();
            readfile($output . '.' . $ext);
            unlink($output . '.' . $ext);*/

            /*print_r( env('APP_URL_REPORTS').$logo_empresa->valor);*/
            $headers = [
                'Content-Type' => 'application/pdf',
            ];

            return response()->download($output . '.' . $ext ,$hora_actual. 'LibroDiario.' . $ext, $headers);

            /* exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
                print_r($output);*/
        }
    }
    public function generarReporteLibroMayor($ext, $periodo, $id_periodo , $mes)
    {
        // echo $ext;

        $os = array("xls", "pdf");
        //echo gethostname();
        if (in_array($ext, $os)) {
            /*$input = 'C:/xampp7/htdocs/resources/reports/Blank_A4.jrxml';
              echo $input;
              $jasper = new PHPJasper;
              $jasper->compile($input)->execute();
            */
            $hora_actual = time() ;
            $input = env('APP_URL_REPORTES') . 'LibroMayor';
            $output = env('APP_URL_REPORTES') . $hora_actual . 'LibroMayor';

            // Rutas de descarga de Reportes en servidor
//            $input = env('APP_URL_REPORTES') . 'LibroMayor';
//            $output = env('APP_URL_REPORTES') . $hora_actual .  'LibroMayor';

            if($ext == 'xls'){
                $input = $input.'.jasper';
            }

            //Ajustes generales del sistema
            $logo_empresa = Ajustes::where('id_ajuste', 3)->select('valor')->first();
            $nombre_empresa = Ajustes::where('id_ajuste', 4)->select('valor')->first();

            $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
            $options = [
                'format' => [$ext],
                'locale' => 'es',
                'params' => [
                    'logo_empresa' => env('APP_URL_IMAGES').$logo_empresa->valor,
                    'nombre_empresa' => $nombre_empresa->valor,
                    'id_empresa' => $usuario_empresa->id_empresa,
                    'mesx'=>$mes,
                    'aniox'=>$periodo,
                    'id_periodox' => $id_periodo
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
            /*header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . $hora_actual. 'CuentasContables.' . $ext);
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Content-Length: ' . filesize($output . '.' . $ext));
            flush();
            readfile($output . '.' . $ext);
            unlink($output . '.' . $ext);*/

            /*print_r( env('APP_URL_REPORTS').$logo_empresa->valor);*/
            $headers = [
                'Content-Type' => 'application/pdf',
            ];

            return response()->download($output . '.' . $ext ,$hora_actual. 'LibroMayor.' . $ext, $headers);

            /* exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
                print_r($output);*/
        }
    }

    /**
     * Reporte de estado de resultado
     * @param $id_periodo_fiscal
     * @param $mes
     * @param $currency
     * @param $extension
     * @param $periodo
     * @return string|BinaryFileResponse
     * @version 1.0
     * @author octaviom
     */
    public function generarReporteEstadoResultado($id_periodo_fiscal, $mes, $currency, $extension, $periodo)
    {
        $os = array("pdf", "xls");

        if (in_array($extension, $os, true)) {

            $hora_actual = time();
            if($currency === 'NIO'){
                $nombre_reporte = 'EstadoResultado';
            }else{
                $nombre_reporte = 'EstadoResultadoDol';
            }

            $input = env('APP_URL_REPORTES') . $nombre_reporte;
            $output = env('APP_URL_REPORTES') . $hora_actual . $nombre_reporte;

            if($extension === 'xls'){
                $input .= '.jasper';
            }


            //Ajustes generales del sistema
            $logo_empresa = Ajustes::where('id_ajuste', 3)->select('valor')->first();
            $nombre_empresa = Ajustes::where('id_ajuste', 4)->select('valor')->first();
            $url = env('APP_URL_REPORTES');

            $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
            $options = [
                'format' => [$extension],
                'locale' => 'en',
                'params' => [
                    'id_periodo_fiscal' => $id_periodo_fiscal,
                    'mes'=>$mes,
                    'logo_empresa' => env('APP_URL_IMAGES').$logo_empresa->valor,
                    'nombre_empresa' => $nombre_empresa->valor,
                    'periodo' => $periodo,
                    'directorioReports'=>$url,
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

            try {
                $jasper->process($input, $output, $options)->execute();
            } catch (ErrorCommandExecutable $e) {
            } catch (InvalidCommandExecutable $e) {
            } catch (InvalidFormat $e) {
            } catch (InvalidInputFile $e) {
            } catch (InvalidResourceDirectory $e) {
            }

  /*          exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
            print_r($output);*/

            $headers = [
                'Content-Type' => 'application/pdf',
            ];



            return response()->download($output . '.' . $extension, $hora_actual . $nombre_reporte . $extension, $headers)->deleteFileAfterSend();


        }

        return '';
    }

    public function generarReporteCambioPatrimonio($ext, $id_periodo , $mes)
    {
        // echo $ext;

        $os = array("xls", "pdf");
        //echo gethostname();
        if (in_array($ext, $os)) {
            /*$input = 'C:/xampp7/htdocs/resources/reports/Blank_A4.jrxml';
              echo $input;
              $jasper = new PHPJasper;
              $jasper->compile($input)->execute();
            */
            $hora_actual = time() ;
            $input = env('APP_URL_REPORTES') . 'EstadoCambioPatrimonio';
            $output = env('APP_URL_REPORTES') . $hora_actual . 'EstadoCambioPatrimonio';

            // Rutas de descarga de Reportes en servidor
//            $input = env('APP_URL_REPORTES') . 'EstadoCambioPatrimonio';
//            $output = env('APP_URL_REPORTES') . $hora_actual .  'EstadoCambioPatrimonio';

            if($ext == 'xls'){
                $input = $input.'.jasper';
            }

            //Ajustes generales del sistema
            $logo_empresa = Ajustes::where('id_ajuste', 3)->select('valor')->first();
            $nombre_empresa = Ajustes::where('id_ajuste', 4)->select('valor')->first();

            $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
            $options = [
                'format' => [$ext],
                'locale' => 'es',
                'params' => [
                    'logo_empresa' => env('APP_URL_IMAGES').$logo_empresa->valor,
                    'nombre_empresa' => $nombre_empresa->valor,
                    'id_empresax' => $usuario_empresa->id_empresa,
                    'mesx'=>$mes,
                    'id_periodox' => $id_periodo,
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
            /*header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . $hora_actual. 'CuentasContables.' . $ext);
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Content-Length: ' . filesize($output . '.' . $ext));
            flush();
            readfile($output . '.' . $ext);
            unlink($output . '.' . $ext);*/

            /*print_r( env('APP_URL_REPORTS').$logo_empresa->valor);*/
            $headers = [
                'Content-Type' => 'application/pdf',
            ];

            return response()->download($output . '.' . $ext ,$hora_actual. 'EstadoCambioPatrimonio.' . $ext, $headers);

            /* exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
                print_r($output);*/
        }
    }

    public function generarReporteBalanzaComprobacion($ext, $id_nivel_cuenta , $id_periodo , $mes)
    {
        // echo $ext;

        $os = array("xls", "pdf");
        //echo gethostname();
        if (in_array($ext, $os)) {
            /*$input = 'C:/xampp7/htdocs/resources/reports/Blank_A4.jrxml';
              echo $input;
              $jasper = new PHPJasper;
              $jasper->compile($input)->execute();
            */
            $hora_actual = time() ;
            $input = env('APP_URL_REPORTES') . 'BalanzaComprobacionConsolidadaAnualCord';
            $output = env('APP_URL_REPORTES') . $hora_actual . 'BalanzaComprobacionConsolidadaAnualCord';

            // Rutas de descarga de Reportes en servidor
//            $input = env('APP_URL_REPORTES') . 'EstadoCambioPatrimonio';
//            $output = env('APP_URL_REPORTES') . $hora_actual .  'EstadoCambioPatrimonio';

            if($ext == 'xls'){
                $input = $input.'.jasper';
            }

            //Ajustes generales del sistema
            $logo_empresa = Ajustes::where('id_ajuste', 3)->select('valor')->first();
            $nombre_empresa = Ajustes::where('id_ajuste', 4)->select('valor')->first();

            $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
            $options = [
                'format' => [$ext],
                'locale' => 'es',
                'params' => [
                    'logo_empresa' => env('APP_URL_IMAGES').$logo_empresa->valor,
                    'nombre_empresa' => $nombre_empresa->valor,
                    'id_empresax' => $usuario_empresa->id_empresa,
                    'mesx'=>$mes,
                    'id_periodox' => $id_periodo,
                    'id_nivelx' => $id_nivel_cuenta
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
            /*header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . $hora_actual. 'CuentasContables.' . $ext);
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Content-Length: ' . filesize($output . '.' . $ext));
            flush();
            readfile($output . '.' . $ext);
            unlink($output . '.' . $ext);*/

            /*print_r( env('APP_URL_REPORTS').$logo_empresa->valor);*/
            $headers = [
                'Content-Type' => 'application/pdf',
            ];

            return response()->download($output . '.' . $ext ,$hora_actual. 'BalanzaComprobacionConsolidadaAnual.' . $ext, $headers)->deleteFileAfterSend();

            /* exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
                print_r($output);*/
        }
    }

    public function generarReporteRazonesFinancieras($ext, $id_periodo_act , $mes_act, $id_periodo_ant, $mes_ant)
    {
        // echo $ext;

        $os = array("xls", "pdf");
        //echo gethostname();
        if (in_array($ext, $os)) {
            /*$input = 'C:/xampp7/htdocs/resources/reports/Blank_A4.jrxml';
              echo $input;
              $jasper = new PHPJasper;
              $jasper->compile($input)->execute();
            */
            $hora_actual = time() ;
            $input = env('APP_URL_REPORTES') . 'RazonesFinancieras';
            $output = env('APP_URL_REPORTES') . $hora_actual . 'RazonesFinancieras';

            // Rutas de descarga de Reportes en servidor
//            $input = env('APP_URL_REPORTES') . 'RazonesFinancieras';
//            $output = env('APP_URL_REPORTES') . $hora_actual .  'RazonesFinancieras';

            if($ext == 'xls'){
                $input = $input.'.jasper';
            }

            //Ajustes generales del sistema
            $logo_empresa = Ajustes::where('id_ajuste', 3)->select('valor')->first();
            $nombre_empresa = Ajustes::where('id_ajuste', 4)->select('valor')->first();

            $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
            $options = [
                'format' => [$ext],
                'locale' => 'es',
                'params' => [
                    'logo_empresa' => env('APP_URL_IMAGES').$logo_empresa->valor,
                    'nombre_empresa' => $nombre_empresa->valor,
                    'id_empresax' => $usuario_empresa->id_empresa,
                    'mesx1'=>$mes_act,
                    'id_periodox1' => $id_periodo_act,
                    'id_periodox2' =>$id_periodo_ant,
                    'mesx2'=>$mes_ant
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
            /*header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . $hora_actual. 'CuentasContables.' . $ext);
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Content-Length: ' . filesize($output . '.' . $ext));
            flush();
            readfile($output . '.' . $ext);
            unlink($output . '.' . $ext);*/

            /*print_r( env('APP_URL_REPORTS').$logo_empresa->valor);*/
            $headers = [
                'Content-Type' => 'application/pdf',
            ];

            return response()->download($output . '.' . $ext ,$hora_actual. 'RazonesFinancieras.' . $ext, $headers);

            /* exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
                print_r($output);*/
        }
    }

    /**
     * Reporte de movimientos de inventario consolidado por movimientos
     * @param $ext
     * @param $id_bodega
     * @param $f_inicial
     * @param $f_final
     * @return string
     * @version 1.0
     * @author octaviom
     */

    public function generarReporteMovimientosConSaldos($ext, $id_bodega,$f_inicial,$f_final)
    {
        $os = array("xls", "pdf");
        if (in_array($ext, $os, true)) {
            // Obtener hora actual
            $hora_actual = time() ;

            // Rutas de descarga de Reportes en servidor
            $input = env('APP_URL_REPORTES') . 'ConsolidadoMovimientosCosto';
            $output = env('APP_URL_REPORTES') . $hora_actual . 'ConsolidadoMovimientosCosto';

            if($ext === 'xls'){
                $input .= '.jasper';
            }

            //Ajustes generales del sistema
            $logo_empresa = Ajustes::where('id_ajuste', 3)->select('valor')->first();
            $nombre_empresa = Ajustes::where('id_ajuste', 4)->select('valor')->first();

            $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
            $options = [
                'format' => [$ext],
                'locale' => 'es',
                'params' => [
                    'logo_empresa' => env('APP_URL_IMAGES').$logo_empresa->valor,
                    'nombre_empresa' => $nombre_empresa->valor,
                    'id_bodega' => $id_bodega,
                    'fecha_inicial' => $f_inicial,
                    'fecha_final' => $f_final,
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

            try {
                $jasper->process($input, $output, $options)->execute();
            } catch (ErrorCommandExecutable $e) {
            } catch (InvalidCommandExecutable $e) {
            } catch (InvalidFormat $e) {
            } catch (InvalidInputFile $e) {
            } catch (InvalidResourceDirectory $e) {
            }

            $headers = [
                'Content-Type' => 'application/pdf',
            ];

            return response()->download($output . '.' . $ext ,$hora_actual. 'ConsolidadMovimientoConMontos.' . $ext, $headers);

            /* Utilizar linea para depurar
            exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
            print_r($output);
            */
        }
        return '';
    }

    /**
     * @access public
     * @param
     * @return JsonResponse
     * @author Dev-Lopez02
     * Obteniene datos que alimentan el formulario reporte de contabilidad
     */
    public function obtenerCatalago (){

       $clientes= Clientes::where('estado', 1)->get();
       $vendedores= Vendedores::where('estado', 1)->get();

       if ($clientes && $vendedores){
           return response()->json([
               'status' => 'success',
               'clientes' => $clientes,
               'vendedores' => $vendedores,
               'messages' => 'Se han obtenido correctamente los datos'
           ],200);

       }else{
           return response()->json([
               'status' => 'error',
               'menssages' =>'No se podido obtener informacion',
           ], 500);
       }


    }

    public function generarReporteListadoFactura($extension,$id_cliente,$id_vendedor, $fecha_inicial, $fecha_final)
    {
        $os = array("pdf", "xls");
        //echo gethostname();
        if (in_array($extension, $os, true)) {

            $hora_actual = time();
            $nombre_reporte = 'ListadodeFacturas';

            $input = env('APP_URL_REPORTES') . $nombre_reporte;
            $output = env('APP_URL_REPORTES') . $hora_actual . $nombre_reporte;

            if($extension === 'xls'){
                $input .= '.jasper';
            }

            $logo_empresa = Ajustes::where('id_ajuste', 3)->select('valor')->first();
            $nombre_empresa = Ajustes::where('id_ajuste', 4)->select('valor')->first();
            $url = ENV('URL_REPORTES');

            $options = [
                'format' => [$extension],
                'locale' => 'en',
                'params' => [
                    'id_cliente' => $id_cliente,
                    'id_vendedor' => $id_vendedor,
                    'fechainicial' => $fecha_inicial,
                    'fechafinal' => $fecha_final,
                    'logo_empresa' => env('APP_URL_IMAGES').$logo_empresa->valor,
                    'nombre_empresa' => $nombre_empresa->valor,
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

            try {
                $jasper->process($input, $output, $options)->execute();
            } catch (ErrorCommandExecutable $e) {
            } catch (InvalidCommandExecutable $e) {
            } catch (InvalidFormat $e) {
            } catch (InvalidInputFile $e) {
            } catch (InvalidResourceDirectory $e) {
            }

            /*                    exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
                                print_r($output);*/

            $headers = [
                'Content-Type' => 'application/pdf',
            ];

            return response()->download($output . '.' . $extension, $hora_actual . $nombre_reporte . $extension, $headers)->deleteFileAfterSend();

        }


        /*             exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
                        print_r($output);*/

        return '';
    }


}
