<?php
include_once '../bbdd/connect.php'; // Asegúrate de que la ruta a connect.php sea correcta
include_once '../bbdd/constantes.php';   // Asegúrate de que la ruta a constantes.php sea correcta

// Llamada a la función para realizar la conexión inicial
$pdo = conectarConBaseDeDatos();

// Obtener la lista de profesores
$profesores = obtenerProfesores($pdo);

// Obtener la lista de asignaturas
$asignaturas = obtenerAsignaturas($pdo);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignaturas y Profesores</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>
    <h1>Asignaturas y Profesores</h1>
    
    <h2>Listado de Profesores</h2>
    <div class="table-container">
        <table border="1">
            <tr>
                <th>Nombre</th>
                <th>Asignaturas</th>
            </tr>
            <?php foreach ($profesores as $profesor): ?>
                <tr>
                    <td><?php echo $profesor['nombre']; ?></td>
                    <td>
                        <?php 
                        // Obtener las asignaturas del profesor
                        $asignaturasProfesor = obtenerAsignaturasPorProfesor($pdo, $profesor['id_profesor']);
                        foreach ($asignaturasProfesor as $asignatura) {
                            echo $asignatura['asignatura'] . "<br>";
                        }
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <br>
    <form action='principal_profe.php' method='post'>
        <input type='submit' value='VOLVER' />
    </form>
</body>
</html>
