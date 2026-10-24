<?php
// Session start karo taaki pata chale kaun logged in hai
session_start();

// Saare session variables ko unset (delete) kar do
session_unset();

// Session ko poora destroy kar do (logout)
session_destroy();

// Wapas Login page ya Home page par bhej do
header("Location: index.php");
exit();
?>





