<?php
include_once '../bbdd/connect.php';

session_start();  // Iniciar la sesión

// Verificar si se ha enviado el formulario de inicio de sesión
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    if (empty($usuario) || empty($password)) {
        $error = "Debes introducir un nombre de usuario y una contraseña";
    } else {
        try {
            // Obtener los datos del usuario desde la base de datos usando la función
            $user = obtenerUsuarioPorNombre($pdo, $usuario);

            if ($user) {
                if ($user && md5($password) === $user['password']) {
                    // Iniciar sesión y almacenar los datos relevantes
                    $_SESSION['id_usuario'] = $user['id_usuario'];
                    $_SESSION['usuario'] = $user['nombre'];
                    $_SESSION['rol'] = $user['rol'];

                    // Redirigir según el rol
                    switch ($user['rol']) {
                        case 'administrador':
                            header("Location: principal_admin.php");
                            exit();
                        case 'profesor':
                            header("Location: principal_profe.php");
                            exit();
                        case 'alumno':
                            header("Location: principal_alumno.php");
                            exit();
                        default:
                            $error = "Rol no reconocido. Contacta al administrador.";
                            break;
                    }
                } else {
                    $error = "¡Usuario o contraseña no válidos!";
                }
            } else {
                $error = "¡Usuario o contraseña no válidos!";
            }
        } catch (PDOException $e) {
            $error = "Error en la base de datos: " . $e->getMessage();
        }
    }
}
?>

<!-- HTML para el formulario -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>
    <div class="login-container">
        <form class="form-signin" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
            <img src="../images/logo_pintacuadri.png" alt="Logo" width="100%">
            
            <!-- Mensaje de error -->
            <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>
            
            <div class="form-group">
                <label for="usuario">Usuario:</label>
                <input type="text" name="usuario" id="usuario" class="form-control" placeholder="Usuario" required autofocus>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Contraseña" required>
            </div>
            
            <button class="btn btn-sm btn-primary" type="submit" name="login">Login</button>
            <p class="mt-3 text-muted">&copy; Cuadrilla Five</p>
        </form>
    </div>
</body>
</html>
