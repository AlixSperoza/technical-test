<?php

namespace App\Services;

use function acos;
use function config;
use function cos;
use function deg2rad;
use function rad2deg;
use function sin;

class Geolocation
{
    public static function calcul($latitude, $longitude)
    {
        $latitude_charlie = deg2rad(config('charlie.latitude'));
        $longitude_charlie = deg2rad(config('charlie.longitude'));

        $theta = $longitude - $longitude_charlie;

        $distance = sin(deg2rad($latitude)) * sin(deg2rad($latitude_charlie)) + cos(deg2rad($latitude)) * cos(deg2rad($latitude_charlie)) * cos(deg2rad($theta));
        $distance = acos($distance);
        $distance = rad2deg($distance);

        $miles = $distance * 60 * 1.1515;
        $kilometers = $miles * 1.609344;

        return $kilometers * 1000;
    }
}