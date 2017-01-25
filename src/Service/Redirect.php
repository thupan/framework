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
    public static function to($location, $data = null)
    {
        $location = ($location === '/') ? URL : URL . $location;

        $c = ($data) ? '?twigformfields='.base64_encode(json_encode($data)) : null;
        header('Location: ' . $location . $c);
        exit;
    }
}
