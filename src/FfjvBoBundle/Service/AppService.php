<?php
/**
 * Created by PhpStorm.
 * User: raulnet
 * Date: 30/05/16
 * Time: 22:07
 */

namespace FfjvBoBundle\Service;

use FfjvBoBundle\Entity\User;

class AppService
{

    /**
     * @var string
     */
    private $appKey = '';

    /**
     * @var User
     */
    private $user;

    /**
     * AppService constructor.
     * @param string $appKey
     */
    public function __construct($appKey)
    {
        $this->appKey = $appKey;
    }

    /**
     * @param $user
     * @return $this
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @param string $string
     * @return string
     */
    public function crypt($string = ''){

        $appKey = md5($this->appKey);
        $salt = $this->user->getSalt();
        $letter = -1;
        $char = -1;
        $new_str = '';
        $new_salt = '';
        $strLen = strlen($string);
        $saltLen = strlen($salt);

        for ($i = 0; $i < $saltLen; $i++) {
            $char++;
            if ($char > 30) {
                $char = 0;
            }
            $newSalt = ord($salt{$i}) + ord($appKey{$char});

            if ($newSalt > 255) {
                $newSalt -= 256;
            }
            $new_salt .= chr($newSalt);

        }

        for ($i = 0; $i < $strLen; $i++) {
            $letter++;
            if ($letter > 30) {
                $letter = 0;
            }
            $neword = ord($string{$i}) + ord($new_salt{$letter});

            if ($neword > 255) {
                $neword -= 256;
            }

            $new_str .= chr($neword);

        }
        return base64_encode($new_str);
    }

    /**
     * @param string $string
     * @return string
     */
    public function deCrypt($string = ''){
        $appKey = md5($this->appKey);
        $salt = $this->user->getSalt();
        $letter = -1;
        $char = -1;
        $new_str = '';
        $new_salt = '';

        $str_to_decrypt = base64_decode($string);
        $strlen = strlen($str_to_decrypt);
        $saltLen = strlen($salt);

        for ($i = 0; $i < $saltLen; $i++) {
            $char++;
            if ($char > 30) {
                $char = 0;
            }
            $newSalt = ord($salt{$i}) + ord($appKey{$char});
            if ($newSalt > 255) {
                $newSalt -= 256;
            }
            $new_salt .= chr($newSalt);
        }

        for ($i = 0; $i < $strlen; $i++) {
            $letter++;
            if ($letter > 30) {
                $letter = 0;
            }

            $neword = ord($str_to_decrypt{$i}) - ord($new_salt{$letter});
            if ($neword < 1) {
                $neword += 256;
            }
            $new_str .= chr($neword);
        }

        return $new_str;
    }


}