<?php
session_start();
$auth = $_SESSION['login'];

if (!$auth) {
    header('location: ../index.php');
}

//incluir base de datos
include '../includes/config/database.php';
$db = conectarDB();

// consulta
$query = "SELECT * FROM propiedades";

//reslutado de al consulta
$consulta = mysqli_query($db, $query);

// validar la URL  
$resultado = $_GET['resultado'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = $_POST['id'];
    $id = filter_var($id, FILTER_VALIDATE_INT);

    if ($id) {
        $query = "DELETE FROM propiedades WHERE id = $id";

        $resultado = mysqli_query($db, $query);

        if ($resultado) {
            header('location:/bienesraices/admin/index.php?resultado=3');
        }
    }
}

//inclucion de header
require '../includes/funciones.php';
incluirTemplate('header');

?>

<main class="contenedor seccion">
    <h1>Administrador de Bienes raíces</h1>

    <?php if ($resultado == 1) : ?>
        <p class="alerta exito">Anuncio creado correctamente</p>
    <?php elseif ($resultado == 2) : ?>
        <p class="alerta exito">Actualizado Correctamente</p>
    <?php elseif ($resultado == 3) : ?>
        <p class="alerta exito">Eliminado Correctamente</p>
    <?php endif; ?>

    <a class="boton-verde boton" href="/bienesraices/admin/propiedad/crear.php">Nueva Propiedad</a>

    <table class="propiedades">
        <thead>
            <tr>
                <th>Id</th>
                <th>Título</th>
                <th>Imagen</th>
                <th>Precio</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>

            <?php while ($propiedades = mysqli_fetch_assoc($consulta)) : ?>

                <tr>
                    <th><?php echo $propiedades['id']; ?></th>
                    <th><?php echo $propiedades['titulo']; ?></th>
                    <th><img src="../imagenes/<?php echo $propiedades['imagen']; ?>" class="imagen-tabla"></th>
                    <th>$ <?php echo $propiedades['precio']; ?></th>
                    <th>

                        <form method="POST" class="w-100">
                            <input type="hidden" name="id" value="<?php echo $propiedades['id']; ?>">

                            <input type="submit" class="boton-rojo-block" value="Eliminar">

                        </form>

                        <a href="././propiedad/actualizar.php?id=<?php echo $propiedades['id']; ?>" class="boton-amarillo-block">Actualizar</a>
                    </th>
                </tr>

            <?php endwhile ?>

        </tbody>
    </table>
</main>

<?php
//cerrar conexión
mysqli_close($db);

incluirTemplate('footer');
?>