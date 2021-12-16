<?php
include_once "Producto.php";
include_once "config.php";

/*
 * Acceso a datos con BD Usuarios : 
 * Usando la librería mysqli
 * Uso el Patrón Singleton :Un único objeto para la clase
 * Constructor privado, y métodos estáticos 
 */
class AccesoDatos {
    
    private static $modelo = null;
    private $dbh = null;
    
    public static function getModelo(){
        if (self::$modelo == null){
            self::$modelo = new AccesoDatos();
        }
        return self::$modelo;
    }
    
    

   // Constructor privado  Patron singleton
   
    private function __construct(){
        
       
         $this->dbh = new mysqli(DB_SERVER,DB_USER,DB_PASSWD,DATABASE);
         
      if ( $this->dbh->connect_error){
         die(" Error en la conexión ".$this->dbh->connect_errno);
        } 

    }

    // Cierro la conexión anulando todos los objectos relacioanado con la conexión PDO (stmt)
    public static function closeModelo(){
        if (self::$modelo != null){
            $obj = self::$modelo;
            // Cierro la base de datos
            $obj->dbh->close();
            self::$modelo = null; // Borro el objeto.
        }
    }


    // SELECT Devuelvo la lista de Productos
    public function getProductos():array {
        $tprod = [];
        // Crea la sentencia preparada
        $stmt_productos  = $this->dbh->prepare("select * from PRODUCTOS");
        // Si falla termian el programa
        if ( $stmt_productos == false) die (__FILE__.':'.__LINE__.$this->dbh->error);
        // Ejecuto la sentencia
        $stmt_productos->execute();
        // Obtengo los resultados
        $result = $stmt_productos->get_result();
        // Si hay resultado correctos
        if ( $result ){
            // Obtengo cada fila de la respuesta como un objeto de tipo Usuario
            while ( $prod = $result->fetch_object('Producto')){
               $tprod[]= $prod;
            }
        }
        // Devuelvo el array de objetos
        return $tprod;
    }
    
    // SELECT Devuelvo un producto o false
    public function getProducto (String $nprod) {
        $prod = false;
        
        $stmt_prod   = $this->dbh->prepare("select * from PRODUCTOS where PRODUCTO_NO =?");
        if ( $stmt_prod == false) die ($this->dbh->error);

        // Enlazo $login con el primer ? 
        $stmt_prod->bind_param("s",$nprod);
        $stmt_prod->execute();
        $result = $stmt_prod->get_result();
        if ( $result ){
            $prod = $result->fetch_object('Producto');
            }
        
        return $prod;
    }
    
    // UPDATE
    public function modProducto($prod):bool{
      
        $stmt_modprod   = $this->dbh->prepare("update PRODUCTOS set DESCRIPCION=?, PRECIO_ACTUAL=?, STOCK_DISPONIBLE=? where PRODUCTO_NO=?");
        if ( $stmt_modprod == false) die ($this->dbh->error);

        $stmt_modprod->bind_param("ssss", $prod->DESCRIPCION, $prod->PRECIO_ACTUAL, $prod->STOCK_DISPONIBLE, $prod->PRODUCTO_NO);
        $stmt_modprod->execute();
        $resu = ($this->dbh->affected_rows  == 1);
        return $resu;
    }

    //INSERT
    public function addProducto($prod):bool{
       
        $stmt_creaProduct  = $this->dbh->prepare("insert into PRODUCTOS (PRODUCTO_NO,DESCRIPCION,PRECIO_ACTUAL,STOCK_DISPONIBLE) Values(?,?,?,?)");
        if ( $stmt_creaProduct == false) die ($this->dbh->error);

        $stmt_creaProduct->bind_param("ssss",$prod->PRODUCTO_NO, $prod->DESCRIPCION, $prod->PRECIO_ACTUAL, $prod->STOCK_DISPONIBLE);
        $stmt_creaProduct->execute();
        $resu = ($this->dbh->affected_rows  == 1);
        return $resu;
    }

    //DELETE
    public function borrarProducto(String $prod):bool {
        $stmt_borProducto   = $this->dbh->prepare("delete from PRODUCTOS where PRODUCTO_NO =?");
        if ( $stmt_borProducto == false) die ($this->dbh->error);
       
        $stmt_borProducto->bind_param("s", $prod);
        $stmt_borProducto->execute();
        $resu = ($this->dbh->affected_rows  == 1);
        return $resu;
    }   
    
     // Evito que se pueda clonar el objeto. (SINGLETON)
    public function __clone()
    { 
        trigger_error('La clonación no permitida', E_USER_ERROR); 
    }
}

