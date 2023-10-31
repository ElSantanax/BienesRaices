<?php
require '../../includes/app.php';

use App\propiedad;
use Intervention\Image\ImageManagerStatic as Image;

$auth = autenticado();;

if (!$auth) {
    header('location: ../index.php');
}

//Base de datos
$db = conectarDB();

//Consulta para los vendedores 
$consulta = "SELECT * FROM vendedores;";

$resultado = mysqli_query($db, $consulta);

// Arreglo con mensajes de errores
$errores = propiedad::getErrores();

//Guardar datos ya escritos
$titulo = '';
$precio = '';
$descripcion = '';
$habitaciones = '';
$wc = '';
$estacionamiento = '';
$vendedores_id = '';

// Ejecutar el codigo despues que el usuario envia el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    //Crea una nueva instancia
        $propiedad = new propiedad($_POST);

    // Generar nombre unico
        $nombreImagen = md5(uniqid(rand(), true)) . '.jpg';

    //Setear la imagen
    //Realiza un resize a la imagen con intervention
            
         if ($_FILES['imagen']['tmp_name']) {
            $image = Image::make($_FILES['imagen']['tmp_name'])->fit(800, 600);
            $propiedad->setImage($nombreImagen);
         }   

    //Validacion
        $errores = $propiedad->validar();

    //Revisar que el arreglo de errores no este vacio 
    if (empty($errores)) {

        //Crear una carpeta
        if (!is_dir(CARPETA_IMAGENES)) {
            mkdir(CARPETA_IMAGENES);
        }
       
        //guarda la imagen en el servidor
    	$image->save(CARPETA_IMAGENES . $nombreImagen);

        //guardar en la base de datos
        $resultado = $propiedad->guardar();
        
        //Mensaje de exito
        if ($resultado) {
        //Redireccionar al usuario
        header("Location: ../../admin/index.php?resultado=1");
        }
    }
}

// ******* Inclución ************

incluirTemplate('header');

?>

<!--******************* HTML ********************-->

<main class="contenedor seccion">
    <h1>Crear</h1>

    <a class="boton-verde boton" href="../index.php">Salir</a>

    <?php foreach ($errores as $error) : ?>
        <div class="alerta error">
            <?php echo $error; ?>
        </div>
    <?php endforeach ?>

    <!-- ***************** FORMULARIO ***************** -->

    <form class="formulario" method="POST" action="../../admin/propiedad/crear.php" enctype="multipart/form-data">
        <fieldset>
            <legend>Información General</legend>

            <label for="titulo">Título de la propiedad</label>
            <input type="text" id="titulo" name="titulo" placeholder="Título de la propiedad" value="<?php echo $titulo; ?>">

            <label for="precio">Precio</label>
            <input type="number" id="precio" name="precio" placeholder="Precio" min="0" value="<?php echo $precio; ?>">

            <label for="imagen">Imagen</label>
            <input type="file" id="imagen" name="imagen" accept="image/jpeg , image/png">

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

        <input type="submit" class="boton-verde boton" value="Crear Propiedad">
    </form>

    <!-- ************ FIN FORMULARIO ************ -->

</main>

<?php
incluirTemplate('footer');
?>