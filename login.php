<?php
require './includes/app.php';
$db = conectarDB();

//errores
$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = mysqli_real_escape_string($db, filter_var($_POST['email'], FILTER_VALIDATE_EMAIL));
    $password = mysqli_real_escape_string($db, $_POST['password']);

    if (!$email) {
        $errores[] = "El email es obligatorio";
    }
    if (!$password) {
        $errores[] = "El password es obligatorio";
    }

    if (empty($errores)) {
        //consulta
        $query = "SELECT * FROM usuario WHERE email = '{$email}'";
        $resultado = mysqli_query($db, $query);

        if ($resultado ->num_rows) {

            //auntenticar el password
            $usuario = mysqli_fetch_assoc($resultado);
            $auth = password_verify($password, $usuario['password']);
            
            if ($auth) {
                //autenticado
                session_start(); //siempre poner a la hora de iniciar sesión

                $_SESSION['usuario'] = $usuario['email'];
                $_SESSION['login'] = true; 

                header('location: ./admin/index.php');

            }else { 
                $errores[] = "El password es incorrecto";
            }

        }else {
            $errores[] = "El usuario no existe";
        }
    }
}

//incluyendo header

incluirTemplate('header');

?>

<main class="contenedor seccion contenido-centrado">
    <h1>Inicia sesion</h1>

    <?php foreach ($errores as $error) : ?>
        <div class="alerta error">
            <?php echo $error; ?>
        </div>
    <?php endforeach ?>

    <form class="formulario" method="POST">
        <fieldset>
            <legend>Email y Password</legend>

            <label for="email">E-mail</label>
            <input type="email" name="email" placeholder="Tu email" id="email">

            <label for="password">Password</label>
            <input type="password" name="password" placeholder="Tu password" id="password">
        </fieldset>

        <input class="boton-verde boton" type="submit" value="Iniciar Sesión">
    </form>

</main>

<?php
incluirTemplate('footer');
?>