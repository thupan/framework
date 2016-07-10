<?php

namespace Service;

class Validator
{
    public static function blank($str)
    {
        return (strlen($str) <= 0) ? true : false;
    }
}
