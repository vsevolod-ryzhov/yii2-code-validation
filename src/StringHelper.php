<?php

declare(strict_types=1);


namespace vsevolodryzhov\yii2CodeValidation;


class StringHelper
{
    //exclude: o O i I l L 0
    const CODE_DICTIONARY = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ123456789';

    public static function generateCode($length = 6): string
    {
        $code = "";
        $len = strlen(self::CODE_DICTIONARY);
        for ($i = 0; $i < $length; $i++)
        {
            $index = rand(0, $len - 1);
            $code .= self::CODE_DICTIONARY[$index];
        }

        return $code;
    }
}