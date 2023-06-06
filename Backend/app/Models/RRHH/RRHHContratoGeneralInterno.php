<?php

namespace App\Models;

use DB, Illuminate\Database\Eloquent\Model;

class RRHHContratoGeneralInterno extends Model
{
    const CREATED_AT = 'f_grabacion';
    const UPDATED_AT = 'f_modificacion';
    protected $table = 'rrhh.contratos_dgenerales_internos';
    protected $primaryKey='id_contrato_dgeneral_interno';
    protected $fillable = ['nombre_represnetante','estado_civil','id_nivel_academico','id_nivel_estudio','caracter_cargo',
        'caracter_legal','no_escritura_publica','nombre_notario_publico','fecha_inscripción','no_asiento_librodiario',
        'no_inscrito','no_tomo','no_unico','domicilio','departamento','denominacion','nombre_empresa','descripcion_contractual',
        'no_ruc','fecha_librada'];
    protected $dateFormat = 'Y-m-d H:i:s.u';

    /**
     * Replace Field
     *
     * @access 	public
     * @param 	
     * @return 	string
     */


	public function replaceField($field, $fields = [])
    {
        if (in_array($field, $fields)) {
            return $fields[$field];
        }

        return $field;
    }

    /**
     * Obtener Listado de contrato
     *
     * @access 	public
     * @param 	
     * @return 	json(array)
     */

    public function obtenerContratosInternos($request)
    {
        $contrato = $this->select(['*']);
        if (!empty($request->search['field'])) {
            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $statusValue = $request->search['status'];
            $contrato->where($searchField, 'ilike', '%' . $searchValue . '%');
            /*if($statusValue == 0){
                $contrato->where('activo',true);
            }*/
            $contrato->orderBy('id_contrato_dgeneral_interno', 'asc');
        }
        $contrato->with('contratoInternoNivelEstudio');
        $contrato->with('contratoInternoNivelAcademico');
        $contrato->with('contratoInternoDepartamentoDomicilio');
        $contrato->with('contratoInternoDepartamento');
        $contrato->with('contratoInternoTipoActoJurudico');
        $contrato->with('contratoInternoDepartamentoLibrado');
        return $contrato->paginate($request->limit);
    }


    public function contratoInternoNivelEstudio()
    {
        return $this->belongsTo('App\Models\RRHHNivelesEstudios','id_nivel_estudio');
    }

    public function contratoInternoNivelAcademico()
    {
        return $this->belongsTo('App\Models\RRHHNivelesAcademicos','id_nivel_academico');
    }
    public function contratoInternoDepartamento()
    {
        return $this->belongsTo('App\Models\PublicDepartamentos','departemento');
    }
    public function contratoInternoDepartamentoDomicilio()
    {
        return $this->belongsTo('App\Models\PublicDepartamentos','domicilio');
    }
    public function contratoInternoDepartamentoLibrado()
    {
        return $this->belongsTo('App\Models\PublicDepartamentos','departamento_librado');
    }
    public function contratoInternoTipoActoJurudico()
    {
        return $this->belongsTo('App\Models\RRHHTiposActosJuridicos','id_tipo_acto_juridico');
    }
}
