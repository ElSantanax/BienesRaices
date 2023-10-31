<?php

namespace App;

class propiedad {
    
    //Base de datos
    protected static $bd; 
    protected static $columnasDB = ['id','titulo','precio','imagen','descripcion','habitaciones','wc','estacionamiento','creado','vendedores_id'];

    //Errores
    protected static $errores = [];

    public $id;
    public $titulo;
    public $precio;
    public $imagen;
    public $descripcion;
    public $habitaciones;
    public $wc;
    public $estacionamiento;
    public $creado;
    public $vendedores_id;

    //Definir la conexion a la base de datos
    public static function setDB($databases)
    {
        self::$bd = $databases;
    }

    public function __construct($args = []) 
    {
        $this->id = $args['id'] ?? '';
        $this->titulo = $args['titulo'] ?? '';
        $this->precio = $args['precio'] ?? '';
        $this->imagen = $args['imagen'] ?? '';
        $this->descripcion = $args['descripcion'] ?? '';
        $this->habitaciones = $args['habitaciones'] ?? '';
        $this->wc = $args['wc'] ?? '';
        $this->estacionamiento = $args['estacionamiento'] ?? '';
        $this->creado = date('d/m/y');
        $this->vendedores_id = $args['vendedores_id'] ?? '';
    }
    
    public function guardar(){

        //Sanetizar los datos
        $atributos = $this->sanetizar();

        // Insertar en base de datos
        $query = "INSERT INTO propiedades (" ;
        $query .= join(', ', array_keys($atributos));
        $query .= " ) VALUES (' "; 
        $query .= join("', '", array_values($atributos));
        $query .= " ') ";

        $resultado = self::$bd->query($query);

        return $resultado;  
    }

    //Identificar y unir los atributos de la basde de datos
    public function atributos(){
        $atributos = [];
        foreach (self::$columnasDB as $columna){
            if ($columna === 'id') continue;
            $atributos[$columna] = $this->$columna;
        }
        return $atributos;
    }   

    public function sanetizar(){

        $atributos = $this->atributos();
        $sanetizado = [];

        foreach ($atributos as $key => $value) {
            $sanetizado[$key] = self::$bd->escape_string($value);
        }

        return $sanetizado;
    }

    //subida de archivos
    public function setImage($imagen){
        //asignar al atributo de imagen el nombre de la imagen
        if ($imagen) {
            $this->imagen = $imagen;
        }
    }

    //Validacion 
    public static function getErrores(){
        return self::$errores;
    }

    public function validar(){
        // ** Validación de formulario **

        if (!$this->titulo) {
           self::$errores[] = 'Debes añadir un título';
        }

        if (!$this->precio) {
           self::$errores[] = 'El precio es obligatorio';
        }

        if (strlen($this->descripcion) < 25) {
           self::$errores[] = 'La descripción es obligatoria y debe tener al menos 25 caracteres';
        }

        if (!$this->habitaciones) {
           self::$errores[] = 'El número de habitaciones es obligatorio';
        }

        if (!$this->wc) {
           self::$errores[] = 'El número de baños es obligatorio';
        }

        if (!$this->estacionamiento) {
           self::$errores[] = 'El número de estacionamientos es obligatorio';
        }

        if (!$this->vendedores_id) {
           self::$errores[] = 'Debes elegir un vendedor';
        }

        if (!$this->imagen) {
           self::$errores[] = 'La imagen es obligatioria';
        }
        
        return self::$errores;
    }
}