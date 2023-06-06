<?php

namespace App\Http\Controllers\Contabilidad;

use App\Http\Controllers\Controller;
use App\Models\Admon\Ajustes;
use App\Models\Admon\UsuariosEmpresas;
use App\Models\Contabilidad\CierresMensuales;
use App\Models\Contabilidad\PeriodosFiscales;
use Hash;
use App\Models\Contabilidad\CuentasContables;
use App\Models\Contabilidad\CuentasContablesVista;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use PHPJasper\PHPJasper;
class CierresMensualesController extends Controller
{

    /**
     * Busqueda de Cuentas Contables
     *
     * @access  public
     * @param
     * @return JsonResponse
     */

    public function buscarCuentasContables(Request $request, CuentasContablesVista $cuentas_contables)
    {
        $cuentas_contables = $cuentas_contables->buscarCuentasContables($request);
        return response()->json([
            'results' => $cuentas_contables
        ]);
    }


    /**
     * Busqueda de Cuentas Contables full
     *
     * @access  public
     * @param
     * @return  json(array)
     */

    public function buscarCuentasContablesF(Request $request, CuentasContablesVista $cuentas_contables)
    {
        $cuentas_contables = $cuentas_contables->buscarCuentasContablesF($request);
        return response()->json([
            'results' => $cuentas_contables
        ]);
    }

    /**
     * Get List of Productos
     *
     * @access  public
     * @param Request $request
     * @param CuentasContablesVista $cuentas_contables
     * @return JsonResponse
     */

    public function obtenerCuentasContables(Request $request, CuentasContablesVista $cuentas_contables)
    {
        //echo gethostname();
        $cuentas_contables = $cuentas_contables->obtenerCuentasContables($request);
        return response()->json([
            'status' => 'success',
            'result' => [
                'total' => $cuentas_contables->total(),
                'rows' => $cuentas_contables->items()
            ],
            'messages' => null
        ]);
    }

    /**
     * Get Single User
     *
     * @access  public
     * @param Request $request
     * @param CuentasContables $cuenta_contable
     * @return JsonResponse
     */

    public function obtenerCuentaContable(Request $request, CuentasContables $cuenta_contable)
    {
        $rules = [
            'id_cuenta_contable' => 'required|integer|min:1'
        ];


        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {

            $cuenta_contable = $cuenta_contable->obtenerCuentaContable($request);

            if(!empty($cuenta_contable)){
                return response()->json([
                    'status' => 'success',
                    'result' => $cuenta_contable,
                    'messages' => null
                ]);
            }
            else{
                return response()->json([
                    'status' => 'error',
                    'result' => array('id_cuenta_contable'=>["Datos no encontrados"]),
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
     * Get Single User
     *
     * @access  public
     * @param Request $request
     * @param CuentasContablesVista $cuenta_contable
     * @return JsonResponse
     */

    public function obtenerCuentaContableV(Request $request, CuentasContablesVista $cuenta_contable)
    {
        $rules = [
            'id_cuenta_contable' => 'required|integer|min:1'
        ];


        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {

            $cuenta_contable = $cuenta_contable->obtenerCuentaContable($request);

            if(!empty($cuenta_contable[0])){
                return response()->json([
                    'status' => 'success',
                    'result' => $cuenta_contable[0],
                    'messages' => null
                ]);
            }
            else{
                return response()->json([
                    'status' => 'error',
                    'result' => array('id_cuenta_contable'=>["Datos no encontrados"]),
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
     * Obtener una lista de cuentas contables sin ningun filtro
     *
     * @access  public
     * @param
     * @return JsonResponse
     */

    public function obtenerTodasCuentasContables(Request $request, CuentasContablesVista $cuentas_contables)
    {
        $cuentas_contables = CuentasContablesVista::orderBy('cta_contable')->get();
        return response()->json([
            'status' => 'success',
            'result' => $cuentas_contables,
            'messages' => null
        ]);
    }


    /**
     * Obtener una lista de cuentas por nivel
     *
     * @access  public
     * @param
     * @return JsonResponse
     */

    public function obtenerCuentasContablesNivel(Request $request, CuentasContablesVista $cuentas_contables)
    {
        $cuentas_contables = $cuentas_contables->obtenerCuentasContablesNivel($request);
        return response()->json([
            'status' => 'success',
            'result' => $cuentas_contables,
            'messages' => null
        ]);
    }



    /**
     * Create a New User
     *
     * @access 	public
     * @param
     * @return JsonResponse
     */

    public function guardarCuentaContable(Request $request)
    {

        $rules = [
            'nombre_cuenta' => 'required|string|max:100',
            'cta_contable' => 'required|string|max:20',
            'nivel_cuenta' => 'required|array|min:1',
            'permite_movimiento' => 'required|boolean',
            'cuenta_padre' => 'required|array|min:1',
            'tipo_cuenta' => 'required|array|min:1',
            //'cuenta_padre.*.id_cuenta_contable' => 'required|integer',
        ];

        if(!empty($request->cuenta_padre['id_cuenta_contable'])){
            $rules['codigo_cuenta'] = ['required','integer','max:999','min:1',
                Rule::unique('pgsql.contabilidad.cuentas_contables')->where(function ($query) use ($request) {
                    return $query->where('codigo_cuenta',$request->codigo_cuenta)
                        ->where('id_cuenta_padre',$request->cuenta_padre['id_cuenta_contable']);
                }),
            ];
            //print_r($rules2[0]) ;
        }
        //print_r($rules[0]);

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {

            try{
                DB::beginTransaction();
                $cuenta_contable = new CuentasContables;

                $cuenta_contable->nombre_cuenta = $request->nombre_cuenta;

                $cuenta_contable->cta_contable = $request->cta_contable;



                /* if((!empty($request->codigo_cuenta)) && $request->codigo_cuenta > 0 && $request->codigo_cuenta < 10 && ($request->nivel_cuenta['id_nivel_cuenta'] > 2))
                 {
                     $cuenta_contable->codigo_cuenta = '0'.$request->codigo_cuenta;
                 }else{
                     $cuenta_contable->codigo_cuenta = $request->codigo_cuenta;
                 }*/
                $cuenta_contable->codigo_cuenta = $request->codigo_cuenta;

                $cuenta_contable->permite_movimiento = $request->permite_movimiento;
                $cuenta_contable->usuario_registra = Auth::user()->usuario;

                $cuenta_contable->id_cuenta_padre = $request->cuenta_padre['id_cuenta_contable'];
                $cuenta_contable->id_tipo_cuenta = $request->tipo_cuenta['id_tipo_cuenta'];

                /*if(!empty($request->anexo)){
                $cuenta_contable->id_anexo = $request->anexo['id_anexo'];
                }*/

                $cuenta_contable->id_nivel_cuenta = $request->nivel_cuenta['id_nivel_cuenta'];
                $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
                $cuenta_contable->id_empresa = $usuario_empresa->id_empresa;
                $cuenta_contable->usuario_registra = Auth::user()->name;
                $cuenta_contable->estado = 1;
                $cuenta_contable->save();

                $periodo = PeriodosFiscales::where('estado',0)->first();
                //print_r($periodo);
                if(!empty($periodo)){
                    $cierres = new CierresMensuales();
                    $cierres->id_periodo = $periodo->id_periodo_fiscal;
                    $cierres->id_cuenta_contable = $cuenta_contable->id_cuenta_contable;
                    $cierres->saldoperiodoanterior=0;
                    $cierres->saldo1=0;
                    $cierres->saldo2=0;
                    $cierres->saldo3=0;
                    $cierres->saldo4=0;
                    $cierres->saldo5=0;
                    $cierres->saldo6=0;
                    $cierres->saldo7=0;
                    $cierres->saldo8=0;
                    $cierres->saldo9=0;
                    $cierres->saldo10=0;
                    $cierres->saldo11=0;
                    $cierres->saldo12=0;
                    $cierres->save();
                }

                DB::commit();
                return response()->json([
                    'status' => 'success',
                    'result' => null,
                    'messages' => null
                ]);
            } catch (Exception $e){
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'result' => 'Error de base de datos',
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
     * Update Existing User
     *
     * @access    public
     * @param Request $request
     * @return JsonResponse
     */

    public function actualizarCuentaContable(Request $request)
    {
        $rules = [
            'id_cuenta_contable' => 'required|integer|min:1',
            'nombre_cuenta' => 'required|string|max:100',
            'permite_movimiento' => 'required|boolean',
            'cta_contable' => 'required|string|max:20',
        ];


        if(!empty($request->cuenta_padre['id_cuenta_contable'])){
            $rules['codigo_cuenta'] = ['required','integer','max:99','min:1',
                Rule::unique('pgsql.contabilidad.cuentas_contables')->where(function ($query) use ($request) {
                    return $query->where('codigo_cuenta',$request->codigo_cuenta)
                        ->where('id_cuenta_padre',$request->cuenta_padre['id_cuenta_contable']);
                })->ignore($request->id_cuenta_contable,'id_cuenta_contable'),
            ];
        }


        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {

            try{
                DB::beginTransaction();
                $cuenta_contable = CuentasContables::findOrFail($request->id_cuenta_contable);

                $cuenta_contable->nombre_cuenta = $request->nombre_cuenta;
                $cuenta_contable->cta_contable = $request->cta_contable;
                /*  if((!empty($request->codigo_cuenta)) && $request->codigo_cuenta > 0 && $request->codigo_cuenta < 10 && ($cuenta_contable->id_nivel_cuenta > 2))
                  {
                      $cuenta_contable->codigo_cuenta = '0'.$request->codigo_cuenta;
                  }else{
                      $cuenta_contable->codigo_cuenta = $request->codigo_cuenta;
                  }*/
                $cuenta_contable->codigo_cuenta = $request->codigo_cuenta;
                $cuenta_contable->permite_movimiento = $request->permite_movimiento;

                /*if(!empty($request->cuenta_anexo)){
                    $cuenta_contable->id_anexo = $request->cuenta_anexo['id_anexo'];
                }else{
                    $cuenta_contable->id_anexo = null;
                }*/

                $cuenta_contable->save();

                DB::commit();
                return response()->json([
                    'status' => 'success',
                    'result' => null,
                    'messages' => null
                ]);
            } catch (Exception $e){
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'result' => 'Error de base de datos',
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
     * Activa Producto
     *
     * @access  public
     * @param
     * @return  json(string)
     */
    public function activarCuentaContable(Request $request)
    {
        $rules = [
            'id_cuenta_contable' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $cuenta_contable = CuentasContables::find($request->id_cuenta_contable);
            $cuenta_contable->estado = 1;
            $cuenta_contable->save();

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
     * Desactiva Producto
     *
     * @access  public
     * @param
     * @return  json(string)
     */
    public function desactivarCuentaContable(Request $request)
    {
        $rules = [
            'id_cuenta_contable' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $cuenta_contable = CuentasContables::find($request->id_cuenta_contable);
            $cuenta_contable->estado = 0;
            $cuenta_contable->save();

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
     * Generar Reporte Jasper
     *
     * @access  public
     * @param
     */
    public function generarReporte($ext, Request $request)
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
//            $input = env('APP_URL_REPORTES') . 'CatalogoCuentasContables';
//            $output = env('APP_URL_REPORTES') . $hora_actual .  'CatalogoCuentasContables';

            if($ext == 'xls'){
                $input = $input.'XLS.jasper';
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

            return response()->download($output . '.' . $ext ,$hora_actual. 'CuentasContables.' . $ext, $headers);

            /* exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
                print_r($output);*/
        }
    }
}
