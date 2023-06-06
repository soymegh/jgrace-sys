<?php

namespace App\Models\Admon;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Secciones extends Model
{
    use HasFactory;

    /**
     * Se establece el schema y tabla de la base de datos
     * estamos apuntando.
     *
     * @var string
     */
    protected $table = 'admon.secciones';

    /**
     * Aca se define una primaryKey protegida, diferente a la que Eloquent
     * establece por defecto.
     *
     * @var int
     */
    protected $primaryKey = 'id_seccion';

    /**
     * Establecemos los atributos que pueden asignar en masa
     *
     * @var array<int, string>
     */
    protected $fillable =['descripcion','secuencia','id_menu','activo','tipo_seccion','id_empresa'];

    /**
     * Este tabla no tiene campos timestamps
     * @var bool
     */
    public $timestamps = false;

    /**
     * Relación Seccion - Secciones Menus
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function seccionMenus()
    {
        //return $this->hasMany('App\Models\AdmonSeccionesMenus','id_seccion');

        return $this->hasMany('App\Models\AdmonSeccionesMenus','id_seccion')
            ->select('admon.secciones_menu.id_seccion_menu','admon.secciones_menu.id_seccion','admon.secciones_menu.id_menu',
                'admon.menus.nombre_menu','admon.menus.secuencia')
            ->leftJoin('admon.menus', 'admon.secciones_menu.id_menu','admon.menus.id_menu')
            ->orderby('admon.menus.secuencia');

        //   DB::raw("(select m.secuencia from admon.menus m where m.id_menu=admon.secciones_menu.id_menu) as secuencia"))
        //->orderBy('secuencia');
    }
}





