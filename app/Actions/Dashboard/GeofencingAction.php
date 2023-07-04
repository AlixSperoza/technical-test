<?php

namespace App\Actions\Dashboard;

use App\Models\TrackerHistory;
use App\Services\Geolocation;

use Carbon\Carbon;

use function array_filter;
use function in_array;
use function json_decode;

class GeofencingAction
{
    /**
     * @return array
     */
    public function execute(): array
    {
        $history = TrackerHistory::select('sensors', 'created_at', 'latitude', 'longitude')->orderBy('created_at', 'ASC')->toBase()->get();
        $sensors_list = [];

        foreach ($history as $h) {
            $sensors = json_decode($h->sensors, true);
            $date = Carbon::parse($h->created_at)->toDateString();

            if (!isset($sensors_list[ $date ])) {
                $sensors_list[ $date ] = [];
            }

            $distance = Geolocation::calcul($h->latitude, $h->longitude);

            if ($distance > 500 || $distance === 0) {
                foreach ($sensors as $sensor) {
                    if (in_array($sensor, $sensors_list[ $date ])) {
                        $sensors_list[ $date ] = array_filter($sensors_list[ $date ], function ($s) use ($sensor) {
                            return $s !== $sensor;
                        });
                    }
                }
            } else {
                foreach ($sensors as $sensor) {
                    if (!in_array($sensor, $sensors_list[ $date ])) {
                        $sensors_list[ $date ][] = $sensor;
                    }
                }
            }
        }

        return [ 'success' => true, 'data' => $sensors_list ];
    }
}