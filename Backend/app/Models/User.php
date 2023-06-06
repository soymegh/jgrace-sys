<?php

namespace App\Models;

use App\Models\Admon\UsuariosEmpresas;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * indicate the table on database
    */
    protected $table = "users";
    protected $primaryKey = "id";
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'last_seen',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Replace Field
     *
     * @access    public
     * @param
     * @return    string
     */
    public function replaceField($field, $fields = [])
    {
        if (in_array($field, $fields, true)) {
            return $fields[$field];
        }

        return $field;
    }


    /**
     * Obtener lista de usuarios segun id_empresa
     *
     * @access    public
     * @param
     * @return    json(array)
     */
    public function obtenerUsuarios($request)
    {
        $users = $this->select(['*']);
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        $users->where('id_empresa',$usuario_empresa->id_empresa);

        if (!empty($request->search['field'])) {
            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $users->where($searchField, 'ilike', '%' . $searchValue . '%');
//            $users->with('trabajador');
//            $users->with('sucursal');
            $users->with('rol');
//            $users->with('empresa');
            $users->with('usuario_empresa');
            $users->orderBy('name', 'asc');
        }
        return $users->paginate($request->limit);
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

    /**
     * Join Usuario - Empresas
     * @return BelongsTo
     * @author octaviom
     */

    public function usuario_empresa(){
        return $this->belongsTo('App\Models\Admon\UsuariosEmpresas','id','id_usuario')
            ->join('admon.empresas','admon.usuarios_empresas.id_empresa','=','admon.empresas.id_empresa')
            ->select('admon.empresas.nombre');
    }

    /**
     * Join Roles - Usuarios
     * @return BelongsTo
     * @author octaviom
     */

    public function rol(){
        return $this->belongsTo('App\Models\Admon\Roles','id_rol');
    }
}
