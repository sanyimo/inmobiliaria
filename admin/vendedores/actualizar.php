<?php
     require '../../includes/app.php'; 
    use App\Vendedor;
    use Intervention\Image\ImageManagerStatic as Image;
  
    //estaAutenticado();

    // Validar la URL por ID válido
    $id = $_GET['id'];
    $id = filter_var($id, FILTER_VALIDATE_INT);

    if(!$id) {
        header('Location: /admin');
    }
    // Obtener los datos del vendedor
    $vendedor = Vendedor::find($id);

    // Arreglo con mensajes de errores
    $errores = Vendedor::getErrores();

    // Ejecutar el código después de que el usuario envia el formulario
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        // Asignar los atributos
        $args = $_POST['vendedor'];

        $vendedor->sincronizar($args);
         // Validación
        $errores = $vendedor->validar();

        
        // Revisar que el array de errores está vacío
        if (empty($errores)) {
            // Guardar en la base de datos
            $vendedor->guardar();
        }
    }
    incluirTemplate('header');
?>

    <main class="contenedor seccion">
        <h1 class="amarillo">Actualizar vendedor/a</h1>

        <a href="/admin" class="boton boton-verde">Volver</a>

        <?php foreach($errores as $error): ?>
        <div class="alerta error">
            <?php echo $error; ?>
        </div>
        <?php endforeach; ?>

        <form class="formulario" method="POST">
            <?php include '../../includes/templates/formulario_vendedores.php' ?>

            <input type="submit" value="Actualizar vendedor" class="boton boton-verde">
        </form>
    </main>

<?php
    incluirTemplate('footer');
?>