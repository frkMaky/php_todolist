<?php
require 'includes/funciones.php';

$tareas = [];
$mensaje = '';


function cargarTareas()
{
    $conn = new mysqli();

    try {
        $conn->connect("localhost", "root", "root", "todolist");

        $query = "SELECT * FROM tarea WHERE id_usuario = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $_SESSION['usuarioID']);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $tareas = $resultado->fetch_all();
    } catch (Exception $e) {
        var_dump(`Ha ocurrido un error: {$e}`);
    } finally {
        $conn->close();
        return $tareas;
    }
}

function completarTarea($id)
{
    $conn = new mysqli();
    $msg  = '';

    try {
        $conn->connect("localhost", "root", "root", "todolist");

        $fechaDate = new DateTime();
        $fechaFinTarea = $fechaDate->format('Y-m-d H:i:s');

        $query = "UPDATE tarea SET fechaFin=? WHERE id_tarea = ? AND id_usuario = ? ;";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sss", $fechaFinTarea, $id, $_SESSION['usuarioID']);
        $resultado = $stmt->execute();
        if ($resultado) {
            $msg = "Se ha actualizado el registro";
        }
    } catch (Exception $e) {
        $msg = `Ha ocurrido un error: {$e}`;
    } finally {
        $conn->close();
        return $msg;
    }
}
function eliminarTarea($id)
{
    $conn = new mysqli();
    $msg  = '';

    try {
        $conn->connect("localhost", "root", "root", "todolist");

        $query = "DELETE FROM tarea WHERE id_tarea = ?;";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $id);
        $resultado = $stmt->execute();
        if ($resultado) {
            $msg = "Se ha eliminado el registro";
        }
    } catch (Exception $e) {
        $msg = `Ha ocurrido un error: {$e}`;
    } finally {
        $conn->close();
        return $msg;
    }
}


?>

<?php
require_once('./includes/templates/header.php');


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['Eliminar'])) {
        $id = $_POST['Eliminar'];
        $mensaje = eliminarTarea($id);
    } elseif (isset($_POST['Completar'])) {
        $id = $_POST['Completar'];
        $mensaje = completarTarea($id);
    }
}

if ($auth) {
    $tareas = cargarTareas();
} else {
    $tareas = "LOGIN para acceder a tus tareas";
}

?>

<main>
    <h2>Tu lista de tareas</h2>


    <?php
    if ($auth) {
    ?>

        <form action="./includes/new_task.php">
            <button name="NuevaTarea" class="boton_tarea verde">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                    <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4" />
                </svg>
            </button>
        </form>

        <form name="form_tareas" method="post">

            <!-- Nueva Tarea -->
            <table class="lista_tareas">
                <th class="lista_tareas">ID</th>
                <th class="lista_tareas">NOMBRE</th>
                <th class="lista_tareas">DESCRIPCION</th>
                <th class="lista_tareas">FECHA INICIO</th>
                <th class="lista_tareas">FECHA FIN</th>
                <th class="lista_tareas">OPCIONES</th>

                <?php
                foreach ($tareas as $tarea) {
                ?>
                    <tr class="lista_tareas">

                        <td class="lista_tareas"><?php echo $tarea[0]; ?></td>
                        <td class="lista_tareas"><?php echo $tarea[1]; ?></td>
                        <td class="lista_tareas"><?php echo $tarea[2]; ?></td>
                        <td class="lista_tareas"><?php echo $tarea[4]; ?></td>
                        <td class="lista_tareas"><?php echo $tarea[5]; ?></td>
                        <td class="lista_tareas">
                            <!-- Modificar Tarea -->
                            <button name="Completar" class="boton_tarea azul" value="<?php echo $tarea[0]; ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2" viewBox="0 0 16 16">
                                    <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0" />
                                </svg>
                            </button>
                            <!-- Eliminar Tarea -->
                            <button name="Eliminar" class="boton_tarea rojo" value="<?php echo $tarea[0]; ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z" />
                                    <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z" />
                                </svg>
                            </button>
                        </td>
                    </tr>

                <?php
                } // foreach
                ?>
            </table>

        </form>

    <?php
    } else {
    ?>
        <h3>LOGIN para acceder a tus tareas</h2>
        <?php
    } // else 
        ?>


</main>


<?php
require_once('./includes/templates/footer.php');
?>