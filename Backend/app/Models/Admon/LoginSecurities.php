<?php

namespace App\Models\Admon;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginSecurities extends Model
{
    use HasFactory;

    /**
     * Se establece el schema y tabla de la base de datos
     * estamos apuntando.
     *
     * @var string
     */
    protected $table = 'admon.login_securities';

    /**
     * Aca se define una primaryKey protegida, diferente a la que Eloquent
     * establece por defecto.
     *
     * @var int
     */
    protected $primaryKey = 'id_login_security';

    /**
     * Establecemos los atributos que pueden asignar en masa
     *
     * @var array<int, string>
     */
    protected $fillable =['id_user','google2fa_enable','google2fa_secret','id_empresa'];

    /**
     * Aca definimos el nombre de los campos de tipo timestamp
     *
     */
    const CREATED_AT = 'f_creacion';
    const UPDATED_AT = 'f_modificacion';

}



