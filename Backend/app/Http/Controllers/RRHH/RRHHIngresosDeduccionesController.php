<?php 

namespace App\Http\Controllers;

use App\Models\ContabilidadCuentasContables;
use App\Models\PublicDepartamentos;
use App\Models\PublicMunicipios;
use App\Models\RRHHContratoGeneralInterno;
use App\Models\RRHHContratoGeneralMerecedor;
use App\Models\RRHHContratoSolicitud;
use App\Models\RRHHIngresosDeducciones;
use App\Models\RRHHNivelesEstudios;
use App\Models\RRHHContratoTipos;
use Illuminate\Support\Facades\DB;
use PHPJasper\PHPJasper;
use Validator,Auth;
use App\Models\RRHHNivelesAcademicos;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class RRHHIngresosDeduccionesController extends Controller
{
    /**
     * Obtener una lista de Roles (con opcion de busqueda y paginacion)
     *
     * @access  public
     * @param Request $request
     * @param RRHHIngresosDeducciones $deduccion
     * @return  json(array)
     */

    public function obtenerIngresos(Request $request, RRHHIngresosDeducciones $deduccion)
    {
        $deduccion = $deduccion->obtenerIngresos($request);
        return response()->json([
            'status' => 'success',
            'result' => [
                'total' => $deduccion->total(), 
                'rows' => $deduccion->items()
            ],
            'messages' => null
        ]);
    }
    public function obtenerIngresosDeducciones(Request $request, RRHHIngresosDeducciones $deduccion)
    {
        $deduccion = $deduccion->obtenerIngresosDeducciones($request);
        return response()->json([
            'status' => 'success',
            'result' => [
                'total' => $deduccion->total(),
                'rows' => $deduccion->items()
            ],
            'messages' => null
        ]);
    }
    public function obtenerDeducciones(Request $request, RRHHIngresosDeducciones $deduccion)
    {
        $deduccion = $deduccion->obtenerDeducciones($request);
        return response()->json([
            'status' => 'success',
            'result' => [
                'total' => $deduccion->total(),
                'rows' => $deduccion->items()
            ],
            'messages' => null
        ]);
    }

    /**
     * Obtener una lista de zonas sin paginacion
     *
     * @access  public
     * @param Request $request
     * @param RRHHIngresosDeducciones $deduccion
     * @return  json(array)
     */

    public function obtenerTodas(Request $request, RRHHIngresosDeducciones $deduccion)
    {
        $deduccion = RRHHIngresosDeducciones::orderby('id_cat_ingreso_deduccion')->get();
        return response()->json([
            'status' => 'success',
            'result' => $deduccion,
            'messages' => null
        ]);
    }

    /**
     * obtener Rol Especifico
     *
     * @access  public
     * @param   
     * @return  json(array)
     */

    public function obtenerIngresoDeduccion(Request $request)
    {
        $rules = [
            'id_cat_ingreso_deduccion'=> 'required|integer|min:1'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $deduccion = RRHHIngresosDeducciones::where('id_cat_ingreso_deduccion',$request->id_cat_ingreso_deduccion)->with('ingresoDeduccionCuentaContable','ingresoDeduccionCuentaContableAdministrativa')->first();
            $cuenta_contable = ContabilidadCuentasContables::select('id_cuenta_contable','nombre_cuenta','codigo_cuenta')->get();
            if(!empty($deduccion)){
            return response()->json([
                'status' => 'success',
                'result' => [
                    'deduccion' => $deduccion,
                    'cuenta_contable' => $cuenta_contable,
                ],
                'messages' => null
            ]);

        }
		else{
		  return response()->json([
				'status' => 'error',
				'result' => array('id_cat_ingreso_deduccion'=>["Datos no encontrados"]),
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
     * @return  json(string)
     */

    public function registrar(Request $request)
    {
        $rules = [
                    'cve_ingreso_deduccion' => 'required|string|max:1',
                    'descripcion' => 'required|string|max:250',
                    'orden' => 'required|integer|min:1',
                    'abreviacion' => [
                        'required',
                        'string',
                        'max:6',
                        Rule::unique('pgsql.rrhh.cat_ingresos_deducciones')],
                    'cuenta_contable.id_cuenta_contable' => 'required|integer|min:1',
                    'cuenta_contable_administrativa.id_cuenta_contable' => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {

            $secuencia = RRHHIngresosDeducciones::select(DB::raw('max(codigo) as codigo' )) -> where('cve_ingreso_deduccion',$request->cve_ingreso_deduccion)->first();
            $deduccion = new RRHHIngresosDeducciones();
            $deduccion->cve_ingreso_deduccion = $request->cve_ingreso_deduccion;
            $deduccion->codigo = $secuencia['codigo'] + 1;
            $deduccion->descripcion = strtoupper($request->descripcion);
            $deduccion->orden = $request->orden;
            $deduccion->abreviacion = strtoupper($request->abreviacion);
            $deduccion->id_cuenta_contable_venta = $request->cuenta_contable['id_cuenta_contable'];
            $deduccion->id_cuenta_contable_administrativa = $request->cuenta_contable_administrativa['id_cuenta_contable'];
            $deduccion->registro_manual = $request->registro_manual;
            $deduccion->grabable = $request->grabable;
            $deduccion->u_grabacion = Auth::user()->usuario;
            $deduccion->estado = 1;
            $deduccion->save();

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
     * @return  json(string)
     */

    public function actualizar(Request $request)
    {
        $rules = [

            'id_cat_ingreso_deduccion' => 'required|integer|min:1',
            'ingreso_deduccion_cuenta_contable.id_cuenta_contable' => 'required|integer|min:1',
            'ingreso_deduccion_cuenta_contable_administrativa.id_cuenta_contable' => 'required|integer|min:1',

            'id_cat_ingreso_deduccion' => 'required|integer|min:1'


        ];



        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {

            $deduccion = RRHHIngresosDeducciones::find($request->id_cat_ingreso_deduccion);
            $deduccion->cve_ingreso_deduccion = $request->cve_ingreso_deduccion;
            $deduccion->descripcion = strtoupper($request->descripcion);
            $deduccion->orden = $request->orden;
            $deduccion->abreviacion = strtoupper($request->abreviacion);
            $deduccion->id_cuenta_contable_venta = $request->ingreso_deduccion_cuenta_contable['id_cuenta_contable'];
            $deduccion->id_cuenta_contable_administrativa = $request->ingreso_deduccion_cuenta_contable_administrativa['id_cuenta_contable'];
            $deduccion->registro_manual = $request->registro_manual;
            $deduccion->grabable = $request->grabable;
            $deduccion->u_modificacion = Auth::user()->usuario;
            $deduccion->estado = 1;
            $deduccion->save();

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
     * @return  json(string)
     */

    public function desactivar(Request $request)
    {
        $rules = [
            'id_cat_ingreso_deduccion' => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $deduccion = RRHHIngresosDeducciones::find($request->id_cat_ingreso_deduccion);
            $deduccion->estado = 0;
            $deduccion->save();

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
     * @return  json(string)
     */

    public function activar(Request $request)
    {
        $rules = [
            'id_cat_ingreso_deduccion' => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $rol = RRHHIngresosDeducciones::find($request->id_cat_ingreso_deduccion);
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
    public function nueva(Request $request)
    {
        $cuentas_contables = ContabilidadCuentasContablesVista::select('id_cuenta_contable','cta_contable','nombre_cuenta_completo')->get();


        return response()->json([
            'status' => 'success',
            'result' => [
                'cuentas_contables' => $cuentas_contables,
            ],
            'messages' => null
        ]);

    }

    public function generarReporte($ext)
    {
        // echo $ext;
        // $ext = 'pdf';
        $os = array("xls", "pdf");
        if (in_array($ext, $os)) {
            $hora_actual = time() ;
            //$input = 'C:/xampp/htdocs/resources/reports/ReporteZonas';
            //$output = 'C:/xampp/htdocs/resources/reports/' .$hora_actual . 'ReporteZonas';
             $input = '/var/www/html/resources/reports/ReporteZonas';
             $output = '/var/www/html/resources/reports/'.$hora_actual.'ReporteZonas';

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

            return response()->download($output . '.' . $ext ,$hora_actual. 'ReporteZonas.' . $ext, $headers);

            /*exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
               print_r($output);*/
        }
    }

}