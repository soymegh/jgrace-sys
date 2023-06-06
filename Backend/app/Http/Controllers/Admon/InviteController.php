<?php

namespace App\Http\Controllers\Admon;

use App\Http\Controllers\Controller;
use App\Models\Admon\Invites;
use App\Models\Admon\UsuariosEmpresas;
use Carbon\Carbon;
use Clarkeash\Doorman\Facades\Doorman;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class InviteController extends Controller
{
    public function obtener(Request $request, Invites $invite)
    {
        $invite = $invite->obtenerInvites($request);
        return response()->json([
            'status' => 'success',
            'result' => [
                'total' => $invite->total(),
                'rows' => $invite->items()
            ],
            'messages' => null
        ]);
    }

    public function create()
    {
        return view('invitations.create');
    }

    /**
     * Este metodo realiza la grabación de codigos de invitación aleatorios - unicos,
     * con fecha de expiración y cantidad de usos permitidos
     * @param Request $request
     * @return JsonResponse
     * @author octaviom
     * @copyright ©2022 octaviom
     * @version 1.0
     */
    public function store(Request $request)
    {
        //Definimos reglas de validaciones
        $rules = [
            'max' => 'required|integer',
            'valid_until' => 'required|date'
        ];
        // Procesamos las peticiones recibidas y comparamos con las reglas previamente definidas
        $validator = Validator::make($request->all(), $rules);
        // Validamos si la ejecución no falló en ninguna validación
        if (!$validator->fails()) {
            try {
                DB::BeginTransaction();

                // Obtenemos empresa de usuario logueado
                $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();

                //Instanciamos la propiedad Carbon para dar formato a la fecha recibida desde el cliente
                $date = Carbon::createFromFormat('Y-m-d', $request->valid_until);
//            Doorman::generate()->uses($request->max)->expiresOn($date)->make();

                // Inicializamos servicio de query log para obtener consultas ejecutadas
                DB::connection()->enableQueryLog();

                //Generamos codigo de invitación con parametros de uso maximo y fecha de expiración
                $code = Doorman::generate()->uses($request->max)->expiresOn($date)->once();

                //Guardamos query obtenida en log de la tabla en base de datos en la variable "queries"
                $queries = DB::getQueryLog();

                // Creacion de log de registro

                $dias = array("Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sábado"); // Creamos arreglo de días
                $meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"); // Creamos arreglo de meses del año
                $log['fecha_log'] = $dias[date('w')] . " " . date('d') . " de " . $meses[date('n') - 1] . " del " . date('Y') . ' a las ' . date('h:i:s A'); //Obtenemos fecha y hora actual
                $log['registro'] = 'Se ha registrado un código de autorización con descripción: ' . $code->code . ' por el usuario ' . Auth::user()->name; // Describimos acción realizada y usuario involucrado


                // Obtenemos el id del registro previamente creado y comparamos con la tabla para complementar información del registro
                $doorman = Invites::findOrFail($code->id);
                $doorman->id_empresa = $usuario_empresa->id_empresa; //Guardando id_empresa de usuario logueado
                $doorman->estado = 1; // Codigo activo
                $doorman->log_query = $queries; // Guardamos query obtenida
                $doorman->log = $log['registro'] . ' - ' . $log['fecha_log']; // Guardamos log de registro
                $doorman->save();

                DB::commit();
                return response()->json([
                    'status' => 'success',
                    'result' => '',
                    'messages' => null
                ]);
            } catch (Exception $e) {
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

        /* $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
         $validator = Validator::make($request->all(), $rules);
         if (!$validator->fails()) {
             //Creamos instancia del modole
             $invite = new Invites();
             $invite->for = $request->user;
             $invite->uses = $request->max;
             $invite->valid_until = $date;
             $invite->id_empresa = $usuario_empresa->id_empresa;

             // Creando log de inserción
             $dias = array("Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sábado");
             $meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
             $log['fecha_log'] = $dias[date('w')] . " " . date('d') . " de " . $meses[date('n') - 1] . " del " . date('Y') . ' a las ' . date('h:i:s A');
             $log['registro'] = 'Se ha registrado un código de invitación utilizando el metodo STORE de InviteController con id: ' . $invite->id . ' por el usuario ' . Auth::user()->name . ' el ';
             $invite->log = $log['registro']  . $log['fecha_log'];
             $invite->save();

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
         }*/

        // Generamos el código
//        Doorman::generate()
//            ->uses($request->get('max'))
//            ->expiresOn($date)
//            ->make();
//        return redirect()->route('invitations.index');
    }
}
