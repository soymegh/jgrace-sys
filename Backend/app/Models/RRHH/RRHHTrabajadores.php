<?php 

namespace App\Models;

use DB, Illuminate\Database\Eloquent\Model;

class RRHHTrabajadores extends Model {

    const CREATED_AT = 'f_creacion';
    const UPDATED_AT = 'f_modificacion';
    protected $table = 'rrhh.trabajadores';
    protected $primaryKey='id_trabajador';
     /**
     * The attributes that are mass assignable.
     *rrhh.empleados
     * @var array
     */

    protected $fillable = ['codigo','activo'];
    protected $hidden = [
        'f_creacion','f_modificacion',
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

    /**
     * Obtener Lista de empleados
     *
     * @access  public
     * @param
     * @return  json(array)
     */

    public function buscar($request)
    {
        $trabajadores = $this->select(['*',DB::raw("CONCAT(primer_nombre,' ',segundo_nombre,' ',primer_apellido,' ',segundo_apellido) AS text")])
        ->join('rrhh.trabajadores_detalles','rrhh.trabajadores_detalles.id_trabajador','=','rrhh.trabajadores.id_trabajador')
        ->where('activo', 1)
        ->whereNotIn('rrhh.trabajadores.id_trabajador',function($query) {

            $query->select('id_empleado')->from('admon.usuarios');

        });

        if (!empty($request->q)) {
            $searchValue = $request->q;

            $trabajadores->where(function($query) use($searchValue) {
                $query->where('primer_nombre', 'ILIKE', '%' . $searchValue . '%')
                    ->OrWhere('segundo_nombre', 'ILIKE', '%' . $searchValue . '%')
                    ->OrWhere('primer_apellido', 'ILIKE', '%' . $searchValue . '%')
                    ->OrWhere('segundo_apellido', 'ILIKE', '%' . $searchValue . '%');
            });

        }
        $trabajadores->orderBy('rrhh.trabajadores.id_trabajador', 'asc');
        return $trabajadores->limit(6)->get();
    }


    public function buscarTrabajador($request)
    {
        $trabajadores = $this->select(['*',DB::raw("CONCAT(primer_nombre,' ',segundo_nombre,' ',primer_apellido,' ',segundo_apellido) AS text")])
            ->join('rrhh.trabajadores_detalles','rrhh.trabajadores_detalles.id_trabajador','=','rrhh.trabajadores.id_trabajador');
        $trabajadores->where('activo', 1);

        if (!empty($request->q)) {
            $searchValue = $request->q;

            $trabajadores->where(function($query) use($searchValue) {
                $query->where('primer_nombre', 'ILIKE', '%' . $searchValue . '%')
                    ->OrWhere('segundo_nombre', 'ILIKE', '%' . $searchValue . '%')
                    ->OrWhere('primer_apellido', 'ILIKE', '%' . $searchValue . '%')
                    ->OrWhere('segundo_apellido', 'ILIKE', '%' . $searchValue . '%');
            });

        }
        $trabajadores->orderBy('primer_nombre', 'asc');
        return $trabajadores->limit(6)->get();
    }

    /**
     * Obtener Lista de empleados
     *
     * @access  public
     * @param   
     * @return  json(array)
     */

    public function obtenerTrabajadores($request)
    {
        $trabajadores = $this->select(['*']);
        if (!empty($request->search['field'])) {
            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $status = $request->search['status'];
            $trabajadores->where($searchField, 'ilike', '%' . $searchValue . '%');
            if($status == 0){
                $trabajadores->where('activo',true);
            }
            $trabajadores->with(['trabajadorDetalles' => function($query) {
                $query->with('trabajadorMunicipio');
            }]);
            $trabajadores->with('trabajadorCargo');
            $trabajadores->with('trabajadorArea');
            $trabajadores->orderBy('id_trabajador', 'asc');
        }
        return $trabajadores->paginate($request->limit);
    }


    public function obtenerTrabajador($request)
    {   
        $trabajador = $this->select(['*']);
        $trabajador->with(['trabajadorDetalles' => function($query) {
            $query->with(['trabajadorMunicipio' => function($query2) {
                $query2->with(['departamentoMunicipio' => function($query3) {
                    $query3->with('municipiosDepartamento');
                }]);
            }]);
        }]);
        $trabajador->with('trabajadorCargo');
        $trabajador->with('trabajadorArea');
        $trabajador->with('trabajadorNivelAcademico');
        $trabajador->with('trabajadorNivelEstudio');
        $trabajador->with('trabajadorDatosMedicos');
        $trabajador->with('trabajadorGrupoFamiliar');
        $trabajador->with('trabajadorSaldoVacacion');
        $trabajador->with(['trabajadorIngresoDeduccion' => function($query){
            $query->with('asignacionIngreso'); }]);
        $trabajador->where('id_trabajador', '=', $request->id_trabajador);
      
        return $trabajador->get();
    }//$municipios->with('departamentoMunicipio');


    public function obtenerCodigoTrabajador($sexo)
    {
        $codigo = $this->select([DB::raw("COALESCE(max(secuencia::integer),0)+1 as secuencia")]);
        if((!empty($sexo)))
        {
            if($sexo == 'M')
            {
                $codigo->where('sexo',$sexo);
            }else if($sexo == 'F')
                {
                    $codigo->where('sexo',$sexo);
                }
        }
        return $codigo->first();
    }

    public function trabajadorCargo()
    {
        return $this->belongsTo('App\Models\RRHHCargos','id_cargo');
    }
    public function trabajadorSaldoVacacion()
    {
        return $this->belongsTo('App\Models\RRHHSaldosVacaciones','id_trabajador');
    }

    public function trabajadorArea()
    {
        return $this->belongsTo('App\Models\PublicAreas','id_area');
    }
    public function trabajadorNivelAcademico()
    {
        return $this->belongsTo('App\Models\RRHHNivelesAcademicos','id_nivel_academico');
    }

    public function trabajadorNivelEstudio()
    {
        return $this->belongsTo('App\Models\RRHHNivelesEstudios','id_niveles_estudios');
    }

    public function trabajadorDatosMedicos()
    {
        return $this->hasOne('App\Models\RRHHDatosMedicos','id_trabajador');
    }

    public function trabajadorGrupoFamiliar()
    {
        return $this->hasMany('App\Models\RRHHGruposFamiliares','id_trabajador');
    }
    public function trabajadorIngresoDeduccion()
    {
        return $this->hasMany('App\Models\RRHHIngresosDeduccionesTrabajadores','id_trabajador');
    }

    /**
     * Trabajador detalle
     */
    public function trabajadorDetalles()
    {
        return $this->hasOne('App\Models\RRHHTrabajadoresDetalles','id_trabajador');
    }
}