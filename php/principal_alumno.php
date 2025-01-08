<?php
include_once '../bbdd/connect.php';
session_start();  // Iniciar la sesión

// Verificar si el usuario ha iniciado sesión y si tiene el rol 'alumno'
if (!isset($_SESSION['usuario']) || $_SESSION['usuario'] == null || $_SESSION['rol'] != 'alumno') {
    // Si no está logueado o no tiene el rol adecuado, redirigir al login
    header("Location: login.php");
    exit();  // Asegurarse de que no se siga ejecutando el script después de redirigir
}

// Aquí obtienes el ID del usuario desde la sesión
$id_usuario = $_SESSION['usuario'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Principal - Alumno</title>
    <link rel="stylesheet" href="../css/estilo_pagprincipal.css">
</head>
<body>
    <!-- Logo -->
    <img src="../images/logo_pintacuadri.png" width="15%">

    <!-- Menú de navegación -->
    <div id="menu">
        <ul>
            <!-- Aquí puedes añadir enlaces de navegación si es necesario -->
            <li><a href="curso_asignaturas.php">Mi curso y asignaturas</a></li>
        </ul>
    </div>

    <!-- Botón de desconexión -->
    <form action='logoff.php' method='post'>
        <input type='submit' name='desconectar' class="btn" 
               value='Desconectar usuario <?php echo htmlspecialchars($_SESSION['usuario']); ?>' />
    </form>

    <!-- Pie de página -->
    <p class="mt-5 mb-3 text-muted">&copy; Cuadrilla Five</p>
</body>
</html>