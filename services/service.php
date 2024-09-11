<?php
enum Sensors: int {
    case Temperatur = 0;
    case Humidity = 1;
    case Loudness = 2;
    case AirQuality = 3;
    // etc.
}
enum ControllerType: int {
    case Sensor = 0;
    case Alarm = 1;
    case Server = 2;
    case Database = 3;
}
enum APIMethod: int {
    case GET = 0;
    case POST = 1;
    case PUT = 2;
    case DELETE = 3;
}
function CallAPI(string $method, string $url){
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_NOSIGNAL, 1);
    curl_setopt($curl, CURLOPT_TIMEOUT_MS, 1000);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    if($method == APIMethod::POST->name){
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
            TemperatureLimits($var, $messure);
            break;
        case Sensors::Humidity:
            HumidityLimits($var, $messure);
            break;
        case Sensors::Loudness:
            LoudnessLimits($var, $messure);
            break;
        case Sensors::AirQuality:
            AirQualityLimits($var, $messure);
            break;
   }
}

function TemperatureLimits(Varibles $var, $messure){
    $currentTime = date("H:i:s");
    // work hours
    if ($currentTime > $var->start_time && $currentTime < $var->end_time){
        if($messure < $var->temperature_low){
            CallAlarms("temps", 0);
        }
        if($messure > $var->temperature_high){
            CallAlarms("temps", 1);
        }
    } else {
        if($messure < $var->temperature_low_after){
            CallAlarms("temps", 0);
        }
        if($messure > $var->temperature_high_after){
            CallAlarms("temps", 1);
        }
    }
}
function HumidityLimits(Varibles $var, $messure){
    
    $currentTime = date("H:i:s");
    // work hours
    if ($currentTime > $var->start_time && $currentTime < $var->end_time){
        if($messure > $var->humidity_medium && $messure < $var->humidity_high){
            CallAlarms("humid", 1);
        }
        if($messure > $var->temperature_high){
            CallAlarms("humid", 2);
        }
    } else {
        if($messure > $var->humidity_medium_after && $messure < $var->humidity_high_after){
            CallAlarms("humid", 1);
        }
        if($messure > $var->temperature_high_after){
            CallAlarms("humid", 2);
        }
    }
}
function LoudnessLimits(Varibles $var, $messure){
    $currentTime = date("H:i:s");
    // work hours
    if ($currentTime > $var->start_time && $currentTime < $var->end_time){
        if($messure > $var->noise_medium && $messure < $var->noise_high){
            CallAlarms("loud", 1);
        }
        if($messure > $var->noise_high){
            CallAlarms("loud", 2);
        }
    }
}
function AirQualityLimits(Varibles $var, $messure){
    $currentTime = date("H:i:s");
    // work hours
    if ($currentTime > $var->start_time && $currentTime < $var->end_time){
        if($messure > $var->airquality_medium && $messure < $var->airquality_high){
            CallAlarms("AirQ", 1);
        }
        if($messure > $var->airquality_high){
            CallAlarms("AirQ", 2);
        }
    }
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
                CallAPI(APIMethod::GET->name, $var);
        }
}
$var = new Varibles();
TemperatureLimits($var, 1);

?>