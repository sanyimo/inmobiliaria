<?php
define('TEMPLATES_URL', __DIR__ . '/templates');
define('FUNCIONES_URL', __DIR__ . 'funciones.php');
define('CARPETA_IMAGENES', __DIR__ . '/../imagenes/imagenesPropiedades/');
define('CARPETA_VENDEDORES', __DIR__ . '/../imagenes/imagenesVendedores/');
function incluirTemplate(string $nombre, bool $inicio = false) {
    include TEMPLATES_URL . "/{$nombre}.php";
}

function estaAutenticado() {
    session_start();

    if(!$_SESSION['login']) {
        header('Location: /');
    }
}
function debuguear($variable) {
    echo "<pre>";
    var_dump($variable);
    echo "</pre>";
    exit;
}
// Escapa / Sanitizar el HTML
function s($html) : string {
    return htmlspecialchars($html, ENT_QUOTES, 'UTF-8');
}

// Valida tipo de petición
function validarTipoContenido($tipo){
    $tipos = ['vendedor', 'propiedad'];
    return in_array($tipo, $tipos);
}

// Muestra los mensajes
function mostrarNotificacion($codigo) {
    $mensaje = '';

    switch ($codigo) {
        case 1:
            $mensaje = 'Propiedad creada correctamente';
            break;
        case 2:
            $mensaje = 'Propiedad actualizada correctamente';
            break;
        case 3:
            $mensaje = 'Propiedad eliminada correctamente';
            break;
        case 4:
            $mensaje = 'Vendedor/a registrado/a correctamente';
            break;
        case 5:
            $mensaje = 'Vendedor/a actualizado/a correctamente';
            break;
        case 6:
            $mensaje = 'Vendedor/a eliminado/a correctamente';
            break;
        default:
            $mensaje = false;
            break;
    }
    return $mensaje;
}
