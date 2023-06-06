<?php

namespace App\Models\Admon;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Roles extends Model
{
    use HasFactory;

    /**
     * Se establece el schema y tabla de la base de datos
     * estamos apuntando.
     *
     * @var string
     */
    protected $table = 'admon.roles';

    /**
     * Aca se define una primaryKey protegida, diferente a la que Eloquent
     * establece por defecto.
     *
     * @var int
     */
    protected $primaryKey = 'id_rol';

    /**
     * Establecemos los atributos que pueden asignar en masa
     *
     * @var array<int, string>
     */
    protected $fillable = ['descripcion', 'activo', 'id_empresa'];

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
     * @access    public
     * @param
     * @return    string
     */
    public function replaceField($field, $fields = [])
    {
        if (in_array($field, $fields)) {
            return $fields[$field];
        }

        return $field;
    }


    /**
     * Obtener lista de roles segun el id empresa del usurio
     *
     * @access    public
     * @param
     * @return    json(array)
     */
    public function obtenerRoles($request)
    {
        $roles = $this->select(['*']);
        if (!empty($request->search['field'])) {
            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $statusValue = $request->search['status'];
            $id_empresa = session()->get('id_empresa');
            $roles->where('id_empresa', '=', $id_empresa)
                ->where($searchField, 'ilike', '%' . $searchValue . '%');
            //$menus->where('admon.menus.activo',1);
            if ($statusValue == 0) {
                $roles->where('estado', true);
            }
            $roles->orderBy('admon.roles.id_rol', 'asc');
        }
        return $roles->paginate($request->limit);
    }

    /**
     * Relación Roles - Empresa
     * @return BelongsTo
     */
    public function Empresa()
    {
        return $this->belongsTo('App\Models\Admon\Empresas', 'id_empresa');
    }

    /**
     * Relación Roles - Permisos
     * @return HasMany
     */
    public function permisos()
    {
        return $this->hasMany('App\Models\AdmonPermisos', 'id_rol');
    }
}





