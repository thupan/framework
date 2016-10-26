<?php
/**
 * Classe controladora de redirect do frame.
 *
 * @version 1.0
 */

namespace Service;

class Redirect
{
    /**
     * Método público para redirecionar uma página.
     *
     * @method to()
     *
     * @param  string
     *
     * @return none
     */
    public static function to($location)
    {
        $location = ($location === '/') ? URL : URL . $location;

        header('Location: ' . $location);
        exit;
    }
}
