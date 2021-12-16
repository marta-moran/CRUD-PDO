<?php
include_once "Producto.php";

function accionBorrar ($nprod){    
    $db = AccesoDatos::getModelo();
    $tprod = $db->borrarProducto($nprod);
}

function accionTerminar(){
    AccesoDatos::closeModelo();
    session_destroy();
}
 
function accionAlta(){
    $prod = new Producto();
    $prod-> PRODUCTO_NO  = "";
    $prod-> DESCRIPCION = "";
    $prod-> PRECIO_ACTUAL  = "";
    $prod-> STOCK_DISPONIBLE = "";
    $orden= "Nuevo";
    include_once "layout/formulario.php";
}

function accionDetalles($nprod){
    $db = AccesoDatos::getModelo();
    $prod = $db->getProducto($nprod);
    $orden = "Detalles";
    include_once "layout/formulario.php";
}


function accionModificar($nprod){
    $db = AccesoDatos::getModelo();
    $prod = $db->getProducto($nprod);
    $orden="Modificar";
    include_once "layout/formulario.php";
}

function accionPostAlta(){
    limpiarArrayEntrada($_POST); //Evito la posible inyección de código
    $prod = new Producto();
    $prod-> PRODUCTO_NO  = $_POST['PRODUCTO_NO'];
    $prod-> DESCRIPCION = $_POST['DESCRIPCION'];
    $prod->  PRECIO_ACTUAL  = $_POST['PRECIO_ACTUAL'];
    $prod-> STOCK_DISPONIBLE = $_POST['STOCK_DISPONIBLE'];
    $db = AccesoDatos::getModelo();
    $db->addProducto($prod);
    
}

function accionPostModificar(){
    limpiarArrayEntrada($_POST); //Evito la posible inyección de código
    $prod = new Producto();
    $prod-> PRODUCTO_NO  = $_POST['PRODUCTO_NO'];
    $prod-> DESCRIPCION = $_POST['DESCRIPCION'];
    $prod-> PRECIO_ACTUAL  = $_POST['PRECIO_ACTUAL'];
    $prod-> STOCK_DISPONIBLE = $_POST['STOCK_DISPONIBLE'];
    $db = AccesoDatos::getModelo();
    $db->modProducto($prod);
    
}

