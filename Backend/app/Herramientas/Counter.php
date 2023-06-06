<?php

namespace App\Herramientas;

use Illuminate\Support\Facades\DB;
use Exception;

class Counter {

    /**
     * Get next counter value for the provided key
     *
     * @param string $key
     * @return string
     * @throws Exception
     */
    public function next($key)
    {
        $found = $this->db()
            ->where('key', $key)
            ->first();

        if(!$found) {
            throw new \RuntimeException('No record for counter found');
        }

        return $found->prefix.$found->value;
    }

    public function increment($key)
    {
        $result = $this->db()
            ->where('key', $key)
            ->increment('value');

        if(!$result) {
            throw new \RuntimeException('Counter could not increment');
        }

        return $result;
    }

    /**
     * Get new database instance
     *
     * @return DB
     */
    protected function db()
    {
        return DB::table('admon.contadores');
    }
}
