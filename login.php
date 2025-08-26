<?php

// Conectar a la BD
require 'includes/config/database.php';

$db = conectarDB();

// Autenticar el usuario

$errores = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    if (!$usuario) {
        setcookie('errores', "El usuario es obligatorio o no es válido");
    }

    if (!$password) {
        setcookie('errores', "El Password es obligatorio");
    }

    if (empty($errores)) { // Si no hay errores comprobar en BD
        // Revisar si el usuario existe.
        $query = "SELECT * FROM usuarios WHERE login= '{$usuario}';";
        $resultado = mysqli_query($db, $query);

        if ($resultado->num_rows) {
            // Revisar si el password es correcto 
            $usuario = mysqli_fetch_assoc($resultado);

            // Verificar si password(hasheado) es correcto 
            // hash el password al insertar
            //$auth = password_verify($password, $usuario['password']);
            $auth = ($password == $usuario['password']) ? true : false;

            if ($auth) {
                // El usuario está autenticado, se inicia sesion 
                session_start();

                // Datos de la sesion 
                $_SESSION['usuario'] = $usuario['login'];
                $_SESSION['usuarioID'] = $usuario['id'];
                $_SESSION['login'] = true;

                header('Location: /todolist');
            } else {
                $errores[] = "Contraseña incorrecta.";
            }
        } else {
            setcookie('errores', "El usuario no existe.");
            header('Location: /todolist');
        }
    } else {
        header('Location: /todolist');
    }
}
