<?php 
    class server {

        public function __construct()
        {
            
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
            
            if(!socket_listen ($sock , 10))
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