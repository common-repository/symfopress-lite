<?php

namespace Netandreus\SymfopressBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class NetandreusSymfopressBundle extends Bundle
{
    /**
     * Parse WordPress config file and get constants from it
     * @param $filepath
     * @return array
     */
    public static function getWpConstants($filepath)
    {
        $defines = array();
        $state = 0;
        $key = '';
        $value = '';

        $file = file_get_contents($filepath);
        $tokens = token_get_all($file);
        $token = reset($tokens);
        while ($token) {
        //    dump($state, $token);
            if (is_array($token)) {
                if ($token[0] == T_WHITESPACE || $token[0] == T_COMMENT || $token[0] == T_DOC_COMMENT) {
                    // do nothing
                } else if ($token[0] == T_STRING && strtolower($token[1]) == 'define') {
                    $state = 1;
                } else if ($state == 2 && self::is_constant($token[0])) {
                    $key = $token[1];
                    $state = 3;
                } else if ($state == 4 && self::is_constant($token[0])) {
                    $value = $token[1];
                    $state = 5;
                }
            } else {
                $symbol = trim($token);
                if ($symbol == '(' && $state == 1) {
                    $state = 2;
                } else if ($symbol == ',' && $state == 3) {
                    $state = 4;
                } else if ($symbol == ')' && $state == 5) {
                    $defines[self::strip($key)] = self::strip($value);
                    $state = 0;
                }
            }
            $token = next($tokens);
        }
        return $defines;
    }

    public static function is_constant($token) {
        return $token == T_CONSTANT_ENCAPSED_STRING || $token == T_STRING ||
            $token == T_LNUMBER || $token == T_DNUMBER;
    }

    public static function dump($state, $token) {
        if (is_array($token)) {
            echo "$state: " . token_name($token[0]) . " [$token[1]] on line $token[2]\n";
        } else {
            echo "$state: Symbol '$token'\n";
        }
    }

    public static function strip($value) {
        return preg_replace('!^([\'"])(.*)\1$!', '$2', $value);
    }
}
