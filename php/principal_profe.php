<?php
include_once '../bbdd/connect.php';
session_start();
if(!isset($_SESSION["usuario"]) || $_SESSION["usuario"]==null){
    header("Location: login.php");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<title>Aplicación de Gestión de Permisos</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../css/estilo_pagprincipal.css">
</head>
<body>

<img src="../images/logo_pintacuadri.png" width="15%">
 
<div id="menu">
<ul>
<li><a href="asignaturas_alumnos.php">Mis asignaturas y alumnos</a></li>
</ul>
</div>
 
<form action='logoff.php' method='post'>
<input type='submit' name='desconectar' class="btn" 
               value='Desconectar usuario <?php echo $_SESSION['usuario']; ?>' />
</form>
 
<p class="mt-5 mb-3 text-muted">&copy; Cuadrilla Five</p>
</body>
</html>