<?php

include_once 'constantes.php';

function conectarConBaseDeDatos() {
    try {
        $pdo = new PDO("mysql:host=" . HOST . ";dbname=" . DATABASE, USERNAME, PASSWORD);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Error al conectar a la base de datos: " . $e->getMessage());
    }
}

function obtenerUsuarioPorNombre($pdo, $nombreUsuario) {
    $query = "SELECT id_usuario, nombre, rol, password FROM usuarios WHERE nombre = :nombreUsuario";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':nombreUsuario', $nombreUsuario, PDO::PARAM_STR);
    $stmt->execute();

    // Si el usuario existe, se retorna el array con los datos
    if ($stmt->rowCount() > 0) {
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        return $usuario;
    } else {
        return false; // No se encuentra el usuario
    }
}

// Función para obtener la asignatura correspondiente al alumno
function obtenerAsignaturasPorAlumno($pdo, $id_alumno) {
    $query = "SELECT asignaturas.nombre AS asignatura 
              FROM asignaturas 
              JOIN cursos ON asignaturas.id_curso = cursos.id_curso 
              JOIN alumnos ON cursos.id_curso = alumnos.id_curso 
              WHERE alumnos.id_alumno = :id_alumno";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id_alumno', $id_alumno, PDO::PARAM_INT);
    $stmt->execute();
    
    // Devuelve el nombre de la asignatura
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Función para obtener las asignaturas correspondientes al profesor
function obtenerAsignaturasPorProfesor($pdo, $id_profesor) {
    $query = "SELECT asignaturas.nombre AS asignatura
              FROM asignaturas 
              WHERE asignaturas.id_profesor = :id_profesor";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id_profesor', $id_profesor, PDO::PARAM_INT);
    $stmt->execute();
    
    // Devuelve el nombre de la asignatura
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Función para obtener todos los profesores
function obtenerProfesores($pdo) {
    $query = "SELECT p.id_profesor, p.id_usuario, p.nombre, u.nombre AS nombre_usuario
              FROM profesores p
              JOIN usuarios u ON p.id_usuario = u.id_usuario";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerCursosDeUsuario($pdo, $id_usuario) {
    // Consulta SQL con JOIN para obtener nombre completo del profesor
    $sql = "
        SELECT 
            c.nombre AS curso, 
            a.nombre AS asignatura, 
            CONCAT(p.nombre) AS profesor
        FROM 
            alumnos al
        JOIN 
            cursos c ON al.id_curso = c.id_curso
        JOIN 
            asignaturas a ON a.id_curso = c.id_curso
        JOIN 
            profesores p ON a.id_profesor = p.id_profesor
        WHERE 
            al.id_usuario = :id_usuario";

    // Ejecutamos la consulta
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC); // Devolvemos los resultados
}

// Función para cambiar el curso de un alumno
function cambiarCurso($pdo, $id_alumno, $nuevo_curso) {
    $sql = "UPDATE alumnos SET id_curso = :nuevo_curso WHERE id_usuario = :id_alumno";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nuevo_curso', $nuevo_curso, PDO::PARAM_INT);
    $stmt->bindParam(':id_alumno', $id_alumno, PDO::PARAM_INT);
    $stmt->execute();
    echo "Curso cambiado correctamente.<br>";
}

// Función para añadir un nuevo usuario
function añadirUsuario($pdo, $nombre, $rol, $contraseña) {
    $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, rol, password) VALUES (?, ?, ?)");
    $stmt->execute([$nombre, $rol, md5($contraseña)]);
    echo "Usuario añadido correctamente.<br>";

    // Si el rol es profesor, agregarlo a la tabla de profesores
    if ($rol === 'profesor') {
        añadirProfesor($pdo, $nombre);
    }
    
    // Si el rol es alumno, agregarlo a la tabla de alumnos
    if ($rol === 'alumno') {
        añadirAlumno($pdo, $nombre);
    }
}

// Función para modificar un usuario
function modificarUsuario($pdo, $id, $nombre, $rol, $contraseña) {
    $stmt = $pdo->prepare("UPDATE usuarios SET nombre = ?, rol = ?, password = ? WHERE id_usuario = ?");
    $stmt->execute([$nombre, $rol, md5($contraseña), $id]);
    echo "Usuario modificado correctamente.<br>";

    // Si el rol es profesor, actualizar la tabla de profesores
    if ($rol === 'profesor') {
        modificarProfesor($pdo, $id, $nombre);
    }
    
    // Si el rol es alumno, actualizar la tabla de alumnos
    if ($rol === 'alumno') {
        modificarAlumno($pdo, $id, $nombre);
    }
}

// Función para eliminar un usuario
function eliminarUsuario($pdo, $id) {
    // Eliminar de las tablas relacionadas antes de eliminar de la tabla usuarios
    $rol = obtenerRolUsuario($pdo, $id);
    if ($rol === 'profesor') {
        eliminarProfesor($pdo, $id);
    } elseif ($rol === 'alumno') {
        eliminarAlumno($pdo, $id);
    }

    $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
    $stmt->execute([$id]);
    echo "Usuario eliminado correctamente.<br>";
}

// Función para obtener todos los usuarios
function obtenerUsuarios($pdo) {
    $sql = "SELECT u.id_usuario, u.nombre, u.rol, a.id_curso, p.nombre AS profesor_nombre, c.nombre AS curso_nombre
            FROM usuarios u
            LEFT JOIN alumnos a ON u.id_usuario = a.id_usuario
            LEFT JOIN profesores p ON u.id_usuario = p.id_usuario
            LEFT JOIN cursos c ON a.id_curso = c.id_curso";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerAlumnos($pdo) {
    $stmt = $pdo->query("SELECT usuarios.id_usuario, usuarios.nombre, usuarios.rol, alumnos.id_alumno, alumnos.id_curso
                         FROM usuarios 
                         JOIN alumnos ON usuarios.id_usuario = alumnos.id_usuario");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Función para obtener el rol de un usuario
function obtenerRolUsuario($pdo, $id_usuario) {
    $stmt = $pdo->prepare("SELECT rol FROM usuarios WHERE id_usuario = ?");
    $stmt->execute([$id_usuario]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['rol'] ?? null;
}

// Función para añadir un alumno
function añadirAlumno($pdo, $nombre) {
    $stmt = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE nombre = ?");
    $stmt->execute([$nombre]);
    $id_usuario = $stmt->fetchColumn();
    
    if ($id_usuario) {
        $id_curso = 1;
        $stmt = $pdo->prepare("INSERT INTO alumnos (id_usuario, nombre, id_curso) VALUES (?, ?, ?)");
        $stmt->execute([$id_usuario, $nombre, $id_curso]);
        echo "Alumno añadido correctamente.<br>";
    } else {
        echo "Error: No se encontró el usuario.<br>";
    }
}

// Función para añadir un profesor
function añadirProfesor($pdo, $nombre) {
    // Se necesita el id_usuario para agregar un profesor
    $stmt = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE nombre = ?");
    $stmt->execute([$nombre]);
    $id_usuario = $stmt->fetchColumn();
    
    if ($id_usuario) {
        $stmt = $pdo->prepare("INSERT INTO profesores (id_usuario, nombre) VALUES (?, ?)");
        $stmt->execute([$id_usuario, $nombre]);
        echo "Profesor añadido correctamente.<br>";
    } else {
        echo "Error: No se encontró el usuario.<br>";
    }
}

// Función para modificar un alumno
function modificarAlumno($pdo, $id_usuario, $nombre) {
    $stmt = $pdo->prepare("UPDATE alumnos SET nombre = ? WHERE id_usuario = ?");
    $stmt->execute([$nombre, $id_usuario]);
    echo "Alumno modificado correctamente.<br>";
}

// Función para modificar un profesor
function modificarProfesor($pdo, $id_usuario, $nombre) {
    $stmt = $pdo->prepare("UPDATE profesores SET nombre = ? WHERE id_usuario = ?");
    $stmt->execute([$nombre, $id_usuario]);
    echo "Profesor modificado correctamente.<br>";
}

// Función para eliminar un alumno
function eliminarAlumno($pdo, $id_usuario) {
    $stmt = $pdo->prepare("DELETE FROM alumnos WHERE id_usuario = ?");
    $stmt->execute([$id_usuario]);
    echo "Alumno eliminado correctamente.<br>";
}

// Función para eliminar un profesor
function eliminarProfesor($pdo, $id_usuario) {
    $stmt = $pdo->prepare("DELETE FROM profesores WHERE id_usuario = ?");
    $stmt->execute([$id_usuario]);
    echo "Profesor eliminado correctamente.<br>";
}

// Función para obtener todos los cursos
function obtenerCursos($pdo) {
    $sql = "SELECT * FROM cursos";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Función para añadir un curso
function añadirCurso($pdo, $nombre) {
    $sql = "INSERT INTO cursos (nombre) VALUES (:nombre)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->execute();
}

// Función para modificar un curso existente
function modificarCurso($pdo, $id_curso, $nombre) {
    $sql = "UPDATE cursos SET nombre = :nombre WHERE id_curso = :id_curso";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':id_curso', $id_curso);
    $stmt->execute();
}

// Función para eliminar un curso
function eliminarCurso($pdo, $id_curso) {
    $sql = "DELETE FROM cursos WHERE id_curso = :id_curso";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id_curso', $id_curso);
    $stmt->execute();
}

// Función para añadir una asignatura
function añadirAsignatura($pdo, $id_curso, $id_profesor, $nombre) {
    $stmt = $pdo->prepare("INSERT INTO asignaturas (id_curso, id_profesor, nombre) VALUES (?, ?, ?)");
    $stmt->execute([$id_curso, $id_profesor, $nombre]);
    echo "Asignatura añadida correctamente.<br>";
}

// Función para obtener todas las asignaturas
function obtenerAsignaturas($pdo) {
    // Realizamos el JOIN para obtener los nombres correctos de los profesores
    $sql = "SELECT a.id_asignatura, a.nombre AS asignatura, c.nombre AS curso, p.nombre AS profesor
            FROM asignaturas a
            JOIN cursos c ON a.id_curso = c.id_curso
            JOIN profesores p ON a.id_profesor = p.id_profesor";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Función para eliminar una asignatura
function eliminarAsignatura($pdo, $id_asignatura) {
    $stmt = $pdo->prepare("DELETE FROM asignaturas WHERE id_asignatura = ?");
    $stmt->execute([$id_asignatura]);
    echo "Asignatura eliminada correctamente.<br>";
}

// Función para modificar una asignatura
function modificarAsignatura($pdo, $id_asignatura, $nombre_asignatura, $id_curso, $id_profesor) {
    $sql = "UPDATE asignaturas 
            SET nombre = :nombre_asignatura, id_curso = :id_curso, id_profesor = :id_profesor
            WHERE id_asignatura = :id_asignatura";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nombre_asignatura', $nombre_asignatura);
    $stmt->bindParam(':id_curso', $id_curso);
    $stmt->bindParam(':id_profesor', $id_profesor);
    $stmt->bindParam(':id_asignatura', $id_asignatura);
    $stmt->execute();
}

// conexión inicial
$pdo = conectarConBaseDeDatos();

?>