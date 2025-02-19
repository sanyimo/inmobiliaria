<?php
//En principio esta sección está pensada para "usuarios" o administradores ya existentes. Hay que registrarlos antes en la BD

    require 'includes/app.php';
    
    $db = conectarDB();

    // Autenticar el usuario
    $errores = [];

    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        // echo "<pre>";
        // var_dump($_POST);
        // echo "</pre>";

        $email = mysqli_real_escape_string($db, filter_var( $_POST['email'], FILTER_VALIDATE_EMAIL) );
        $password = mysqli_real_escape_string($db, $_POST['password']);

        if(!$email) {
            $errores[] = "El correo electrónico no es válido";
        }

        if(!$password) {
            $errores[] = "La contraseña es necesaria";
        }

        if(empty($errores)) {
            // Revisar si el usuario existe.
            $query = "SELECT * FROM usuarios WHERE email = '{$email}' ";
            $resultado = mysqli_query($db, $query);

            if( $resultado->num_rows ) {
                // Revisar si el password es correcto
                $usuario = mysqli_fetch_assoc($resultado);
                // var_dump($usuario['password']);

                // Verificar si el password es correcto o no
                $auth = password_verify($password, $usuario['password']);

                if($auth) {
                    // El usuario esta autenticado
                    session_start();

                    // Llenar el arreglo de la sesión
                    //ROLES????
                    $_SESSION['usuario'] = $usuario['email'];
                    $_SESSION['login'] = true;

                    header('Location: /admin');
                } else {
                    $errores[] = 'Contraseña incorrecta';
                }
            } else {
                $errores[] = "Este usuario no existe";
            }
        }
    }

    incluirTemplate('header');
?>

    <main class="contenedor seccion contenido-centrado">
        <h1>Iniciar sesión</h1>

        <?php foreach($errores as $error): ?>
            <div class="alerta error">
                <?php echo $error; ?>
            </div>
        <?php endforeach; ?>

        <form method="POST" class="formulario">
            <fieldset>
                <legend>Correo electrónico y contraseña</legend>

                <label for="email">E-mail</label>
                <input type="email" name="email" placeholder="Tu e-mail" id="email">

                <label for="password">Contraseña</label>
                <input type="password" name="password" placeholder="Tu contraseña" id="password">
            </fieldset>
        
            <input type="submit" value="Iniciar sesión" class="boton boton-verde">
        </form>
    </main>

<?php
    incluirTemplate('footer');
?>