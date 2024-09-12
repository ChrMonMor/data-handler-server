<?php
    require_once "src/server.php";
    require_once "services/service.php";


    $server = new Server();
    $sock = $server->createSocket();
    $server->liveServer($sock);
?> 