<?php

class Validator
{
    public static function sanear($datos)
    {
        $saneados = [];
        foreach ($datos as $key => $value) {
            $saneados[$key] = htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
        }
        return $saneados;
    }

    public static function validarProducto($data)
    {
        $errors = [];



        return $errors;
    }


    public static function esFormatoFecha($string, $formato = 'Y-m-d')
    {
        $fecha = DateTime::createFromFormat($formato, $string);
        return $fecha && $fecha->format($formato) === $string;
    }
}