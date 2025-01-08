<?php
include_once '../bbdd/connect.php';

// Funciones para añadir, modificar y eliminar cursos
if (isset($_POST['add'])) {
    $nombre = $_POST['nombre'];
    // Añadir curso
    añadirCurso($pdo, $nombre);
}

if (isset($_POST['modify'])) {
    $id_curso = $_POST['id_curso'];
    $nombre = $_POST['nombre'];
    // Modificar curso
    modificarCurso($pdo, $id_curso, $nombre);
}

if (isset($_POST['delete'])) {
    $id_curso = $_POST['id_curso'];
    // Eliminar curso
    eliminarCurso($pdo, $id_curso);
}

// Obtener cursos registrados
$cursos = obtenerCursos($pdo);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Cursos</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>
    <h1>Gestión de Cursos</h1>

    <!-- Tabla de cursos registrados -->
    <h2>Cursos Registrados</h2>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cursos as $curso): ?>
                <tr>
                    <td><?php echo $curso['nombre']; ?></td>
                    <td>
                        <!-- Formulario para modificar el curso -->
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="id_curso" value="<?php echo $curso['id_curso']; ?>">
                            <input type="text" name="nombre" placeholder="Nuevo nombre" value="<?php echo $curso['nombre']; ?>" required>
                            <button type="submit" name="modify">Modificar</button>
                        </form>
                        <br>
                        <!-- Formulario para eliminar el curso -->
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="id_curso" value="<?php echo $curso['id_curso']; ?>">
                            <button type="submit" name="delete" onclick="return confirm('¿Estás seguro de que quieres eliminar este curso?');">Eliminar</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Formulario para añadir un curso -->
    <h2>Añadir Curso</h2>
    <form method="post">
        <input type="text" name="nombre" placeholder="Nombre del curso" required><br>
        <button type="submit" name="add">Añadir Curso</button>
    </form>

    <!-- Botón para volver a la página principal -->
    <br>
    <form action='principal_admin.php' method='post'>
        <input type='submit' value='VOLVER' />
    </form>
</body>
</html>
