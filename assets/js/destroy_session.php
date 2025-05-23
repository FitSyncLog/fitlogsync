<?php
session_start();

// Destroy the session
session_unset();
session_destroy();

echo "Session destroyed";
?>
