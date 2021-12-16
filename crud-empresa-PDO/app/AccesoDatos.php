<?php
include_once "Producto.php";
include_once "config.php";

/*
 * Acceso a datos con BD Usuarios y Patrón Singleton 
 * Un único objeto para la clase
 */
class AccesoDatos {
    
    private static $modelo = null;
    private $dbh = null;
    private $stmt_productos = null;
    private $stmt_producto = null;
    private $stmt_borProd = null;
    private $stmt_modProd  = null;
    private $stmt_creaProd = null;
    
    public static function getModelo(){
        if (self::$modelo == null){
            self::$modelo = new AccesoDatos();
        }
        return self::$modelo;
    }
    
    

   // Constructor privado  Patron singleton
   
    private function __construct(){
        
        try {
            $dsn = "mysql:host=".SERVER_DB.";dbname=EMPRESA;charset=utf8";
            $this->dbh = new PDO($dsn, "root", "Root2323$");
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e){
            echo "Error de conexión ".$e->getMessage();
            exit();
        }
        // Construyo las consultas
        $this->stmt_productos  = $this->dbh->prepare("select * from PRODUCTOS");
        $this->stmt_producto   = $this->dbh->prepare("select * from PRODUCTOS where PRODUCTO_NO=:PRODUCTO_NO");
        $this->stmt_borProd   = $this->dbh->prepare("delete from PRODUCTOS where PRODUCTO_NO =:PRODUCTO_NO");
        $this->stmt_modProd   = $this->dbh->prepare("update PRODUCTOS set DESCRIPCION=:DESCRIPCION, PRECIO_ACTUAL=:precio_actual, STOCK_DISPONIBLE=:STOCK_DISPONIBLE where PRODUCTO_NO=:PRODUCTO_NO");
        $this->stmt_creaProd = $this->dbh->prepare("insert into PRODUCTOS (PRODUCTO_NO, DESCRIPCION, PRECIO_ACTUAL, STOCK_DISPONIBLE) Values(?,?,?,?)");
    }

    // Cierro la conexión anulando todos los objectos relacioanado con la conexión PDO (stmt)
    public static function closeModelo(){
        if (self::$modelo != null){
            $obj = self::$modelo;
            $obj->stmt_productos = null;
            $obj->stmt_producto  = null;
            $obj->stmt_borProducto  = null;
            $obj->stmt_modProducto  = null;
            $obj->stmt_creaProducto = null;
            $obj->dbh = null;
            self::$modelo = null; // Borro el objeto.
        }
    }


    // Devuelvo la lista de Usuarios
    public function getProductos ():array {
        $tprod = [];
        $this->stmt_productos->setFetchMode(PDO::FETCH_CLASS, 'Producto');
        
        if ( $this->stmt_productos->execute() ){
            while ( $prod = $this->stmt_productos->fetch()){
               $tprod[]= $prod;
            }
        }
        return $tprod;
    }
    
    // Devuelvo un usuario o false
    public function getProducto (String $nprod) {
        $prod = false;
        
        $this->stmt_producto->setFetchMode(PDO::FETCH_CLASS, 'Producto');
        $this->stmt_producto->bindParam(':PRODUCTO_NO', $nprod);
        if ( $this->stmt_producto->execute() ){
             if ( $obj = $this->stmt_producto->fetch()){
                $prod= $obj;
            }
        }
        return $prod;
    }
    
    // UPDATE
    public function modProducto($prod):bool{
      
        $this->stmt_modProd->bindValue(':PRODUCTO_NO',$prod->PRODUCTO_NO);
        $this->stmt_modProd->bindValue(':DESCRIPCION',$prod->DESCRIPCION);
        $this->stmt_modProd ->bindValue(':PRECIO_ACTUAL',$prod->PRECIO_ACTUAL);
        $this->stmt_modProd->bindValue(':STOCK_DISPONIBLE',$prod->STOCK_DISPONIBLE);
        $this->stmt_modProd->execute();
        $resu = ($this->stmt_modProd->rowCount () == 1);
        return $resu;
    }

    //INSERT
    public function addProducto($prod):bool{
        
        $this->stmt_creaProd->execute( [$prod->PRODUCTO_NO, $prod->DESCRIPCION, $prod->PRECIO_ACTUAL, $prod->STOCK_DISPONIBLE]);
        $resu = ($this->stmt_creaProd->rowCount () == 1);
        return $resu;
    }

    //DELETE
    public function borrarProducto(String $prod):bool {
        $this->stmt_borProd->bindParam(':PRODUCTO_NO', $prod);
        $this->stmt_borProd->execute();
        $resu = ($this->stmt_borProd->rowCount () == 1);
        return $resu;
    }   
    
     // Evito que se pueda clonar el objeto. (SINGLETON)
    public function __clone()
    { 
        trigger_error('La clonación no permitida', E_USER_ERROR); 
    }
}