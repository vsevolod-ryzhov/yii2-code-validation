<?php

declare(strict_types=1);


namespace vsevolodryzhov\yii2CodeValidation;


use Exception;
use UnexpectedValueException;

class StringHelper
{
    //exclude: o O i I l L 0
    private const CODE_DICTIONARY = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ123456789';

    public static function generateCode($length = 6): ?string
    {
        $code = "";
        $len = strlen(self::CODE_DICTIONARY);
        try {
            for ($i = 0; $i < $length; $i++) {
                $index = random_int(0, $len - 1);
                $code .= self::CODE_DICTIONARY[$index];
            }
        } catch (Exception $e) {
            throw new UnexpectedValueException('Code creating error: ' . $e->getMessage());
        }

        return $code;
    }
}