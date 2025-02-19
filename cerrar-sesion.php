<?php 

session_start();

$_SESSION = [];//reiniciar arreglo de sesion es mejor que session_unset/session_destroy

header('Location: /');