<?php

// Database connection parameters
$sname= "localhost:3307"; 
$uname= "root"; 
$password= ""; 
$db_name= "fitlogsync1"; 

// Establish a connection to the database
$conn = mysqli_connect($sname, $uname, $password, $db_name);

// Validate if the connection is successful
if (!$conn){ 
    echo 'Connection Failed';
}