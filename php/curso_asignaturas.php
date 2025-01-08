<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
    die("Error: No has iniciado sesión.");
}

include_once '../bbdd/connect.php'; // Asegúrate de que la ruta es correcta

// Obtener el ID del usuario que ha iniciado sesión
$id_usuario = $_SESSION['id_usuario'];

$alumnos_cursos = obtenerCursosDeUsuario($pdo, $id_usuario);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cursos y Asignaturas</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>
    <h1>Cursos y Asignaturas</h1>
    <div class="table-container">
        <table border="1">
            <thead>
                <tr>
                    <th>Curso</th>
                    <th>Asignatura</th>
                    <th>Profesor</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($alumnos_cursos as $curso): ?>
                    <tr>
                        <td><?= ($curso['curso']) ?></td>
                        <td><?= ($curso['asignatura']) ?></td>
                        <td><?= ($curso['profesor']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
     <!-- Botón para volver a la página principal -->
    <br>
    <form action='principal_alumno.php' method='post'>
        <input type='submit' value='VOLVER' />
    </form>
</body>
</html>
