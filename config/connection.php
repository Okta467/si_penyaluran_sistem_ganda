<?php

include_once "db_config.php";

$connection = mysqli_connect(APP_HOSTNAME, APP_DATABASE_USERNAME, APP_DATABASE_PASSWORD, APP_DATABASE);

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

?>