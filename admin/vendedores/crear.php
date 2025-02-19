<?php 
    require '../../includes/app.php';
    use App\Vendedor;
    use Intervention\Image\ImageManagerStatic as Image;

    //estaAutenticado();

    $vendedor = new Vendedor;

    // Consultar para obtener los vendedores
    $vendedores = Vendedor::all();

    // Arreglo con mensajes de errores
    $errores = Vendedor::getErrores();

    // Ejecutar el código después de que el usuario envia el formulario
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $vendedor = new Vendedor($_POST['vendedor']);

        // Generar un nombre único
        $vendedorImagen = md5(uniqid(rand(), true)) . ".jpg";

        //setear la imagen
        // Realiza un resize de imagen con Intervention Image
        if($_FILES['vendedor']['tmp_name']['imagen']) {
            $image = Image::make($_FILES['vendedor']['tmp_name']['imagen'])->fit(800,600);
            $vendedor->setImagen($vendedorImagen);
        }
        //Validar
        $errores = $vendedor->validar();
         //Revisar que el array de errores esta vacio
         if (empty($errores)) {
            // Crear la carpeta para subir imagenes
            if(!is_dir(CARPETA_VENDEDORES)) {
                mkdir(CARPETA_VENDEDORES);
            }

            // Guarda la imagen en el servidor
            $image->save(CARPETA_VENDEDORES . $vendedorImagen);

            // Guarda en la base de datos
            $vendedor->guardar();
        }

    }

    incluirTemplate('header');
?>
<main class="contenedor seccion">
        <h1>Registrar nuevo/a vendedor/a</h1>

        <a href="/admin" class="boton boton-verde">Volver</a>

        <?php foreach($errores as $error): ?>
        <div class="alerta error">
            <?php echo $error; ?>
        </div>
        <?php endforeach; ?>
        
        <form class="formulario" method="POST" action="/admin/vendedores/crear.php" enctype="multipart/form-data">
            <?php include '../../includes/templates/formulario_vendedores.php' ?>

            <input type="submit" value="Crear ficha" class="boton boton-verde">
        </form>
    </main>

<?php
    incluirTemplate('footer');
?>