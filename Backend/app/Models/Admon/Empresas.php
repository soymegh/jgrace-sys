<?php

namespace App\Models\Admon;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresas extends Model
{
    use HasFactory;

    /**
     * Se establece el schema y tabla de la base de datos
     * estamos apuntando.
     *
     * @var string
     */
    protected $table = 'admon.empresas';

    /**
     * Aca se define una primaryKey protegida, diferente a la que Eloquent
     * establece por defecto.
     *
     * @var int
     */
    protected $primaryKey = 'id_empresa';

    /**
     * Establecemos los atributos que pueden asignar en masa
     *
     * @var array<int, string>
     */
    protected $fillable =['nombre','siglas','token','solvente','administrador','estado'];

    /**
     * Aca definimos el nombre de los campos de tipo timestamp
     *
     * @var array<int, string>
     */
    const CREATED_AT = 'f_creacion';
    const UPDATED_AT = 'f_modificacion';

    /** Relación Empresa - Menus
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function Menus(){
        return $this->hasMany('App\Models\Admon\Menus','id_empresa');
    }

    /**
     * Relación Empresa - Secciones Menus
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function SeccionesMenus(){
        return $this->hasMany('App\Models\Admon\SeccionesMenus','id_empresa');
    }
}



