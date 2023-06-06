<?php

namespace App\Models\Admon;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menus extends Model
{
    use HasFactory;

    /**
     * Se establece el schema y tabla de la base de datos
     * estamos apuntando.
     *
     * @var string
     */
    protected $table = 'admon.menus';

    /**
     * Aca se define una primaryKey protegida, diferente a la que Eloquent
     * establece por defecto.
     *
     * @var int
     */
    protected $primaryKey = 'id_menu';

    /**
     * Establecemos los atributos que pueden asignar en masa
     *
     * @var array<int, string>
     */
    protected $fillable =[];

    /**
     * Aca definimos el nombre de los campos de tipo timestamp
     *
     *
     */
    const CREATED_AT = 'f_creacion';
    const UPDATED_AT = 'f_modificacion';

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
     * Obtener lista de Menus segun el tipo de menu y el id empresa del usurio
     *
     * @access 	public
     * @param
     * @return 	json(array)
     */
    public function obtenerMenus($request)
    {
        $menus = $this->select(['*']);
        if (!empty($request->search['field'])) {
            $searchField = $request->search['field'];
            $id_empresa = $request->auth['id_empresa'];
            $searchValue = $request->search['value'];
            $menus->where($searchField, 'ilike', '%' . $searchValue . '%');
            //$menus->where('admon.menus.activo',1);
            $menus->where('admon.menus.id_empresa',$id_empresa);
            $menus->whereNotIn('admon.menus.tipo_menu', array(1,2));
            $menus->orderBy('admon.menus.id_menu', 'asc');
        }
        return $menus->paginate($request->limit);
    }

    /**
     * Relación Menu - Empresa
     * @return BelongsTo
     */
    public function Empresa(){
        return $this->belongsTo('App\Models\Admon\Empresas','id_empresa');
    }

    /**
     * Relación Menu - Menu padre
     * @return HasMany
     */
    public function subMenus()
    {
        return $this->hasMany('App\Models\Admon\AdmonMenus','id_menu_padre');
    }

    /**
     * Relación Menu - Secciones
     * @return HasMany
     */
    public function menuSecciones()
    {
        return $this->hasMany('App\Models\Admon\AdmonSecciones', 'id_menu');
    }

}





