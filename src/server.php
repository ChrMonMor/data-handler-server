<?php 
    require_once "../services/service.php";

    class server {

        public $serverVaribles;
        public array $sensors;

        public function __construct() {
            $this->serverVaribles = new varibles();
            $this->sensors = array();
        }

        public function createSocket():Socket {
                    
            if(($sock = socket_create(AF_INET, SOCK_STREAM, 0)))
            {
                return $sock;

            } else {
                $errorcode = socket_last_error();
                $errormsg = socket_strerror($errorcode);
                die("Couldn't create socket: [$errorcode] $errormsg \n");
            }

        }

        public function bindSocket(Socket $sock, string $ip, ) {
                        
            // Bind the source address
            if( !socket_bind($sock, "127.0.0.1" , 5000) )
            {
                $errorcode = socket_last_error();
                $errormsg = socket_strerror($errorcode);
                
                die("Could not bind socket : [$errorcode] $errormsg \n");
            }

            echo "Socket bind OK \n";
        }

        public function socketListen(Socket $sock) {
            
            if(!socket_listen ($sock , 100))
            {
                $errorcode = socket_last_error();
                $errormsg = socket_strerror($errorcode);
                
                die("Could not listen on socket : [$errorcode] $errormsg \n");
            }

            echo "Socket listen OK \n";
        }

        public function acceptSocket(Socket $sock) : Socket {
            
            echo "Waiting for incoming connections... \n";

            //Accept incoming connection - This is a blocking call
            $client = socket_accept($sock);
                
            //display information about the client who is connected
            if(socket_getpeername($client , $address , $port))
            {
                echo "Client $address : $port is now connected to us.";
            }

            return $client;
        }

        public function readSocket(Socket $client) : string {
            
            //read data from the incoming socket
            if(($input = socket_read($client, 1024000))){
                
                $str_arr = explode ("|", $input); 
                $readings = $str_arr[0];
                $ip = $str_arr[1];
                $type = $str_arr[2];
                if($type == "".Sensors::Temperatur){
                    $temp = $this->updateSensorArray($ip, Sensors::Temperatur);
                    AlarmClass::AlertFilter($this->serverVaribles, $temp, $readings, $ip);
                }
                if($type == "".Sensors::Humidity){
                    $humid = $this->updateSensorArray($ip, Sensors::Humidity);
                    AlarmClass::AlertFilter($this->serverVaribles, $humid, $readings, $ip);
                }
                if($type == "".Sensors::Loudness){
                    $noise = $this->updateSensorArray($ip, Sensors::Loudness);
                    AlarmClass::AlertFilter($this->serverVaribles, $noise, $readings, $ip);
                }
                if($type == "".Sensors::AirQuality){
                    $airq = $this->updateSensorArray($ip, Sensors::AirQuality);
                    AlarmClass::AlertFilter($this->serverVaribles, $airq, $readings, $ip);
                }
                return $input;
            }
            return "error";
        }

        public function response(Socket $client, string $message) : bool{
            
            if(socket_write($client, $message)){
                return true;
            }
            return false;
        }

        public function updateSensorArray($ip, Sensors $type){
            foreach($this->sensors as $sensor){
                if($sensor->ip == $ip and $sensor->sensor == $type){
                    ErrorClass::HasErrorOccured($sensor);
                    if($type == Sensors::Temperatur){
                        $sensor = $this->NextPromise($this->serverVaribles->temperature);
                    }
                    if($type == Sensors::Humidity){
                        $sensor = $this->NextPromise($this->serverVaribles->humidity);
                    }
                    if($type == Sensors::Loudness){
                        $sensor = $this->NextPromise($this->serverVaribles->noise);
                    }
                    if($type == Sensors::AirQuality){
                        $sensor = $this->NextPromise($this->serverVaribles->airquality);
                    }
                    return $sensor;
                }
            }
            if($type == Sensors::Temperatur){
                $arr = new SensorModel($ip, $type, $this->serverVaribles->temperature);
                $this->sensors[] = $arr;
                return $arr;
            }
            if($type == Sensors::Humidity){
                $arr = new SensorModel($ip, $type, $this->serverVaribles->humidity);
                $this->sensors[] = $arr;
                return $arr;
            }
            if($type == Sensors::Loudness){
                $arr = new SensorModel($ip, $type, $this->serverVaribles->noise);
                $this->sensors[] = $arr;
                return $arr;
            }
            if($type == Sensors::AirQuality){
                $arr = new SensorModel($ip, $type, $this->serverVaribles->airquality);
                $this->sensors[] = $arr;
                return $arr;
            }
        }
        
        public function NextPromise(int $addTime){
            $addTime =+ 2;
            return date('H:i:s', strtotime("+$addTime minutes"));
        }


        public function liveServer(Socket $sock){
            
            //start loop to listen for incoming connections
            while (true) 
            {
                //Accept incoming connection - This is a blocking call
                $client =  $this->acceptSocket($sock);
                
                //display information about the client who is connected
                if(socket_getpeername($client , $address , $port))
                {
                    echo "Client $address : $port is now connected to us. \n";
                }
                
                //read data from the incoming socket
                $input = socket_read($client, 1024000);
                
                $response = "OK .. $input";
                
                // Display output  back to client
                socket_write($client, $response);
            }
        }
    }
?>