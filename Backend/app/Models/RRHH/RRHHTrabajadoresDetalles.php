<?php 

namespace App\Models;

use DB, Illuminate\Database\Eloquent\Model;

class RRHHTrabajadoresDetalles extends Model {

  public $timestamps=false;
    protected $table = 'rrhh.trabajadores_detalles';
    protected $primaryKey='id_trabajador_detalle';
     /**
     * The attributes that are mass assignable.
     *rrhh.empleados
     * @var array
     */

    protected $fillable = ['sexo','estado_civil','email','direccion','telefono','notifica','fecha_ingreso','fecha_egreso','id_niveles_estudios','id_nivel_academico'];

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

    public function trabajadorMunicipio()
    {
        return $this->belongsTo('App\Models\PublicMunicipios','id_municipio');
    }



}