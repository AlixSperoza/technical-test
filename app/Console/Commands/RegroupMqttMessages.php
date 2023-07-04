<?php

namespace App\Console\Commands;

use App\Models\MqttMessages;
use App\Models\TrackerHistory;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

use function count;
use function json_decode;
use function json_last_error;
use function str_replace;
use function str_starts_with;

class RegroupMqttMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'regroup:mqtt_messages';

    /**
     * @var array
     */
    private array $trackers = [];

    /**
     * @var array
     */
    private array $without_sensors = [];

    /**
     * @var array
     */
    private array $without_tracker = [];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Retrieving of all messages');

        $messages = MqttMessages::orderBy('created_at', 'ASC')->get();

        $this->newLine();
        $this->info($messages->count().' messages retrieved');

        $messages_trackers = $messages->filter(function ($m) {
            return str_starts_with($m->message, '{ "s": "GT:');
        });
        
        $messages_sensors = $messages->filter(function ($m) {
            return str_starts_with($m->message, '{ "s": "CP:');
        });

        $this->newLine();
        $this->info('Parsing of '.$messages_trackers->count().' trackers messages');

        $bar = $this->output->createProgressBar($messages_trackers->count());
        $bar->start();

        foreach ($messages_trackers as $m) {
            $this->parseTrackerMessage($m);

            $bar->advance();
        }

        $bar->finish();

        $this->newLine(2);
        $this->info('Parsing of '.$messages_sensors->count().' sensors messages');

        $bar = $this->output->createProgressBar($messages_sensors->count());
        $bar->start();

        foreach ($messages_sensors as $m) {
            $this->parseSensorMessage($m);

            $bar->advance();
        }

        $bar->finish();

        $this->newLine(2);
        $this->info(count($this->without_tracker).' sensors without tracker ignored');

        $this->newLine();
        $this->info('Saving in database of '.count($this->trackers).' trackers\'s sensors lists');

        $bar = $this->output->createProgressBar(count($this->trackers));
        $bar->start();

        foreach ($this->trackers as $inventory) {
            $this->saveInventory($inventory);

            $bar->advance();
        }

        $bar->finish();

        $this->newLine(2);
        $this->info(count($this->without_sensors).' trackers\'s sensors lists without sensors ignored');

        Storage::put('without_sensors.json', json_encode($this->without_sensors, JSON_PRETTY_PRINT));

        $this->newLine();
        $this->info('Trackers\'s sensors lists without sensors are stored in file for more details');

        $this->newLine();
    }

    /**
     * @param MqttMessages $message
     */
    private function parseSensorMessage(MqttMessages $message)
    {
        $content = json_decode($message->message, true);
        $error_decode = json_last_error();

        if ($error_decode > 0) {
            return;
        }

        $tracker = (int) str_replace('CP:', '', $content['s']);
        $frame_id = (int) str_replace('id:', '', $content['ID_FRAME']);
        $payload = $content['ble_payload'];

        if (!isset($this->trackers[ $frame_id.'/'.$tracker ])) {
            $this->without_tracker[] = [ 'frame_id' => $frame_id, 'tracker' => $tracker, 'message_id' => $message->id ];

            return;
        }

        foreach ($payload as $sensor) {
            $sensor_value = explode(' ', $sensor)[2];
            $sensor_name = explode(';', $sensor_value)[0];

            $this->trackers[ $frame_id.'/'.$tracker ]['sensors'][] = $sensor_name;
        }
    }

    /**
     * @param MqttMessages $message
     */
    private function parseTrackerMessage(MqttMessages $message)
    {
        $content = json_decode($message->message, true);
        $error_decode = json_last_error();

        if ($error_decode > 0) {
            return;
        }

        $tracker = (int) str_replace('GT:', '', $content['s']);
        $frame_id = (int) str_replace('id:', '', $content['v']['ID_FRAME']['value']);
        $battery = $content['v']['battery'];
        $created_at = Carbon::parse($content['ts'])->toDateTimeString();
        $location = $content['loc'];
        $rsrp = $content['v']['network']['rsrp'];

        if (isset($this->trackers[ $frame_id.'/'.$tracker ])) {
            return;
        }

        $this->trackers[ $frame_id.'/'.$tracker ] = [
            'tracker' => $tracker,
            'battery' => $battery,
            'created_at' => $created_at,
            'location' => $location,
            'strength_signal' => $rsrp,
            'sensors' => [],
        ];
    }

    /**
     * @param array $inventory
     * @return void
     */
    private function saveInventory(array $inventory): void
    {
        if (empty($inventory['sensors'])) {
            $this->without_sensors[] = $inventory;

            return;
        }

        [ $latitude, $longitude ] = $inventory['location'];

        $history = new TrackerHistory();
        $history->tracker_id = $inventory['tracker'];
        $history->latitude = $latitude;
        $history->longitude = $longitude;
        $history->sensors = $inventory['sensors'];
        $history->battery = $inventory['battery'];
        $history->strength_signal = $inventory['strength_signal'];
        $history->created_at = $inventory['created_at'];
        $history->save();
    }
}