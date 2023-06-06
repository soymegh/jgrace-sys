<?php 

namespace App\Http\Controllers;

use App\Models\AdmonAjustes;
use App\Models\PublicDepartamentos;
use App\Models\PublicMunicipios;
use App\Models\RRHHContratoGeneralInterno;
use App\Models\RRHHContratoGeneralMerecedor;
use App\Models\RRHHContratoSolicitud;
use App\Models\RRHHNivelesEstudios;
use App\Models\RRHHContratoTipos;
use Illuminate\Support\Facades\DB;
use PHPJasper\PHPJasper;
use Validator,Auth;
use App\Models\RRHHNivelesAcademicos;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class RRHHContratoSolicitudController extends Controller
{
    /**
     * Obtener una lista de Roles (con opcion de busqueda y paginacion)
     *
     * @access  public
     * @param Request $request
     * @param RRHHContratoSolicitud $contrato
     * @return  json(array)
     */

    public function obtener(Request $request, RRHHContratoSolicitud $contrato)
    {
        $contrato = $contrato->obtenerContratosSolicitud($request);
        return response()->json([
            'status' => 'success',
            'result' => [
                'total' => $contrato->total(), 
                'rows' => $contrato->items()
            ],
            'messages' => null
        ]);
    }

    /**
     * Obtener una lista de zonas sin paginacion
     *
     * @access  public
     * @param Request $request
     * @param RRHHContratoSolicitud $contrato
     * @return  json(array)
     */

    public function obtenerTodas(Request $request, RRHHContratoSolicitud $contrato)
    {
        $contrato = RRHHContratoSolicitud::orderby('id_contrato_solicitud')->get();
        return response()->json([
            'status' => 'success',
            'result' => $contrato,
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

    public function obtenerContratoSolicitud(Request $request)
    {
        $rules = [
            'id_contrato_solicitud'=> 'required|integer|min:1'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $contrato = RRHHContratoSolicitud::where('id_contrato_solicitud',$request->id_contrato_solicitud)->with('solicitudContratoInterno','solicitudContratoMerecedor','solicitudContratoDepartamento','solicitudContratoTipo')->first();
            $contrato_interno = RRHHContratoGeneralInterno::select('id_contrato_dgeneral_interno','nombre_representante','caracter_cargo','caracter_legal',DB::raw("CONCAT((select t.descripcion from rrhh.tipos_actos_juridicos t where t.id_tipo_acto_juridico = rrhh.contratos_dgenerales_internos.id_tipo_acto_juridico),'(',nombre_representante,')') AS representante_acto"))->get();
            $contrato_merecedor = RRHHContratoGeneralMerecedor::select('id_contrato_dgeneral_merecedor','nombre_representante','caracter_cargo','caracter_legal',DB::raw("CONCAT((select t.descripcion from rrhh.tipos_actos_juridicos t where t.id_tipo_acto_juridico = rrhh.contratos_dgenerales_merecedores.id_tipo_acto_juridico),'(',nombre_representante,')') AS representante_acto"))->get();
            $departamento = PublicDepartamentos::select('id_departamento','descripcion')->get();
            $contrato_tipo = RRHHContratoTipos::select('id_contrato_tipo','descripcion')->get();

            if(!empty($contrato)){	
            return response()->json([
                'status' => 'success',
                'result' => [
                    'contrato' => $contrato,
                    'contratos_interno' => $contrato_interno,
                    'contratos_merecedor' => $contrato_merecedor,
                    'departamentos' => $departamento,
                    'contratos_tipo' => $contrato_tipo,
                ],
                'messages' => null
            ]);

        }
		else{
		  return response()->json([
				'status' => 'error',
				'result' => array('id_contrato_solicitud'=>["Datos no encontrados"]),
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
                'interno' => 'array|required|min:1',
              //  'id_contrato' => 'unique|required',
                'merecedor'=> 'array|required|min:1',
                'monto' => 'numeric|regex:/^\d*(\.\d{1,2})?$/|min:0',
                'tipo' => 'array|required|min:1',
                'descripcion_servicio' => 'string|required',
                'plazo_ejecucion' => 'integer|required',
                'departamento' => 'array|required|min:1',
                'fecha_inicio' => 'date|required',
                'fecha_fin' => 'date|required',
                'observacion' => 'string|required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            
            $secuencia = RRHHContratoSolicitud::max('id_contrato') + 1;
            $contrato = new RRHHContratoSolicitud;
            $contrato->id_contrato = 'CO-' . $secuencia;
            $contrato->id_contratos_dgeneral_interno = $request->interno['id_contrato_dgeneral_interno'];
            $contrato->id_contratos_dgeneral_merecedor = $request->merecedor['id_contrato_dgeneral_merecedor'];
            $contrato->monto = $request->monto;
            $contrato->id_contrato_tipo = $request->tipo['id_contrato_tipo'];
            $contrato->descripcion_servicio = $request->descripcion_servicio;
            $contrato->plazo_ejecucion = $request->plazo_ejecucion;
            $contrato->id_departamento = $request->departamento['id_departamento'];
            $contrato->f_inicio_contrato = $request->fecha_inicio;
            $contrato->f_fin_contrato = $request->fecha_fin;
            $contrato->observacion = $request->observacion;
            $contrato->u_grabacion = Auth::user()->usuario;
            $contrato->estado = 1;
            $contrato->save();

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
            'id_contrato_solicitud' => 'required|integer|min:1'

        ];



        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {

            $contrato = RRHHContratoSolicitud::find($request->id_contrato_solicitud);
            $contrato->id_contratos_dgeneral_interno = $request->solicitud_contrato_interno['id_contrato_dgeneral_interno'];
            $contrato->id_contratos_dgeneral_merecedor = $request->solicitud_contrato_merecedor['id_contrato_dgeneral_merecedor'];
            $contrato->monto = $request->monto;
            $contrato->id_contrato_tipo = $request->solicitud_contrato_tipo['id_contrato_tipo'];
            $contrato->descripcion_servicio = $request->descripcion_servicio;
            $contrato->plazo_ejecucion = $request->plazo_ejecucion;
            $contrato->id_departamento = $request->solicitud_contrato_departamento['id_departamento'];
            $contrato->f_inicio_contrato = $request->fecha_inicio;
            $contrato->f_fin_contrato = $request->fecha_fin;
            $contrato->observacion = $request->observacion;
            $contrato->u_grabacion = Auth::user()->usuario;
            $contrato->estado = 1;
            $contrato->save();

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
            'id_zona' => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $contrato = PublicZonas::find($request->id_zona);
            $contrato->activo = 0;
            $contrato->save();

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
            'id_zona' => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $rol = PublicZonas::find($request->id_zona);
            $rol->activo = 1;
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

    public function generarReporte($id_contrato_solicitud)
    {
        // echo $ext;
        $ext = 'pdf';
        $os = array("xls", "pdf");
        if (in_array($ext, $os)) {
            $hora_actual = time() ;
            //$input = 'C:/xampp/htdocs/resources/reports/ContratoConsignación';
            //$output = 'C:/xampp/htdocs/resources/reports/' .$hora_actual . 'ContratoConsignación';
            $input = '/var/www/html/resources/reports/ContratoConsignacion';
            $output = '/var/www/html/resources/reports/'.$hora_actual.'ContratoConsignacion';
            $nombre_empresa = AdmonAjustes::where('id_ajuste',4)->select('valor')->first();
            $logo_empresa = AdmonAjustes::where('id_ajuste',3)->select(DB::raw("substr(valor, 2, length(valor) - 2)::json->>'file_thumbnail' as file_thumbnail"))->first();
            $url = '/var/www/html/resources/reports/';

            $options = [
                'format' => [$ext],
                'locale' => 'en',
                'params' => [
                    'id_contrato_solicitud' => $id_contrato_solicitud,
                    'empresa_nombre' => $nombre_empresa->valor,
                   // 'empresa_logo' =>  env('APP_URL_REPORTS').$logo_empresa->file_thumbnail
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

            return response()->download($output . '.' . $ext ,$hora_actual. 'FormatoContratoSolicitud.' . $ext, $headers);

           /* exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
               print_r($output);*/
        }
    }

}