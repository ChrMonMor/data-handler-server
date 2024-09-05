<?php
$DB_SERVER ='127.0.0.1';
$DB_USERNAME = 'MonradAdmin';
$DB_PASSWORD='Lord0fTheStacks';
$DB_NAME='sensorDB';
$options = [
    PDO::ATTR_EMULATE_PREPARES   => false, // Disable emulation mode for "real" prepared statements
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Disable errors in the form of exceptions
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Make the default fetch be an associative array
  ];
/* Attempt to connect to MySQL database */
try{
    $pdo = new PDO('mysql:host=' . $DB_SERVER . '; dbname=' . $DB_NAME, $DB_USERNAME, $DB_PASSWORD, $options);
} catch(PDOException $e){
    die('ERROR: Could not connect. ' . $e->getMessage() . ' : ' . $e->getCode());
}