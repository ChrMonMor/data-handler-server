<?php 

    class client {

        public Socket $sock;

        public function __construct()
        {
            $this->sock = $this->createSocket();
            $this->connectSocket($this->sock, $_SERVER['REMOTE_ADDR']);
        }

        private function createSocket():Socket {
                    
            if(($sock = socket_create(AF_INET, SOCK_STREAM, 0)))
            {

                return $sock;

            } else {
                
                $errorcode = socket_last_error();
                $errormsg = socket_strerror($errorcode);
                
                die("Couldn't create socket: [$errorcode] $errormsg \n");
            
            }

        }

        public function connectSocket(Socket $sock, string $address, ?int $port = null) {
            if ($port == null){
                $port = 80;
            }
            if(!socket_connect($sock , $address , $port))
            {
                $errorcode = socket_last_error();
                $errormsg = socket_strerror($errorcode);
                
                die("Could not connect: [$errorcode] $errormsg \n");
            }

        }

        public function sendMessage(string $message){

            if( ! socket_send ( $this->sock , $message , strlen($message) , 0))
            {
                $errorcode = socket_last_error();
                $errormsg = socket_strerror($errorcode);
                
                die("Could not send data: [$errorcode] $errormsg \n");
            }
            
            echo "Message send successfully \n";
        }

        public function reciveMessage() {
            //Now receive reply from server
            if(socket_recv ( $this->sock , $buf , 2045 , MSG_WAITALL ) === FALSE)
            {
                $errorcode = socket_last_error();
                $errormsg = socket_strerror($errorcode);
                
                die("Could not receive data: [$errorcode] $errormsg \n");
            }

            //print the received message
            echo $buf;
        }

        public function closeSocket(Socket $sock) {
            socket_close($sock);
        }
    }

?>