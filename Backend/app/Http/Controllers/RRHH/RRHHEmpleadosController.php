<?php 

namespace App\Http\Controllers;

use Hash, Validator;
use App\Models\RRHHPersonas;
use App\Models\RRHHEmpleados;
use App\Models\AdmonUsuarios;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
class RRHHEmpleadosController extends Controller
{
	/**
     * Obtener Lista de empleados con paginación
     *
     * @access  public
     * @param   
     * @return  json(array)
     */

    public function obtenerEmpleados(Request $request, RRHHEmpleados $empleado)
    {
        $empleado = $empleado->obtenerEmpleados($request);
        return response()->json([
            'status' => 'success',
            'result' => [
                'total' => $empleado->total(), 
                'rows' => $empleado->items()
            ],
            'messages' => null
        ]);
	}
	
	/**
     * Busqueda de empleados
     *
     * @access  public
     * @param   
     * @return  json(array)
     */

    public function buscarEmpleados(Request $request, RRHHEmpleados $empleado)
    {
        $empleado = $empleado->buscarEmpleados($request);
        return response()->json([
            'results' => $empleado
        ]);
    }

    /**
     * Obtener empleado especifico
     *
     * @access  public
     * @param   
     * @return  json(array)
     */

    public function obtenerEmpleado(Request $request, RRHHEmpleados $empleado)
    {
        $rules = [
            'id_empleado' => 'required|integer|min:1'
		];
	

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
		  
			$empleado = $empleado->obtenerEmpleado($request);
			if(!empty($empleado[0])){	
            return response()->json([
                'status' => 'success',
                'result' => $empleado[0],
                'messages' => null
			]);
		}
		else{
		  return response()->json([
				'status' => 'error',
				'result' => array('id'=>["Datos no encontrados"]),
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

	public function crearEmpleado(Request $request)
	{
		$rules = [
			'codigo' => 'required|string|max:10|unique:pgsql.rrhh.empleados,codigo',
			'nombre' => 'required|string|max:50',
			'primer_apellido' => 'required|string|max:50',
			'segundo_apellido' => 'required|string|max:50',
			'cedula' =>  'required|string|max:14',
			'email' => 'email|required|string|max:100',
			'direccion' =>  'required|string|max:100',
			'telefono' =>  'required|string|max:20',
			'id_rol' => 'required|integer',
			'usuario' => 'required|string|max:50|unique:pgsql.admon.usuarios,usuario',
			'password' => 'required|confirmed|string|max:60'
		];

		$validator = Validator::make($request->all(), $rules);
		if (!$validator->fails()) {

			try{
			DB::beginTransaction();
			$persona = new RRHHPersonas;
			$persona->nombre = $request->nombre;
			$persona->primer_apellido = $request->primer_apellido;
			if(!empty( $request->segundo_apellido)){
				$persona->segundo_apellido = $request->segundo_apellido;
			}else{$persona->segundo_apellido = '';}
			
			$persona->cedula = $request->cedula;
			$persona->direccion = $request->direccion;
			$persona->email = $request->email;
			$persona->telefono = $request->telefono;
			$persona->activo = 1;
			$persona->save();

			$empleado = new RRHHEmpleados;
			
			$empleado->id_persona = $persona->id_persona;
			$empleado->codigo = $request->codigo;
			$empleado->activo = 1;
			$empleado->save();

			$usuario = new AdmonUsuarios;
			$usuario->password = Hash::make($request->password);
			$usuario->usuario = $request->usuario;
			$usuario->id_rol = $request->id_rol;
			$usuario->token = str_random(10);
			$usuario->remember_token = str_random(10);
			$usuario->id_empleado = $empleado->id_empleado;
			$usuario->estado = 1;
			$usuario->save();

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

	public function actualizarEmpleado(Request $request)
	{
		$rules = [
			'id_usuario' => 'required|integer',
			'id_empleado' => 'required|integer',
			'codigo' => [
                'required',
                'string',
                'max:10',
                Rule::unique('pgsql.rrhh.empleados')->ignore($request->id_empleado,'id_empleado')
            ],
			//'codigo' => 'required|string|max:10|unique:pgsql.rrhh.empleados,codigo',
			'nombre' => 'required|string|max:50',
			'primer_apellido' => 'required|string|max:50',
			'segundo_apellido' => 'string|max:50',
			'email' => 'email|required|string|max:100',
			'cedula' =>  'required|string|max:14',
			'direccion' =>  'required|string|max:191',
			'telefono' =>  'required|string|max:20',
			'id_rol' => 'required|integer',
			'password' => 'confirmed|string|max:60'
		];
	
		
		$validator = Validator::make($request->all(), $rules);
		if (!$validator->fails()) {

			try{
			DB::beginTransaction();

			$usuario = AdmonUsuarios::findOrFail($request->id_usuario);
			if($request->password!=''){
			$usuario->password = Hash::make($request->password);
			}
			$usuario->id_rol = $request->id_rol;
			$usuario->save();

			$empleado = RRHHEmpleados::findOrFail($usuario->id_empleado);
			//$empleado->codigo = $request->codigo;
			//$empleado->save();

			$persona = RRHHPersonas::findOrFail($empleado->id_persona);
			$persona->nombre = $request->nombre;
			$persona->primer_apellido = $request->primer_apellido;
			if(!empty( $request->segundo_apellido)){
				$persona->segundo_apellido = $request->segundo_apellido;
			}else{$persona->segundo_apellido = '';}
			$persona->cedula = $request->cedula;
			$persona->direccion = $request->direccion;
			$persona->email = $request->email;
			$persona->telefono = $request->telefono;
			$persona->save();
			

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
     * Change User Password
     *
     * @access 	public
     * @param 	
     * @return 	json(string)
     */

	public function cambiarContrasenaEmpleado(Request $request)
	{
		$rules = [
			'id' => 'required',
			'password' => 'required'
		];

		$validator = Validator::make($request->all(), $rules);
		if (!$validator->fails()) {
			$user = AdmonUsuarios::find($request->id);
			$user->password = Hash::make($request->password);
			$user->save();

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
     * Delete User
     *
     * @access 	public
     * @param 	
     * @return 	json(string)
     */

	public function desactivarEmpleado(Request $request)
	{
		$rules = [
			'id_empleado' => 'required'
		];

		$validator = Validator::make($request->all(), $rules);
		if (!$validator->fails()) {
			$empleado = RRHHEmpleados::find($request->id_empleado);
			$usuario = AdmonUsuarios::where('id_empleado','=', $empleado->id_empleado)->get()->first();;
			//print_r($usuario);
			if(auth()->id() == $usuario->id) {
				return response()->json([
					'status' => 'success',
					'result' => 'No te puedes desactivar a tí mismo!',
					'messages' => null
				]);
			}else{
				$empleado->activo = 0;
				$empleado->save();
	
				return response()->json([
					'status' => 'success',
					'result' => null,
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
     * Delete User
     *
     * @access 	public
     * @param 	
     * @return 	json(string)
     */

	public function activarEmpleado(Request $request)
	{
		$rules = [
			'id_empleado' => 'required'
		];

		$validator = Validator::make($request->all(), $rules);
		if (!$validator->fails()) {
			$empleado = RRHHEmpleados::find($request->id_empleado);
			$empleado->activo = 1;
			$empleado->save();

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

}