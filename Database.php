<?php

define("DB_HOST", "localhost");
define("DB_USER", "bathalert_user");
define("DB_PASS", "LAp7kWEaQv");
define("DB_SCHE", "bathalerts");

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_SCHE);
if($conn->connect_error) {
    echo "DB Problems";
    exit;
}
$conn->set_charset('utf8');
register_shutdown_function('close');

function close() {
    global $conn;
    $conn->close();
}

?>