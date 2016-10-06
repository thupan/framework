<?php
/**
 * Classe de Crypt.
 * Para usar é só declarar use \Service\Crypt;
 * $hash = Crypt::encode('Texto','senha');
 * echo $hash;
 * echo Crypt::decode($hash,'senha');
 *
 * @package \Service\Crypt
 * @version 1.0
 *
 */

namespace Service;

class Crypt {
    /**
     * Método público encode.
     *
     * @method merge()
     * @param String
     * @param String
     * @param String
     * @return String
     */
     public static function encode($string,$senha,$algorithm ='rijndael-256') {
      $key = md5($senha, true); // bynary raw 16 byte dimension.
      $iv_length = mcrypt_get_iv_size( $algorithm, MCRYPT_MODE_CBC );
      $iv = mcrypt_create_iv( $iv_length, MCRYPT_RAND );
      $encrypted = mcrypt_encrypt( $algorithm, $key, $string, MCRYPT_MODE_CBC, $iv );
      $result = base64_encode( $iv . $encrypted );
      return $result;
    }
    /**
     * Método público decode.
     *
     * @method decode()
     * @param String
     * @param String
     * @param String
     * @return String
     */
    public static function decode($string,$senha,$algorithm ='rijndael-256') {
      $key = md5($senha, true);
      $iv_length = mcrypt_get_iv_size( $algorithm, MCRYPT_MODE_CBC );
      $string = base64_decode( $string );
      $iv = substr( $string, 0, $iv_length );
      $encrypted = substr( $string, $iv_length );
      $result = mcrypt_decrypt( $algorithm, $key, $encrypted, MCRYPT_MODE_CBC, $iv );
      return $result;
    }

}
