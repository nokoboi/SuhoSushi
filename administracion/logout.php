<?php
// Hay que utilizar session_start() para poder tener acceso a $_SESSION
session_start();

// cierro la session
session_destroy();
header('Location: loginAdmin.php');
exit();