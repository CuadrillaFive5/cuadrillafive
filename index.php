<?php
// Configuración de conexión
define("HOST", "localhost");
define("USERNAME", "root");
define("PASSWORD", "");
define("DATABASE", "academiapintura");

function conectarSinBaseDeDatos() {
    try {
        $pdo = new PDO("mysql:host=" . HOST, USERNAME, PASSWORD);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Error al conectar al servidor MySQL: " . $e->getMessage());
    }
}

function conectarConBaseDeDatos() {
    try {
        $pdo = new PDO("mysql:host=" . HOST . ";dbname=" . DATABASE, USERNAME, PASSWORD);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Error al conectar a la base de datos: " . $e->getMessage());
    }
}

function crearBBDD($basedatos) {
    try {
        $conexion = conectarSinBaseDeDatos();
        $sql = "CREATE DATABASE IF NOT EXISTS $basedatos CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;";
        $conexion->exec($sql);
        return true; // Base de datos creada o ya existente
    } catch (PDOException $e) {
        echo "Error al crear la base de datos: " . $e->getMessage();
        return false; // Error
    }
}

function crearTablas($conexion) { 
    try {
        // Crear las tablas en el orden adecuado para las claves foráneas
        $tablas = [
            // Tabla usuarios
            "CREATE TABLE `usuarios` (
                `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
                `nombre` varchar(100) NOT NULL,
                `rol` enum('administrador','profesor','alumno') NOT NULL,
                `password` varchar(255) NOT NULL,
                PRIMARY KEY (`id_usuario`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;", 

            // Tabla cursos
            "CREATE TABLE `cursos` (
                `id_curso` int(11) NOT NULL AUTO_INCREMENT,
                `nombre` varchar(100) NOT NULL,
                PRIMARY KEY (`id_curso`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;", 

            // Tabla profesores
            "CREATE TABLE `profesores` (
                `id_profesor` int(11) NOT NULL AUTO_INCREMENT,
                `id_usuario` int(11) NOT NULL,
                `nombre` varchar(255) NOT NULL,
                PRIMARY KEY (`id_profesor`),
                KEY `id_usuario` (`id_usuario`),
                CONSTRAINT `profesores_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;", 

            // Tabla alumnos
            "CREATE TABLE `alumnos` (
                `id_alumno` int(11) NOT NULL AUTO_INCREMENT,
                `id_usuario` int(11) NOT NULL,
                `nombre` varchar(255) NOT NULL,
                `id_curso` int(11) NOT NULL,
                PRIMARY KEY (`id_alumno`),
                KEY `id_usuario` (`id_usuario`),
                KEY `id_curso` (`id_curso`),
                CONSTRAINT `alumnos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE,
                CONSTRAINT `alumnos_ibfk_2` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id_curso`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;", 

            // Tabla asignaturas
            "CREATE TABLE `asignaturas` (
                `id_asignatura` int(11) NOT NULL AUTO_INCREMENT,
                `id_curso` int(11) NOT NULL,
                `id_profesor` int(11) NOT NULL,
                `nombre` varchar(100) NOT NULL,
                PRIMARY KEY (`id_asignatura`),
                KEY `id_curso` (`id_curso`),
                KEY `id_profesor` (`id_profesor`),
                CONSTRAINT `asignaturas_ibfk_1` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id_curso`) ON DELETE CASCADE,
                CONSTRAINT `asignaturas_ibfk_2` FOREIGN KEY (`id_profesor`) REFERENCES `profesores` (`id_profesor`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;"
        ];

        // Ejecutamos todas las consultas SQL de creación
        foreach ($tablas as $tabla) {
            $conexion->exec($tabla);
        }
        return true; // Tablas creadas correctamente
    } catch (PDOException $e) {
        echo "Error al crear las tablas: " . $e->getMessage();
        return false; // Error
    }
}

// Ejecución principal
try {
    // Crear base de datos si no existe
    if (crearBBDD(DATABASE)) {
        // Conectar a la base de datos recién creada o existente
        $conexion = conectarConBaseDeDatos();
        // Crear tablas
        if (crearTablas($conexion)) {
            // Redirigir al login si todo salió bien
            header("Location: php/login.php");
            exit(); // Asegura que no se ejecute más código
        }
    }
} catch (Exception $e) {
    echo "Ocurrió un error general: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Creación de Base de Datos</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            text-align: center;
            font-family: Arial, sans-serif;
        }
    </style>
</head>
<body>
    <h1>Bienvenido a PintaCuadri</h1>
</body>
</html>
