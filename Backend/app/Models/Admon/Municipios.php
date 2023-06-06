<?php

namespace App\Models\Admon;

use DB, Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Municipios extends Model
{
    protected $table = 'public.municipios';
    protected $primaryKey='id_municipio';
    protected $fillable = ['descripcion'];
    public $timestamps = false;
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
     * Obtener Listado de municipios
     *
     * @access 	public
     * @param
     * @return 	json(array)
     * @author octaviom
     */

    public function obtener($request)
    {

        $municipios = $this->select(['public.municipios.*','public.departamentos.descripcion as departamento']);
        $municipios->Join('public.departamentos', 'public.municipios.id_departamento', '=', 'public.departamentos.id_departamento');
        if (!empty($request->search['field'])) {
            //echo $this->replaceField($request->search['field'], $fields);
            //$searchField = $this->replaceField($request->search['field'], $fields);
            if($request->search['field'] === 'departamento'){
                $searchField = 'public.departamentos.descripcion';
            }else {
                $searchField = 'public.municipios.descripcion';
            }
            $searchValue = $request->search['value'];
            //$searchField = $request->search['field'];
            //$searchValue = $request->search['value'];
            $municipios->where($searchField, 'ilike', '%' . $searchValue . '%');
        }
        $municipios->with('departamentoMunicipio');
        $municipios->orderby('id_municipio');
        return $municipios->paginate($request->limit);
    }

    /**
     * Relación municipio - departamento
     * @return BelongsTo
     * @author octaviom
     */
    public function departamentoMunicipio()
    {
        return $this->belongsTo('App\Models\Admon\Departamentos','id_departamento')->select('id_departamento','descripcion');
    }

}
