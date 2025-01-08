<?php
include_once '../bbdd/connect.php';

// Obtener todos los cursos y profesores
$cursos = obtenerCursos($pdo);
$profesores = $pdo->query("SELECT id_profesor, nombre FROM profesores")->fetchAll(PDO::FETCH_ASSOC);

// Función para añadir una asignatura
if (isset($_POST['add'])) {
    $nombre_asignatura = $_POST['nombre_asignatura'];
    $id_curso = $_POST['id_curso'];
    $id_profesor = $_POST['id_profesor'];

    // Añadir asignatura
    añadirAsignatura($pdo, $id_curso, $id_profesor, $nombre_asignatura);
}

// Función para eliminar una asignatura
if (isset($_POST['delete'])) {
    $id_asignatura = $_POST['id_asignatura'];
    eliminarAsignatura($pdo, $id_asignatura);
}

// Función para modificar una asignatura
if (isset($_POST['modify'])) {
    $id_asignatura = $_POST['id_asignatura'];
    $nombre_asignatura = $_POST['nombre_asignatura'];
    $id_curso = $_POST['id_curso'];
    $id_profesor = $_POST['id_profesor'];

    // Modificar asignatura
    modificarAsignatura($pdo, $id_asignatura, $nombre_asignatura, $id_curso, $id_profesor);
}

// Obtener todas las asignaturas
$asignaturas = obtenerAsignaturas($pdo);

// Si se desea editar una asignatura, obtenemos su información
if (isset($_GET['edit'])) {
    $id_asignatura = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM asignaturas WHERE id_asignatura = ?");
    $stmt->execute([$id_asignatura]);
    $asignatura_edit = $stmt->fetch(PDO::FETCH_ASSOC);
    $cursos = obtenerCursos($pdo);
    $profesores = $pdo->query("SELECT id_profesor, nombre FROM profesores")->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Asignaturas</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>
    <h1>Gestión de Asignaturas</h1>

    <!-- Formulario para modificar una asignatura -->
    <?php if (isset($asignatura_edit)): ?>
        <h2>Modificar Asignatura</h2>
        <form method="post">
            <input type="hidden" name="id_asignatura" value="<?php echo $asignatura_edit['id_asignatura']; ?>">

            <input type="text" name="nombre_asignatura" value="<?php echo $asignatura_edit['nombre']; ?>" required><br>

            <select name="id_curso" required>
                <option value="" disabled>Selecciona un curso</option>
                <?php foreach ($cursos as $curso): ?>
                    <option value="<?php echo $curso['id_curso']; ?>" <?php echo $curso['id_curso'] == $asignatura_edit['id_curso'] ? 'selected' : ''; ?>>
                        <?php echo $curso['nombre']; ?>
                    </option>
                <?php endforeach; ?>
            </select><br>

            <select name="id_profesor" required>
                <option value="" disabled>Selecciona un profesor</option>
                <?php foreach ($profesores as $profesor): ?>
                    <option value="<?php echo $profesor['id_profesor']; ?>" <?php echo $profesor['id_profesor'] == $asignatura_edit['id_profesor'] ? 'selected' : ''; ?>>
                        <?php echo $profesor['nombre']; ?>
                    </option>
                <?php endforeach; ?>
            </select><br>

            <button type="submit" name="modify">Modificar Asignatura</button>
        </form>
    <?php endif; ?>

    <!-- Tabla de asignaturas registradas -->
    <h2>Asignaturas Registradas</h2>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Curso</th>
                    <th>Profesor</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($asignaturas as $asignatura): ?>
                <tr>
                    <td><?php echo $asignatura['asignatura']; ?></td>
                    <td><?php echo $asignatura['curso']; ?></td>
                    <td><?php echo $asignatura['profesor']; ?></td>
                    <td>
                        <!-- Formulario para eliminar la asignatura -->
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="id_asignatura" value="<?php echo $asignatura['id_asignatura']; ?>">
                            <button type="submit" name="delete" onclick="return confirm('¿Estás seguro de que quieres eliminar esta asignatura?');">Eliminar</button>
                        </form>
                        <br><br>
                        <!-- Formulario para editar la asignatura -->
                        <form method="get" style="display:inline;">
                            <input type="hidden" name="edit" value="<?php echo $asignatura['id_asignatura']; ?>">
                            <button type="submit">Editar</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Formulario para añadir una nueva asignatura -->
    <h2>Añadir Asignatura</h2>
    <form method="post">
        <input type="text" name="nombre_asignatura" placeholder="Nombre de la asignatura" required><br>

        <select name="id_curso" required>
            <option value="" disabled selected>Selecciona un curso</option>
            <?php foreach ($cursos as $curso): ?>
                <option value="<?php echo $curso['id_curso']; ?>"><?php echo $curso['nombre']; ?></option>
            <?php endforeach; ?>
        </select><br>

        <select name="id_profesor" required>
            <option value="" disabled selected>Selecciona un profesor</option>
            <?php foreach ($profesores as $profesor): ?>
                <option value="<?php echo $profesor['id_profesor']; ?>"><?php echo $profesor['nombre']; ?></option>
            <?php endforeach; ?>
        </select><br>

        <button type="submit" name="add">Añadir Asignatura</button>
    </form>

    <!-- Botón para volver a la página principal -->
    <br>
    <form action='principal_admin.php' method='post'>
        <input type='submit' value='VOLVER' />
    </form>

</body>
</html>
