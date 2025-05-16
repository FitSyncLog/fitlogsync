<?php
// Secure session settings before session_start()
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    ini_set('session.cookie_secure', 1);
}

session_start();
include "indexes/db_con.php";

?>