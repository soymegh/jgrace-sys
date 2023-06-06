<?php

namespace App\Models;

use DB, Illuminate\Database\Eloquent\Model;

class RRHHContratoSolicitud extends Model
{
    const CREATED_AT = 'f_grabacion';
    const UPDATED_AT = 'f_modificacion';
    protected $table = 'rrhh.contratos_solicitudes';
    protected $primaryKey='id_contrato_solicitud';
    protected $fillable = ['id_contrato','id_contratos_dgeneral_interno','id_contratos_dgeneral_merecedor','monto',
        'id_contrato_tipo','descripcion_servicio','plazo_ejecucion','id_departamento','f_inicio_contrato','f_fin_contrato',
        'estado','observacion'];
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

    public function obtenerContratosSolicitud($request)
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
            $contrato->orderBy('id_contrato_solicitud', 'asc');
        }
        $contrato->with('solicitudContratoInterno');
        $contrato->with('solicitudContratoMerecedor');
        $contrato->with('solicitudContratoDepartamento');
        $contrato->with('solicitudContratoTipo');
        return $contrato->paginate($request->limit);
    }


    public function solicitudContratoInterno()
    {
        return $this->belongsTo('App\Models\RRHHContratoGeneralInterno','id_contratos_dgeneral_interno')->select('id_contrato_dgeneral_interno','nombre_representante','caracter_cargo','caracter_legal',DB::raw("CONCAT((select t.descripcion from rrhh.tipos_actos_juridicos t where t.id_tipo_acto_juridico = rrhh.contratos_dgenerales_internos.id_tipo_acto_juridico),'(',nombre_representante,')') AS representante_acto"));
    }

    public function solicitudContratoMerecedor()
    {
        return $this->belongsTo('App\Models\RRHHContratoGeneralMerecedor','id_contratos_dgeneral_merecedor')->select('id_contrato_dgeneral_merecedor','nombre_representante','caracter_cargo','id_tipo_acto_juridico','caracter_legal',
            DB::raw("CONCAT((select t.descripcion from rrhh.tipos_actos_juridicos t where t.id_tipo_acto_juridico = rrhh.contratos_dgenerales_merecedores.id_tipo_acto_juridico),'(',nombre_representante,')') AS representante_acto"));
    }
    public function solicitudContratoDepartamento()
    {
        return $this->belongsTo('App\Models\PublicDepartamentos','id_departamento');
    }
    public function solicitudContratoTipo()
    {
        return $this->belongsTo('App\Models\RRHHContratoTipos','id_contrato_tipo');
    }
}
