<?php 

namespace App\Http\Controllers;

use App\AdmonUsuarios;
use App\Models\AdmonAjustes;
use App\Models\RRHHDatosMedicos;
use App\Models\RRHHGruposFamiliares;
use Hash, Validator;
use App\Models\RRHHTrabajadoresDetalles;
use App\Models\RRHHTrabajadores;
//use App\Models\AdmonUsuarios;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use PHPJasper\PHPJasper;

class RRHHTrabajadoresController extends Controller
{

    /**
     * Busqueda de empleados
     *
     * @access  public
     * @param
     * @return  json(array)
     */

    public function buscar(Request $request, RRHHTrabajadores $trabajador)
    {
        $trabajador = $trabajador->buscar($request);
        return response()->json([
            'results' => $trabajador
        ]);
    }


    public function buscarTrabajador(Request $request, RRHHTrabajadores $trabajador)
    {
        $trabajador = $trabajador->buscarTrabajador($request);
        return response()->json([
            'results' => $trabajador
        ]);
    }

    /**
     * Obtener una lista de cargos sin ningun filtro
     *
     * @access  public
     * @param
     * @return  json(array)
     */

    public function obtenerTrabajadoresActivos(Request $request, RRHHTrabajadores $trabajadores)
    {
        $trabajadores = RRHHTrabajadores::where('activo', 1)->get();
        return response()->json([
            'status' => 'success',
            'result' => $trabajadores,
            'messages' => null
        ]);
    }


	/**
     * Obtener Lista de empleados con paginación
     *
     * @access  public
     * @param   
     * @return  json(array)
     */

    public function obtener(Request $request, RRHHTrabajadores $trabajador)
    {
        $trabajador = $trabajador->obtenerTrabajadores($request);
        return response()->json([
            'status' => 'success',
            'result' => [
                'total' => $trabajador->total(), 
                'rows' => $trabajador->items()
            ],
            'messages' => null
        ]);
	}
	
    /**
     * Obtener empleado especifico
     *
     * @access  public
     * @param   
     * @return  json(array)
     */

    public function obtenerTrabajador(Request $request, RRHHTrabajadores $trabajador)
    {
        $rules = [
            'id_trabajador' => 'required|integer|min:1'
		];
	

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
		  
			$trabajador = $trabajador->obtenerTrabajador($request);
			if(!empty($trabajador[0])){	
            return response()->json([
                'status' => 'success',
                'result' => $trabajador[0],
                'messages' => null
			]);
		}
		else{
		  return response()->json([
				'status' => 'error',
				'result' => array('id_trabajador'=>["Datos no encontrados"]),
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
     * Registrar nuevo empleado
     *
     * @access 	public
     * @param 	
     * @return 	json(string)
     */

	public function registrar(Request $request)
	{
		$rules = [
			//'codigo' => 'required|string|max:10|unique:pgsql.rrhh.trabajadores,codigo',
			'primer_nombre' => 'required|string|max:50',
            'segundo_nombre' => 'nullable|string|max:50',
			'primer_apellido' => 'required|string|max:50',
			'segundo_apellido' => 'nullable|string|max:50',
            'tipo_contrato' => 'required|string|max:6',
            'imagen' => 'nullable|string|max:255',
            'cargo' => 'required|array|min:1',
            'area' => 'required|array|min:1',
			'cedula' =>  'nullable|string|max:14',

            'sexo' =>  'required',
            'estado_civil' =>  'nullable|string|max:1',
            'municipio' => 'nullable|array|min:1',
			'email' => 'email|nullable|string|max:100',
			'direccion' =>  'nullable|string|max:191',
			'telefono' =>  'nullable|string|max:20',
            'notifica' =>  'nullable|string|max:50',
            'telefono_notifica' =>  'nullable|string|max:50',
            'no_inss' =>  'nullable|integer|min:1',
            'salario_basico' =>  'required|numeric|min:0.01',
            'nivel' =>  'nullable|array|min:1',
            'nivel_estudio' =>  'nullable|array|min:1',
            'id_nomina' =>  'required|integer|min:1',
		];

		$validator = Validator::make($request->all(), $rules);
		if (!$validator->fails()) {

			try{
			DB::beginTransaction();

                $trabajador = new RRHHTrabajadores;
                $codigo_nuevo = $trabajador->obtenerCodigoTrabajador($request->sexo);
                $str_length = 4;
                if($request->sexo == 'M'){

                $str ="L2".substr("0000{$codigo_nuevo['secuencia']}", -$str_length);

                } else if ($request->sexo == 'F')
                {
                    $str ="L1".substr("000{$codigo_nuevo['secuencia']}", -$str_length);
                }
                $trabajador->secuencia = $codigo_nuevo['secuencia'];
                $trabajador->codigo = $str;
                $trabajador->primer_nombre = strtoupper($request->primer_nombre);
                $trabajador->segundo_nombre = strtoupper($request->segundo_nombre);
                $trabajador->primer_apellido = strtoupper($request->primer_apellido);
                $trabajador->segundo_apellido = strtoupper($request->segundo_apellido);
                $trabajador->tipo_contrato = $request->tipo_contrato;
                $trabajador->sexo = $request->sexo;

                if($request->imagen == ''){
                    $trabajador->imagen = '[{"file_name":"imagenproducto.png","file_size":67892,"file_type":"image/png","file_thumbnail":"/public/product-images/imagenproducto.png"}]';
                }else{
                    $trabajador->imagen = $request->imagen;
                }

                $trabajador->id_cargo = $request->cargo['id_cargo'];
                $trabajador->id_area = $request->area['id_area'];
                $trabajador->cedula = $request->cedula;
                $trabajador->id_nivel_academico = $request->nivel['id_nivel_academico'];
                $trabajador->id_niveles_estudios = $request->nivel_estudio['id_niveles_estudios'];
                $trabajador->activo = 1;
                $trabajador->id_nomina = $request->id_nomina;
                $trabajador->save();


                $grupos = new RRHHTrabajadoresDetalles;
                $grupos->id_trabajador = $trabajador->id_trabajador;
                //$grupos->sexo = $request->sexo;
                $grupos->estado_civil = $request->estado_civil;
                $grupos->id_municipio = $request->municipio['id_municipio'];
                $grupos->email = $request->email;
                $grupos->direccion = $request->direccion;
                $grupos->telefono = $request->telefono;
                $grupos->notifica = $request->notifica;
                $grupos->telefono_notifica = $request->telefono_notifica;
                $grupos->no_inss = $request->no_inss;
                $grupos->salario_basico = $request->salario_basico;
                $grupos->fecha_ingreso = $request->fecha_ingreso;
                $grupos->fecha_egreso = null;
                $grupos->save();

                $datos = new RRHHDatosMedicos;
                $datos->id_trabajador = $trabajador->id_trabajador;
                $datos->seguro_inss = $request->seguroInss;
                $datos->seguro_medico = $request->seguroMedico;
                $datos->inss_ipss = $request->inssIpss;
                $datos->inss_ipssrp = $request->inssIpssrp;
                $datos->centro_privado = $request->centroPrivado;
                $datos->grupo_sanguineo = $request->grupoSanguineo;
                $datos->alergia = $request->alergia;
                $datos->alergia_descripcion = $request->alergiaDescripcion;
                $datos->diabetes = $request->diabetes;
                $datos->hipertension = $request->hipertension;
                $datos->cardiaca = $request->cardiaca;
                $datos->peso_libras = $request->pesoLibras;
                $datos->asma = $request->asma;
                $datos->otro_enfermedad = $request->otraEnfermedad;
                $datos->nombre_medico = $request->nombreMedico;
                $datos->telefono_medico = $request->telefonoMedico;
                $datos->contacto_emergencia = $request->contactoEmergencia;
                $datos->telefono_emergencia = $request->telefonoEmergencia;
                $datos->observaciones = $request->observaciones;
                $datos->altura = $request->altura;
                $datos-> save();
                $i = 1;
                foreach ($request->detalleGrupo as $trabajador_grupo) {
                    $grupos = new RRHHGruposFamiliares();
                    $grupos->id_trabajador = $trabajador->id_trabajador;
                  //  $grupos->no_item = $trabajador_grupo['nombres'];
                    $grupos->nombres = $trabajador_grupo['nombres'];
                    $grupos->apellidos = $trabajador_grupo['apellidos'];
                    $grupos->sexo = $trabajador_grupo['sexoGrupo'];
                    $grupos->id_parentesco = $trabajador_grupo['parentesco']['id_parentesco'];
                    $grupos->fecha_nacimiento = $trabajador_grupo['fecha_nacimiento'];
                    $grupos->tipo_identificacion = $trabajador_grupo['tipo_identificacion'];
                    $grupos->no_identificacion = $trabajador_grupo['no_identificacion'];
                    $grupos->no_telefonico = $trabajador_grupo['no_telefonico'];
                    $grupos->estado = 1;
                    $grupos->save();
                    $i++;
                }

                /*$usuario = new AdmonUsuarios;
                $usuario->password = Hash::make($request->password);
                $usuario->usuario = $request->usuario;
                $usuario->id_rol = $request->id_rol;
                $usuario->token = str_random(10);
                $usuario->remember_token = str_random(10);
                $usuario->id_trabajador = $trabajador->id_trabajador;
                $usuario->estado = 1;
                $usuario->save();*/

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
     * @access 	public
     * @param 	
     * @return 	json(string)
     */

	public function actualizar(Request $request)
	{
		$rules = [
			'id_trabajador' => 'required|integer|min:1',
			/*'codigo' => [
                'required',
                'string',
                'max:10',
                Rule::unique('pgsql.rrhh.trabajadores')->ignore($request->id_trabajador,'id_trabajador')
            ],*/
            'primer_nombre' => 'required|string|max:50',
            'segundo_nombre' => 'nullable|string|max:50',
            'primer_apellido' => 'required|string|max:50',
            'segundo_apellido' => 'nullable|string|max:50',
            'tipo_contrato' => 'required|string|max:6',
            'imagen' => 'nullable|string|max:255',
            'trabajador_cargo' => 'required|array|min:1',
            'trabajador_area' => 'required|array|min:1',
            'trabajador_nivel_academico' => 'required|array|min:1',
            'trabajador_nivel_estudio' => 'required|array|min:1',
            'cedula' =>  'required|string|max:14',

            'trabajador_detalles.sexo' =>  'nullable|string|max:1',
            'trabajador_detalles.estado_civil' =>  'nullable|string|max:1',
            'municipio' => 'nullable|array|min:1',
            'trabajador_detalles.email' => 'email|string|max:100',
            'trabajador_detalles.direccion' =>  'nullable|string|max:191',
            'trabajador_detalles.telefono' =>  'nullable|string|max:20',
            'trabajador_detalles.notifica' =>  'nullable|string|max:50',
            'trabajador_detalles.telefono_notifica' =>  'nullable|string|max:50',
            'trabajador_detalles.no_inss' =>  'required|integer|min:1',
            'trabajador_detalles.salario_basico' =>  'required|numeric|min:0.01',

            'trabajador_datos_medicos.seguro_inss' => 'nullable|integer',
            'trabajador_datos_medicos.seguro_medico' => 'nullable|integer',
            'trabajador_datos_medicos.inss_ipss' => 'nullable|string',
            'trabajador_datos_medicos.inss_ipssrp' => 'nullable|string',
            'trabajador_datos_medicos.centro_privado' => 'nullable|string|max:100',
            'trabajador_datos_medicos.grupo_sanguineo' => 'nullable|integer',
            'trabajador_datos_medicos.alergia' => 'nullable|integer',
            'trabajador_datos_medicos.alergia_descripcion' => 'nullable|string|max:250',
            'trabajador_datos_medicos.diabetes' => 'nullable|integer',
            'trabajador_datos_medicos.hipertension' => 'nullable|integer',
            'trabajador_datos_medicos.cardiaca' => 'nullable|integer',
            'trabajador_datos_medicos.peso_libra' => 'nullable|numeric',
            'trabajador_datos_medicos.asma' => 'nullable|integer',
            'trabajador_datos_medicos.otro_enfermedad' => 'nullable|string|max:200',
            'trabajador_datos_medicos.nombre_medico' => 'nullable|string|max:200',
            'trabajador_datos_medicos.telefono_medico' => 'nullable|string|max:10',
            'trabajador_datos_medicos.contacto_emergencia' => 'nullable|string|max:50',
            'trabajador_datos_medicos.telefono_emergencia' => 'nullable|string|max:50',
            'trabajador_datos_medicos.observaciones' => 'nullable|string|max:150',
            'trabajador_datos_medicos.altura' => 'nullable|numeric',
		];
	
		
		$validator = Validator::make($request->all(), $rules);
		if (!$validator->fails()) {

			try{
			DB::beginTransaction();

			/*$usuario = AdmonUsuarios::findOrFail($request->id_usuario);
			if($request->password!=''){
			$usuario->password = Hash::make($request->password);
			}
			$usuario->id_rol = $request->id_rol;
			$usuario->save();*/

			$trabajador = RRHHTrabajadores::findOrFail($request->id_trabajador);
            //$trabajador->codigo = $request->codigo;
            $trabajador->primer_nombre = strtoupper($request->primer_nombre);
            $trabajador->segundo_nombre = strtoupper($request->segundo_nombre);
            $trabajador->primer_apellido = strtoupper($request->primer_apellido);
            $trabajador->segundo_apellido =strtoupper ($request->segundo_apellido);
            $trabajador->tipo_contrato = $request->tipo_contrato;


                if($request->imagen == ''){
                    $trabajador->imagen = '[{"file_name":"imagenproducto.png","file_size":67892,"file_type":"image/png","file_thumbnail":"/public/product-images/imagenproducto.png"}]';
                }else{
                    $trabajador->imagen = $request->imagen;
                }

            $trabajador->id_cargo = $request->trabajador_cargo['id_cargo'];
            $trabajador->id_area = $request->trabajador_area['id_area'];
            $trabajador->id_nivel_academico = $request->trabajador_nivel_academico['id_nivel_academico'];
            $trabajador->id_niveles_estudios = $request->trabajador_nivel_estudio['id_niveles_estudios'];
            $trabajador->cedula = $request->cedula;
            $trabajador->id_nomina = $request->id_nomina;
            $trabajador->u_modificacion = Auth::user()->usuario;
			$trabajador->save();

			$grupos = RRHHTrabajadoresDetalles::where('id_trabajador', $trabajador->id_trabajador)->first();
            $grupos->sexo = $request->trabajador_detalles['sexo'];
            $grupos->estado_civil = $request->trabajador_detalles['estado_civil'];
            $grupos->id_municipio = $request->municipio['id_municipio'];
            $grupos->email = $request->trabajador_detalles['email'];

            $grupos->direccion = $request->trabajador_detalles['direccion'];
            $grupos->telefono = $request->trabajador_detalles['telefono'];
            $grupos->notifica = $request->trabajador_detalles['notifica'];
            $grupos->telefono_notifica = $request->trabajador_detalles['telefono_notifica'];
            $grupos->no_inss = $request->trabajador_detalles['no_inss'];
            $grupos->salario_basico = $request->trabajador_detalles['salario_basico'];
            $grupos->fecha_ingreso = $request->trabajador_detalles['fecha_ingreso'];
            $grupos->fecha_egreso = $request->trabajador_detalles['fecha_egreso'];
			$grupos->save();

			$usuario = AdmonUsuarios::where('id_empleado',$trabajador->id_trabajador)->first();
			if(!empty($usuario)){
                $usuario->email = $grupos->email;
                $usuario->save();
            }

                $datos = RRHHDatosMedicos::where('id_trabajador', $trabajador->id_trabajador)->first();
                $datos->seguro_inss = $request->trabajador_datos_medicos['seguro_inss'];
                $datos->seguro_medico = $request->trabajador_datos_medicos['seguro_medico'];
                $datos->inss_ipss = $request->trabajador_datos_medicos['inss_ipss'];
                $datos->inss_ipssrp = $request->trabajador_datos_medicos['inss_ipssrp'];
                $datos->centro_privado = $request->trabajador_datos_medicos['centro_privado'];
                $datos->grupo_sanguineo = $request->trabajador_datos_medicos['grupo_sanguineo'];
                $datos->alergia = $request->trabajador_datos_medicos['alergia'];
                $datos->alergia_descripcion = $request->trabajador_datos_medicos['alergia_descripcion'];
                $datos->diabetes = $request->trabajador_datos_medicos['diabetes'];
                $datos->hipertension = $request->trabajador_datos_medicos['hipertension'];
                $datos->cardiaca = $request->trabajador_datos_medicos['cardiaca'];
                $datos->peso_libras = $request->trabajador_datos_medicos['peso_libras'];
                $datos->asma = $request->trabajador_datos_medicos['asma'];
                $datos->otro_enfermedad = $request->trabajador_datos_medicos['otro_enfermedad'];
                $datos->nombre_medico = $request->trabajador_datos_medicos['nombre_medico'];
                $datos->telefono_medico = $request->trabajador_datos_medicos['telefono_medico'];
                $datos->contacto_emergencia = $request->trabajador_datos_medicos['contacto_emergencia'];
                $datos->telefono_emergencia = $request->trabajador_datos_medicos['telefono_emergencia'];
                $datos->observaciones = $request->trabajador_datos_medicos['observaciones'];
                $datos->altura = $request->trabajador_datos_medicos['altura'];
                $datos-> save();

                RRHHGruposFamiliares::where('id_trabajador',$trabajador->id_trabajador)->delete();
                $i = 1;
                foreach ($request->trabajador_grupo_familiar as $trabajador_grupo) {
                    $grupos = new RRHHGruposFamiliares();
                    $grupos->id_trabajador = $trabajador->id_trabajador;
                    //  $grupos->no_item = $trabajador_grupo['nombres'];
                    $grupos->nombres = $trabajador_grupo['nombres'];
                    $grupos->apellidos = $trabajador_grupo['apellidos'];
                    $grupos->sexo = $trabajador_grupo['sexo'];
                    $grupos->id_parentesco = $trabajador_grupo['id_parentesco'];
                    $grupos->fecha_nacimiento = $trabajador_grupo['fecha_nacimiento'];
                    $grupos->tipo_identificacion = $trabajador_grupo['tipo_identificacion'];
                    $grupos->no_identificacion = $trabajador_grupo['no_identificacion'];
                    $grupos->no_telefonico = $trabajador_grupo['no_telefonico'];
                    $grupos->estado = 1;
                    $grupos->save();
                    $i++;
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

    public function desactivar(Request $request)
    {
        $rules = [
            'id_trabajador' => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $trabajador = RRHHTrabajadores::find($request->id_trabajador);
            $trabajador->activo = 0;
            $trabajador->u_modificacion = Auth::user()->usuario;
            $trabajador->save();
            $trabajador_detalle = RRHHTrabajadoresDetalles::find($request->id_trabajador);
            $trabajador_detalle->fecha_egreso = date('Y-m-d');
            $trabajador_detalle->save();

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


    public function activar(Request $request)
    {
        $rules = [
            'id_trabajador' => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $trabajador = RRHHTrabajadores::find($request->id_trabajador);
            $trabajador->activo = 1;
            $trabajador->u_modificacion = Auth::user()->usuario;
            $trabajador->save();

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

    public function reporteExpedientePersonal($id_trabajador)
    {
        // echo $ext;
        $ext = 'pdf';
        $os = array("pdf");
        if (in_array($ext, $os)) {
            $hora_actual = time() ;
            //$input = 'C:/xampp/htdocs/resources/reports/ReporteExpedientePersonal';
            //$output = 'C:/xampp/htdocs/resources/reports/' .$hora_actual . 'ReporteExpedientePersonal';
            $input = '/var/www/html/resources/reports/ReporteExpedientePersonal';
            $output = '/var/www/html/resources/reports/'.$hora_actual.'ReporteExpedientePersonal';
            $nombre_empresa = AdmonAjustes::where('id_ajuste',4)->select('valor')->first();
            $logo_empresa = AdmonAjustes::where('id_ajuste',3)->select(DB::raw("substr(valor, 2, length(valor) - 2)::json->>'file_thumbnail' as file_thumbnail"))->first();
            $url = '/var/www/html/resources/reports/';

            $options = [
                'format' => [$ext],
                'locale' => 'en',
                'params' => [
                    'id_trabajador' => $id_trabajador,
                    'empresa_nombre' => $nombre_empresa->valor,
                    'empresa_logo' =>  env('APP_URL_REPORTS').$logo_empresa->file_thumbnail],
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

            return response()->download($output . '.' . $ext ,$hora_actual. 'ExpedientePersonal.' . $ext, $headers);
        }else{
            return '';
        }
    }

}