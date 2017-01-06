<?php

namespace Service;

class Cache {

    public static function fetch($key) {
        return apc_fetch($key);
    }

    public static function add($key, $value, $time = null) {
        if(!$time) {
            apc_store($key, $value);
        } else {
            if(!$lastupdated = apc_fetch('lastupdated')) {
                // criando cache
                $lastupdated = new \DateTime("now");
                apc_store('lastupdated', $lastupdated);
                apc_store($key, $value);
            } else {
                $now  = new \DateTime("now");
                $diff = $now->diff($lastupdated);

                $min = ($diff->h/60) + ($diff->i) + ($diff->s/3600);

                if($min > $time) {
                    // sobreescreve
                    $lastupdated = new \DateTime("now");
                    apc_store('lastupdated', $lastupdated);
                    apc_store($key, $value);
                } else {
                    // atualiza
                    apc_add($key, $value);
                }
            }
        }
    }
}
