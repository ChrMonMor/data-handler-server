<?php
enum Sensors: int
{
    case Temperatur = 0;
    case Humidity = 1;
    case Loudness = 2;
    case AirQuality = 3;
    // etc.
}

enum ControllerType: int
{
    case Sensor = 0;
    case Alarm = 1;
    case Server = 2;
    case Database = 3;
}
function CallAPI(string $method, string $url){
    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    if($method == "POST"){
        curl_setopt($curl, CURLOPT_POST, 1);
    }

    try {
        $resp = curl_exec($curl);
        $result = json_decode($resp);
        return $result;
    } catch (\Throwable $th) {
        echo $th;
    }
    curl_close($curl);
    
}

class Varibles {
     public $temperature;
     public $temperature_low;
     public $temperature_high;
     public $temperature_low_after;
     public $temperature_high_after;
     public $humidity;
     public $humidity_medium;
     public $humidity_high;
     public $humidity_medium_after;
     public $humidity_high_after;
     public $noise;
     public $noise_medium;
     public $noise_high;
     public $airquality;
     public $airquality_medium;
     public $airquality_high;
     public $start_time;
     public $end_time;
     public $after_alarm;

     public function __construct() {
        $row = CallAPI("GET", "192.168.1.149/post_api.php");
        $this->temperature = $row[0]->temperature;
        $this->temperature_low = $row[0]->temperature_low;
        $this->temperature_high = $row[0]->temperature_high;
        $this->temperature_low_after = $row[0]->temperature_low_after;
        $this->temperature_high_after = $row[0]->temperature_high_after;
        $this->humidity = $row[0]->humidity;
        $this->humidity_medium = $row[0]->humidity_medium;
        $this->humidity_high = $row[0]->humidity_high;
        $this->humidity_medium_after = $row[0]->humidity_medium_after;
        $this->humidity_high_after = $row[0]->humidity_high_after;
        $this->noise = $row[0]->noise;
        $this->noise_medium = $row[0]->noise_medium;
        $this->noise_high = $row[0]->noise_high;
        $this->airquality = $row[0]->airquality;
        $this->airquality_medium = $row[0]->airquality_medium;
        $this->airquality_high = $row[0]->airquality_high;
        $this->start_time = $row[0]->start_time;
        $this->end_time = $row[0]->end_time;
        $this->after_alarm = $row[0]->after_alarm;
    }
    public function UpdateValues() : Varibles {
        return new Varibles();
    }
}

function AlertFilter(Varibles $var, int $sensor, int $messure) {
   switch ($sensor) {
      case Sensors::Temperatur:
         echo "Your favorite color is red!";
         break;
      case Sensors::Humidity:
         echo "Your favorite color is blue!";
         break;
      case Sensors::Loudness:
         echo "Your favorite color is green!";
         break;
      case Sensors::AirQuality:
          echo "Your favorite color is green!";
           break;
   }
}

function TemperatureLimits(Varibles $var, $messure){
        CallAlarms("temps", 1);
}
function HumidityLimits(Varibles $var, $messure){
        CallAlarms("humid", 1);
}
function LoudnessLimits(Varibles $var, $messure){
        CallAlarms("loud", 1);
}
function AirQualityLimits(Varibles $var, $messure){
        CallAlarms("AirQ", 1);
}
function CallAlarmServers(string $url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        try {
            $resp = curl_exec($curl);
            print_r("dan");
        } catch (\Throwable $th) {
            echo $th;
        }
        curl_close($curl);
}
function GetAlarmIP() {
        $row = CallAPI("GET", "192.168.1.149/controller_post.php");
        $array = array();
        foreach ($row as $x) {
                if($x->types == ControllerType::Alarm->name){
                        $array[] = array($x->ip, $x->name);
                }
        }
        return $array;
}
function CallAlarms(string $what, int $type) {
        $arr = GetAlarmIP();
        print_r($arr);
        foreach ($arr as $x) {
            $var = $x[0].'?type='.$type.'&where="'.$x[1].'"&what="'.$what.'"';
                CallAlarmServers($var);
        }
}
$var = new Varibles();
TemperatureLimits($var, 1);