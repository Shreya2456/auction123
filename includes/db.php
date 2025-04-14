<?php
$host = 'localhost';
$dbname = 'auction_db';
$username = 'root';
$password = 'root';

$db = new mysqli($host, $username, $password, $dbname);

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}
?>