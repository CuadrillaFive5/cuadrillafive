<?php
include_once '../bbdd/connect.php';

if (isset($_POST['add'])) {
    añadirUsuario($pdo, $_POST['nombre'], $_POST['rol'], $_POST['contraseña']);
}

if (isset($_POST['modify'])) {
    modificarUsuario($pdo, $_POST['id'], $_POST['nombre'], $_POST['rol'], $_POST['contraseña']);
}

if (isset($_POST['delete'])) {
    eliminarUsuario($pdo, $_POST['id']);
}

if (isset($_POST['change_course'])) {
    cambiarCurso($pdo, $_POST['id_alumno'], $_POST['nuevo_curso']);
}

$usuarios = obtenerUsuarios($pdo);
$cursos = obtenerCursos($pdo);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>
    <h1>Gestión de Usuarios</h1>

    <!-- Tabla de usuarios registrados -->
    <h2>Usuarios Registrados</h2>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Rol</th>
                    <th>Curso</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td><?php echo $usuario['nombre']; ?></td>
                    <td><?php echo ucfirst($usuario['rol']); ?></td>
                    <td>
                        <?php
                            // Mostrar el curso si es un alumno
                            if ($usuario['rol'] == 'alumno') {
                                echo $usuario['curso_nombre'];
                            } elseif ($usuario['rol'] == 'profesor') {
                                echo 'No curso'; // Profesor no tiene curso
                            } else {
                                echo 'No curso'; // Administrador no tiene curso
                            }
                        ?>
                    </td>
                    <td>
                        <!-- Formulario para modificar el usuario -->
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $usuario['id_usuario']; ?>">
                            <input type="text" name="nombre" placeholder="Nuevo nombre" value="<?php echo $usuario['nombre']; ?>" required>
                            <select name="rol" required>
                                <option value="administrador" <?php if ($usuario['rol'] == 'administrador') echo 'selected'; ?>>Administrador</option>
                                <option value="alumno" <?php if ($usuario['rol'] == 'alumno') echo 'selected'; ?>>Alumno</option>
                                <option value="profesor" <?php if ($usuario['rol'] == 'profesor') echo 'selected'; ?>>Profesor</option>
                            </select>
                            <input type="password" name="contraseña" placeholder="Nueva contraseña" required>
                            <button type="submit" name="modify">Modificar</button>
                        </form>
                        <br>
                        <!-- Formulario para eliminar el usuario -->
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $usuario['id_usuario']; ?>">
                            <br>
                            <button type="submit" name="delete" onclick="return confirm('¿Estás seguro de que quieres eliminar este usuario?');">Eliminar</button>
                        </form>
                        <br>
                        <!-- Formulario para cambiar el curso (solo alumnos) -->
                        <?php if ($usuario['rol'] == 'alumno'): ?>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="id_alumno" value="<?php echo $usuario['id_usuario']; ?>">
                                <select name="nuevo_curso" required>
                                    <?php foreach ($cursos as $curso): ?>
                                        <option value="<?php echo $curso['id_curso']; ?>" <?php if ($curso['nombre'] == $usuario['curso_nombre']) echo 'selected'; ?>>
                                            <?php echo $curso['nombre']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" name="change_course">Cambiar Curso</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Formulario para añadir un usuario -->
    <h2>Añadir Usuario</h2>
    <form method="post">
        <input type="text" name="nombre" placeholder="Nombre" required><br>
        <select name="rol" required>
            <option value="administrador">Administrador</option>
            <option value="alumno">Alumno</option>
            <option value="profesor">Profesor</option>
        </select><br>
        <input type="password" name="contraseña" placeholder="Contraseña" required><br>
        <button type="submit" name="add">Añadir Usuario</button>
    </form>

    <!-- Botón para volver a la página principal -->
    <br>
    <form action='principal_admin.php' method='post'>
        <input type='submit' value='VOLVER' />
    </form>
</body>
</html>