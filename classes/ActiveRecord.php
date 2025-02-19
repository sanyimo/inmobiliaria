<?php
namespace App;

class ActiveRecord {
    
    // Base DE DATOS
    protected static $db;
    protected static $tabla = '';
    protected static $columnasDB = [];

    // Propiedades comunes
    protected $id;
    protected $imagen; 

    //errores
    protected static $errores = [];

    //definir la conexion a la BD
    public static function setDB($database) {
        self::$db = $database;
    }

    // Registros - CRUD
    public function guardar() {
    if(isset($this->id)) {
        $this->actualizar();
    } else {
        $this->crear();
    }
}
    public function crear() {
        //sanitizar los datos
        $atributos = $this->sanitizarAtributos();

        // Insertar en la base de datos
        $query = " INSERT INTO " . static::$tabla . " ( ";
        $query .= join(', ', array_keys($atributos));
        $query .= " ) VALUES (' "; 
        $query .= join("', '", array_values($atributos));
        $query .= " ')";
        
        $resultado = self::$db->query($query);
        // Mensaje de exito
        if($resultado) {
            // Redireccionar al usuario.
            header('Location: /admin?resultado=1');
        }
    }

    //actualizar
    public function actualizar() {
        // Sanitizar los datos
        $atributos = $this->sanitizarAtributos();

        $valores = [];
        foreach($atributos as $key => $value) {
            $valores[] = "{$key}='{$value}'";
        }
        $query = "UPDATE " . static::$tabla . " SET ";
        $query .=  join(', ', $valores );
        $query .= " WHERE id = '" . self::$db->escape_string($this->id) . "' ";
        $query .= " LIMIT 1 "; 

        $resultado = self::$db->query($query);

        if($resultado) {
            // Redireccionar al usuario.
            header('Location: /admin?resultado=2');
        }
    }

    // Eliminar un registro
    public function eliminar() {
        $query = "DELETE FROM " . static::$tabla . " WHERE id = " . self::$db->escape_string($this->id) . " LIMIT 1";
        $resultado = self::$db->query($query);

        if($resultado) {
            $this->borrarImagen();
            header('location: /admin?resultado=3');
        }
    }

    //identificar y unir los atributos de la BD
    public function atributos() {
        $atributos = [];

        foreach(static::$columnasDB as $columna) {
            if ($columna === 'id') continue;
            $atributos[$columna] = $this->$columna;
        }
        return $atributos;
    }

    public function sanitizarAtributos() {
        $atributos = $this->atributos();
        $sanitizado = [];
        foreach($atributos as $key => $value ) {
            $sanitizado[$key] = self::$db->escape_string($value);
        }
        return $sanitizado;

    }
    // Subida de archivos
    public function setImagen($imagen) {
        // Elimina la imagen previa
        if (!is_null($this->id) && isset($this->imagen)) {
        $this->borrarImagen();
    }
        // Asignar al atributo de imagen el nombre de la imagen
        if($imagen) {
            $this->imagen = $imagen;
        }
    }

    public function borrarImagen() {
        // Comprobar si existe el archivo
        if (!isset($this->imagen) || empty($this->imagen)) {
            return;
        }
        $existeArchivo = file_exists(CARPETA_IMAGENES . $this->imagen);
        $existeVarchivo = file_exists(CARPETA_VENDEDORES. $this->imagen);
        if($existeArchivo) {
            unlink(CARPETA_IMAGENES . $this->imagen);
        } else if($existeVarchivo) {
            unlink(CARPETA_VENDEDORES . $this->imagen);
        }
    }

    //validacion
    public static function getErrores() {
        return static::$errores;
    }

    public function validar() {
        static::$errores = [];
        return static::$errores;
    }
    public static function all() {
        $query = "SELECT * FROM " . static::$tabla;

        $resultado = self::consultarSQL($query);

        return $resultado;
    }
    //obtiene eterminado numero de registros
    public static function get($cantidad) {
        $query = "SELECT * FROM " . static::$tabla . " LIMIT " .$cantidad;

        $resultado = self::consultarSQL($query);

        return $resultado;
    }

      // Busca un registro por su id
      public static function find($id) {
        $id = self::$db->escape_string($id);
        $query = "SELECT * FROM " . static::$tabla . " WHERE id = {$id}";

        $resultado = self::consultarSQL($query);

        return array_shift( $resultado ) ;
    }
    public static function consultarSQL($query) {
        // Consultar la base de datos
        $resultado = self::$db->query($query);

        // Iterar los resultados
        $array = [];
        while($registro = $resultado->fetch_assoc()) {
            $array[] = static::crearObjeto($registro);
        }
        // liberar la memoria
        $resultado->free();

        // retornar los resultados
        return $array;
    }
    protected static function crearObjeto($registro) {
        $objeto = new static;

        foreach($registro as $key => $value ) {
            if(property_exists( $objeto, $key  )) {
                $objeto->$key = $value;
            }
        }
        return $objeto;
    }

    //Sincronizar el objeto en memoria con los cambios realizados por el usuario
    public function sincronizar($args=[]) { 
        foreach($args as $key => $value) {
          if(property_exists($this, $key) && !is_null($value)) {
            $this->$key = $value;
          }
        }
    }
}