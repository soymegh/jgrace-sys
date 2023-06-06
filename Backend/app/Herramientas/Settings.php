<?php

namespace App\Herramientas;

use DB;

class Settings {

    /**
     * Get value for the provided key
     *
     * @param  string $key
     * @return value or null
     */
    public function get($key)
    {
        $found = $this->db()
            ->where('identificador', $key)
            ->first();

        return optional($found)->valor;
    }

    public function getLogo()
    {
        $found = $this->db()
            ->where('identificador', 'uploaded_logo')
            ->first();
        
         $logo = Array(json_decode(optional($found)->valor)[0]);  
       
        return $logo[0]->file_thumbnail;
    }

    public function getIcon()
    {
        $found = $this->db()
            ->where('identificador', 'uploaded_icon')
            ->first();

        
         $logo = Array(json_decode(optional($found)->valor)[0]);  
       
        return $logo[0]->file_thumbnail;
    }

    public function getMany($keys)
    {
        $output = [];

        foreach($keys as $type => $identificador) {
            $output[$identificador] = $this->get($identificador);
        }

        return $output;
    }

    /**
     * Update the value of the provided key
     *
     * @param string $key
     * @param string $value
     * @return boolean
     */
    public function set($key, $value)
    {
        return $this->db()
            ->where('identificador', $key)
            ->update(['valor' => $value]);
    }

    public function setMany($array)
    {
        foreach($array as $key => $value) {
            $this->db()
                ->where('identificador', $key)
                ->update(['valor' => $value]);
        }
    }

    /**
     * Update the value of key with null
     *
     * @param  string $key
     * @return boolean
     */
    public function forget($key)
    {
        return $this->set($key, null);
    }

    /**
     * Get new database instance
     *
     * @return DB
     */
    protected function db()
    {
        return DB::table('admon.ajustes');
    }
}
