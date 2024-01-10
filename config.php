<?php
    $dbhost = 'localhost';
    $dbname = 'first_sql';
    $dbuser = 'admin';
    $dbpasswd = 'fff99911G';
    $db_connect = mysqli_connect($dbhost, $dbuser, $dbpasswd, $dbname);
    if (!$db_connect) {
        die("Connection failed: " . mysqli_connect_error());
    }
?>