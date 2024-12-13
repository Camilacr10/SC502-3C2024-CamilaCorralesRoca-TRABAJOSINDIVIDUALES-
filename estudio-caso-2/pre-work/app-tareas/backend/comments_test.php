<?php
require 'comments.php';

$idComentario = crearComentario(1, 1, 'Este es un comentario de prueba');
if($idComentario){
    echo 'Comentario creado exitosamente ' . $idComentario . PHP_EOL;
}else{
    echo 'No se creó el comentario' . PHP_EOL;
}

$editado = editarComentario($idComentario, 'Este comentario ha sido editado');
if ($editado) {
    echo "Comentario editado exitosamente.\n";
} else {
    echo "Error al editar el comentario.\n";
}

echo "Lista de comentarios de la tarea 1:" . PHP_EOL;
$comentarios = obtenerComentariosPorTarea(1);
if ($comentarios) {
    foreach ($comentarios as $comentario) {
        echo "ID: " . $comentario["id"] . " Comentario: " . $comentario["comment"] . PHP_EOL;
    }
}

echo "Eliminando el comentario: " . $idComentario . PHP_EOL;
$eliminado = eliminarComentario($idComentario);
if ($eliminado) {
    echo "El comentario se eliminó exitosamente" . PHP_EOL;
} else {
    echo "Error al eliminar el comentario" . PHP_EOL;
}
?>
C:/Users/Alejandro Sandi/Desktop/instantclient_19_8/instantclient_23_4