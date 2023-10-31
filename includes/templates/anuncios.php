 <?php 
    //incluir la base de datos
    $db = conectarDB();

    //hacer la consulta
    $query = "SELECT * FROM propiedades LIMIT $limite";

    //obtener la los resultados
    $resultado = mysqli_query($db, $query);

 ?>
 
 <div class="contenedor-anuncios">

    <?php while ($propiedades = mysqli_fetch_assoc($resultado)): ?>

     <div class="anuncio">
             
             <img loading="lazy" src="./imagenes/<?php echo $propiedades['imagen'] ?>" alt="imagen">

         <div class="contenido-anuncio">
             <h3><?php echo $propiedades['titulo']; ?></h3>
             <p> <?php echo substr($propiedades['descripcion'], 0 ,110) . '...' ?></p>
             <p class="precio"> $ <?php echo number_format($propiedades['precio']); ?></p>

             <ul class="iconos-caracteristicas">
                 <li>
                     <img class="icono" loading="lazy" src="build/img/icono_wc.svg" alt="icono wc">
                     <p><?php echo $propiedades['wc']; ?> </p>
                 </li>
                 <li>
                     <img class="icono" loading="lazy" src="build/img/icono_estacionamiento.svg" alt="icono estacionamiento">
                     <p><?php echo $propiedades['estacionamiento']; ?> </p>
                 </li>
                 <li>
                     <img class="icono" loading="lazy" src="build/img/icono_dormitorio.svg" alt="icono habitaciones">
                     <p><?php echo $propiedades['habitaciones']; ?> </p>
                 </li>
             </ul>

             <a href="anuncio.php?id=<?php echo $propiedades['id']; ?>" class="boton-amarillo-block">
                 Ver Propiedad
             </a>
         </div><!--.contenido-anuncio-->
     </div><!--anuncio-->

    <?php endwhile ?>

 </div> <!--.contenedor-anuncios-->

 <?php 
 
    //cerra conexión
    mysqli_close($db);
 
 ?>