<?php

/**
 * Description of PassHash
 *
 * @author robert wilkinson
 */
class PassHash {
    
    //set blowfish
    private static $algo = '$2a';
    //set cost
    private static $cost= '$10';
    
    public static function unique_salt() {
        return substr(sha1(mt_rand()), 0, 22);
    } 
    
    public static function hash($password) {
        return crypt($password, self::$algo . self::$cost . '$' . self::unique_salt());   
    }
    
    public static function check_password($hash, $password) {
        $full_salt = substr($hash, 0, 29);
        $new_hash = crypt($password, $full_salt);
        return ($hash === $new_hash);
        
    }
}