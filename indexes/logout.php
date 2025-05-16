<?php
session_start();
session_unset();
session_destroy();
setcookie(session_name(), '', time() - 3600, '/'); // Remove session cookie
header("Location: ../index.php?Success=Logout success");
exit();
?>
