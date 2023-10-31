<?php
require '../../includes/funciones.php';

$auth = autenticado();

if (!$auth) {
    header('location: ../index.php');
}


//validar que el id sea el correcto
$id = $_GET['id'];
$id = filter_var($id, FILTER_VALIDATE_INT);

if (!$id) {
    header('location: ../index.php');
}

//Base de datos
require '../../includes/config/database.php';
$db = conectarDB();

//consulta de actualización
$consulta = "SELECT * FROM  propiedades WHERE id = {$id}";
$resultado = mysqli_query($db, $consulta);
$propiedad = mysqli_fetch_assoc($resultado);

//Consulta para los vendedores 
$consulta = "SELECT * FROM vendedores;";

$resultado = mysqli_query($db, $consulta);

// Arreglo con mensajes de errores
$errores = [];

//Guardar datos ya escritos
$titulo = $propiedad['titulo'];
$precio = $propiedad['precio'];
$descripcion = $propiedad['descripcion'];
$habitaciones = $propiedad['habitaciones'];
$wc = $propiedad['wc'];
$estacionamiento = $propiedad['estacionamiento'];
$vendedores_id = $propiedad['vendedores_id'];
$imagenPropiedad = $propiedad['imagen'];

// Ejecutar el codigo despues que el usuario envia el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // $_FILES permite ver el contenido de los archivos 

    $titulo = mysqli_real_escape_string($db, $_POST['titulo']);
    $precio = mysqli_real_escape_string($db, $_POST['precio']);
    $descripcion = mysqli_real_escape_string($db, $_POST['descripcion']);
    $habitaciones = mysqli_real_escape_string($db, $_POST['habitaciones']);
    $wc = mysqli_real_escape_string($db, $_POST['wc']);
    $estacionamiento = mysqli_real_escape_string($db, $_POST['estacionamiento']);
    $vendedores_id = mysqli_real_escape_string($db, $_POST['vendedores_id']);
    $creado = DATE('y/m/d');

    //Asignar $_FILES a una variable 
    $imagen = $_FILES['imagen'];

    // ** Validación de formulario **
    if (!$titulo) {
        $errores[] = 'Debes añadir un título';
    }

    if (!$precio) {
        $errores[] = 'El precio es obligatorio';
    }

    if (strlen($descripcion) < 25) {
        $errores[] = 'La descripción es obligatoria y debe tener al menos 25 caracteres';
    }

    if (!$habitaciones) {
        $errores[] = 'El número de habitaciones es obligatorio';
    }

    if (!$wc) {
        $errores[] = 'El número de baños es obligatorio';
    }

    if (!$estacionamiento) {
        $errores[] = 'El número de estacionamientos es obligatorio';
    }

    if (!$vendedores_id) {
        $errores[] = 'Debes elegir un vendedor';
    }

    //Validar por tamaño de imagen
    $medida = 1000 * 1000;

    if ($imagen['size'] > $medida) {
        $errores[] = 'La imagen es muy pesada';
    }

    //revisar que el arreglo de errores no este vacio 
    if (empty($errores)) {

        //crear una carpeta
        $carpetaImagenes = '../../imagenes/';

        if (!is_dir('imagenes')) {
            mkdir($carpetaImagenes);
        }

        $nombreImagen = '';

        if ($imagen['name']) {
            unlink($carpetaImagenes . $propiedad['imagen']); //unlink es para eliminar un archivo

            // Generar nombre aleatorio
            $nombreImagen = md5(uniqid(rand(), true)) . '.jpg';

            //subir imagen al servidor
            move_uploaded_file($imagen['tmp_name'], $carpetaImagenes . $nombreImagen);

        } else {
           $nombreImagen = $propiedad['imagen'];
        }

        // actualizar en base de datos
        $query = "UPDATE propiedades SET titulo = '{$titulo}', precio = {$precio}, imagen = '{$nombreImagen}', descripcion = '{$descripcion}', habitaciones = {$habitaciones}, wc = {$wc}, estacionamiento = {$estacionamiento}, vendedores_id = {$vendedores_id} WHERE id = {$id};";

        // Pasar los valores a la base de datos 
        $resultado = mysqli_query($db, $query);

        if ($resultado) {
            //Redireccionar al usuario
            header("Location: ../../admin/index.php?resultado=2");
        }
    }
}

// ******* Inclución ************
incluirTemplate('header');

?>

<!--******************* HTML ********************-->

<main class="contenedor seccion">
    <h1>Actualizar</h1>

    <a class="boton-verde boton" href="../index.php">Salir</a>

    <?php foreach ($errores as $error) : ?>
        <div class="alerta error">
            <?php echo $error; ?>
        </div>
    <?php endforeach ?>

    <!-- ***************** FORMULARIO ***************** -->

    <form class="formulario" method="POST" enctype="multipart/form-data">
        <fieldset>
            <legend>Información General</legend>

            <label for="titulo">Título de la propiedad</label>
            <input type="text" id="titulo" name="titulo" placeholder="Título de la propiedad" value="<?php echo $titulo; ?>">

            <label for="precio">Precio</label>
            <input type="number" id="precio" name="precio" placeholder="Precio" min="0" value="<?php echo $precio; ?>">

            <label for="imagen">Imagen</label>
            <input type="file" id="imagen" name="imagen" accept="image/jpeg , image/png">

            <img src="../../imagenes/<?php echo $imagenPropiedad;?>" class="imagen-small">

            <label for="descripcion">Descripción</label>
            <textarea id="descripcion" name="descripcion" cols="30" rows="10"> <?php echo $descripcion; ?> </textarea>

        </fieldset>

        <fieldset>
            <legend>Información de la propiedad</legend>

            <label for="habitaciones">Habitaciones</label>
            <input type="number" name="habitaciones" id="habitaciones" min="1" max="9" placeholder="Ej: 3" value="<?php echo $habitaciones; ?>">

            <label for="wc">Baños</label>
            <input type="number" id="wc" name="wc" placeholder="Ej: 3" min="1" max="9" value="<?php echo $wc; ?>">

            <label for="estacionamiento">Estacionamientos</label>
            <input type="number" id="estacionamiento" name="estacionamiento" placeholder="Ej: 3" min="1" max="9" value="<?php echo $estacionamiento ?>">
        </fieldset>

        <fieldset>
            <legend>Vendedor</legend>
            <select name="vendedores_id">
                <option selected disabled>-- Seleccionar --</option>

                <?php while ($vendedor = mysqli_fetch_assoc($resultado)) : ?>

                    <option <?php echo $vendedores_id === $vendedor['id'] ? 'selected' : ''; ?> value="<?php echo $vendedor['id']; ?>"> <?php echo $vendedor['nombre'] . " " . $vendedor['apellido'] ?> </option>

                <?php endwhile; ?>

            </select>
        </fieldset>

        <input type="submit" class="boton-verde boton" value="Actualizar propiedad">
    </form>

    <!-- ************ FIN FORMULARIO ************ -->

</main>

<?php
incluirTemplate('footer');
?>