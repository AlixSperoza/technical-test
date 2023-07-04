<?php

namespace App\Actions\Dashboard;

use App\Models\TrackerHistory;

use Carbon\Carbon;

use function json_decode;

class RecordsSensorsDayAction
{
    /**
     * @return array
     */
    public function execute(): array
    {
        $history = TrackerHistory::select('sensors', 'created_at')->toBase()->get();
        $sensors_list = [];

        foreach ($history as $h) {
            $sensors = json_decode($h->sensors, true);
            $date = Carbon::parse($h->created_at)->toDateString();

            foreach ($sensors as $sensor) {
                if (!isset($sensors_list[ $date ])) {
                    $sensors_list[ $date ] = [];
                }

                if (!isset($sensors_list[ $date ][ $sensor ])) {
                    $sensors_list[ $date ][ $sensor ] = 0;
                }

                $sensors_list[ $date ][ $sensor ]++;
            }
        }

        return [ 'success' => true, 'data' => $sensors_list ];
    }
}