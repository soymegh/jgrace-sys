<?php

namespace App\Models\Admon;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeccionesMenus extends Model
{
    use HasFactory;

    /**
     * Se establece el schema y tabla de la base de datos
     * estamos apuntando.
     *
     * @var string
     */
    protected $table = 'admon.secciones_menus';

    /**
     * Aca se define una primaryKey protegida, diferente a la que Eloquent
     * establece por defecto.
     *
     * @var int
     */
    protected $primaryKey = 'id_seccion_menu';

    /**
     * Establecemos los atributos que pueden asignar en masa
     *
     * @var array<int, string>
     */
    protected $fillable =['id_seccion','id_menu','id_empresa'];

    /**
     * Este tabla no tiene campos timestamps
     * @var bool
     */
    public $timestamps = false;



    /**
     * Relación Sección Menu - Empresa
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Empresa(){
        return $this->belongsTo('App\Models\Admon\Empresas','id_empresa');
    }

    /**
     * Relación Seccion Menu - Menu
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function seccionMenu()
    {
        return $this->belongsTo('App\Models\AdmonMenus','id_menu')
            ->select('admon.menus.id_menu','admon.menus.secuencia','admon.menus.tipo_menu','admon.menus.nombre_menu','admon.menus.nombre_route',
                DB::raw("substr(admon.menus.icon, 2, length(admon.menus.icon) - 2)::json->>'file_thumbnail' as file_thumbnail"))
            ->join('admon.roles_menus','admon.menus.id_menu','admon.roles_menus.id_menu')
            ->join('admon.roles','admon.roles.id_rol','admon.roles_menus.id_rol')
            ->where('admon.roles.id_rol',Auth::user()->id_rol)
            ->where('admon.menus.activo',1)
            ->whereIn('admon.menus.tipo_menu',array(2))
            ->orderBy('admon.menus.secuencia');
    }

    /**
     * Relación Seccion Menu - Menu
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function listaMenus()
    {
        return $this->hasMany('App\Models\AdmonMenus','id_menu');
    }

    /**
     * Relación Sección Menu - Menu
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function menuDetalle()
    {
        return $this->belongsTo('App\Models\AdmonMenus','id_menu')->with(['menuSecciones' => function($query) {$query->with('seccionMenus');}])->orderby('secuencia');;
    }
}





