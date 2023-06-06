<?php

namespace App\Models;

use DB, Illuminate\Database\Eloquent\Model;

class RRHHDatosMedicos extends Model
{
    const CREATED_AT = 'f_grabacion';
    const UPDATED_AT = 'f_modificacion';
    protected $table = 'rrhh.datos_medicos';
    protected $primaryKey='id_dato_medico';
    protected $fillable = ['id_trabajador','seguro_inss','seguro_medico','inss_ipss','inss_ipssrp','centro_privado','grupo_sanguineo',
        'alergia','alergia_descripcion','diabetes','hipertension','cardiaca','peso_libras','asma','otra_enfermedad',
        'nombre_medico','telefono_medico','contacto_emergencia','telefono_emergencia','observaciones','altura'];
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
     * Obtener Listado de Paises
     *
     * @access 	public
     * @param 	
     * @return 	json(array)
     */

    public function obtenerDatosMedicos($request)
    {
        $datos = $this->select(['*']);
        if (!empty($request->search['field'])) {
            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $statusValue = $request->search['status'];
            $datos->where($searchField, 'ilike', '%' . $searchValue . '%');
            if($statusValue == 0){
                $datos->where('activo',true);
            }
            $datos->orderBy('id_dato_medico', 'asc');
        }
        return $datos->paginate($request->limit);
    }

}
