<?php 

namespace App\Models;

use DB, Illuminate\Database\Eloquent\Model;

class RRHHEmpleados extends Model {

    protected $table = 'rrhh.empleados';
    protected $primaryKey='id_empleado';
     /**
     * The attributes that are mass assignable.
     *rrhh.empleados
     * @var array
     */
   
    protected $fillable = ['codigo','estado'];
    const CREATED_AT = 'f_creacion';
    const UPDATED_AT = 'f_modificacion';

    protected $hidden = [
        'id_persona', 'f_creacion','f_modificacion',
    ];
    /**
     * Replace Field
     *
     * @access  public
     * @param   
     * @return  string
     */ 

    public function replaceField($field, $fields = [])
    {
        if (in_array($field, $fields)) {
            return $fields[$field];
        }

        return $field;
    }

    /**
     * Obtener Lista de empleados
     *
     * @access  public
     * @param   
     * @return  json(array)
     */

    public function buscarEmpleados($request)
    {   
        $empleados = $this->select(['admon.usuarios.id_usuario','admon.usuarios.usuario', 'admon.roles.descripcion','rrhh.personas.nombre',
        'rrhh.personas.primer_apellido as text',
        'rrhh.personas.primer_apellido','rrhh.personas.segundo_apellido','rrhh.personas.email','rrhh.personas.telefono','rrhh.empleados.id_empleado','rrhh.empleados.activo']);
        $empleados->leftJoin('admon.usuarios', 'admon.usuarios.id_empleado', '=', 'rrhh.empleados.id_empleado');
        $empleados->leftJoin('admon.roles', 'admon.roles.id_rol', '=', 'admon.usuarios.id_rol');
        $empleados->leftJoin('rrhh.personas','rrhh.personas.id_persona','=','rrhh.empleados.id_persona');
        $fields = [
            'nombre' => 'rrhh.personas.nombre',
            'primer_apellido' => 'rrhh.personas.primer_apellido',
            'segundo_apellido' => 'rrhh.personas.segundo_apellido',
            'usuario' => 'admon.usuarios.usuario',
            'descripcion' => 'admon.roles.descripcion'
        ];
        //print_r($request->q);
        if (!empty($request->q)) {
            //$searchField = $this->replaceField($request->search['field'], $fields);
            $searchValue = $request->q;
            $empleados->where('rrhh.personas.nombre', 'ILIKE', '%' . $searchValue . '%');
            $empleados->orderBy('rrhh.empleados.id_empleado', 'asc');
        }
        return $empleados->limit(6)->get();
    }

    /**
     * Obtener Lista de empleados
     *
     * @access  public
     * @param   
     * @return  json(array)
     */

    public function obtenerEmpleados($request)
    {   
        $empleados = $this->select(['admon.usuarios.id_usuario','admon.usuarios.usuario', 'admon.roles.descripcion','rrhh.personas.nombre','rrhh.personas.primer_apellido','rrhh.personas.segundo_apellido','rrhh.personas.email','rrhh.personas.telefono','rrhh.empleados.id_empleado','rrhh.empleados.activo']);
        $empleados->leftJoin('admon.usuarios', 'admon.usuarios.id_empleado', '=', 'rrhh.empleados.id_empleado');
        $empleados->leftJoin('admon.roles', 'admon.roles.id_rol', '=', 'admon.usuarios.id_rol');
        $empleados->leftJoin('rrhh.personas','rrhh.personas.id_persona','=','rrhh.empleados.id_persona');
        $fields = [
            'nombre' => 'rrhh.personas.nombre',
            'primer_apellido' => 'rrhh.personas.primer_apellido',
            'segundo_apellido' => 'rrhh.personas.segundo_apellido',
            'usuario' => 'admon.usuarios.usuario',
            'descripcion' => 'admon.roles.descripcion'
        ];
        if (!empty($request->search['field'])) {
            $searchField = $this->replaceField($request->search['field'], $fields);
            $searchValue = $request->search['value'];
            $empleados->where($searchField, 'ilike', '%' . $searchValue . '%');
            $empleados->orderBy('rrhh.empleados.id_empleado', 'asc');
        }
        return $empleados->paginate($request->limit);
    }


    public function obtenerEmpleado($request)
    {   
        $empleados = $this->select(['admon.usuarios.id_usuario','admon.usuarios.usuario','rrhh.empleados.codigo','admon.usuarios.id_rol',
        'admon.roles.descripcion','rrhh.personas.direccion','rrhh.personas.cedula','rrhh.personas.nombre','rrhh.personas.primer_apellido',
        'rrhh.personas.segundo_apellido','rrhh.personas.email','rrhh.personas.telefono','rrhh.empleados.id_empleado','rrhh.empleados.activo']);
        $empleados->leftJoin('admon.usuarios', 'admon.usuarios.id_empleado', '=', 'rrhh.empleados.id_empleado');
        $empleados->leftJoin('admon.roles', 'admon.roles.id_rol', '=', 'admon.usuarios.id_rol');
        $empleados->leftJoin('rrhh.personas','rrhh.personas.id_persona','=','rrhh.empleados.id_persona');
        $empleados->where('rrhh.empleados.id_empleado', '=', $request->id_empleado);
      
        return $empleados->get();
    }

    public function persona()
    {
        return $this->belongsTo('App\Models\RRHHPersonas','id_persona');
    }
}