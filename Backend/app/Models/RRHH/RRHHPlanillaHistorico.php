<?php

namespace App\Models;

use DB, Illuminate\Database\Eloquent\Model;
use DateTime;

class RRHHPlanillaHistorico extends Model
{
    const CREATED_AT = 'f_grabacion';
    const UPDATED_AT = 'f_modificacion';
    protected $table = 'rrhh.planillas_historicos';
    protected $primaryKey='id_planilla_historico';
    protected $fillable = [];
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
     * Obtener Listado de planillas
     *
     * @access 	public
     * @param 	
     * @return array
     */

    public function obtenerPlanilla($request)
    {
        if(!empty($request->id_planilla_control)) /* && !empty($request->id_nomina) obtener planilla según tipo */
        {
            $planilla = DB::select("SELECT * from rrhh.generarplanilla(?)",[$request->id_planilla_control]);/*,$request->id_nomina parametro para tipo planilla a generar*/

            return [
                'planilla' => $planilla
            ];
        }else
            {
                return [];
            }
    }

    public function obtenerCodigo($id_trabajador)
    {
        //print_r($request);
        $codigo = $this->select([DB::raw("COALESCE(max(num_colilla),0)+1 as secuencia")])
            ->where('id_trabajador',$id_trabajador);
        return $codigo->first();
    }

}
