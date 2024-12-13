<?php

require 'db.php';

function crearComentario($task_id, $user_id, $comment)
{
    global $pdo;
    try {
        $sql = "INSERT INTO comments (task_id, user_id, comment) values (:task_id, :user_id, :comment)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'task_id' => $task_id,
            'user_id' => $user_id,
            'comment' => $comment
        ]);
        return $pdo->lastInsertId();
    } catch (Exception $e) {
        logError("Error creando comentario: " . $e->getMessage());
        return 0;
    }
}

function editarComentario($id, $comment)
{
    global $pdo;
    try {
        $sql = "UPDATE comments set comment = :comment where id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'comment' => $comment,
            'id' => $id
        ]);
        $affectedRows = $stmt->rowCount();
        return $affectedRows > 0;
    } catch (Exception $e) {
        logError($e->getMessage());
        return false;
    }
}

function obtenerComentariosPorTarea($task_id)
{
    global $pdo;
    try {
        $sql = "SELECT * FROM comments WHERE task_id = :task_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['task_id' => $task_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        logError("Error al obtener comentarios: " . $e->getMessage());
        return [];
    }
}

function eliminarComentario($id)
{
    global $pdo;
    try {
        $sql = "DELETE FROM comments WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount() > 0;
    } catch (Exception $e) {
        logError("Error al eliminar el comentario: " . $e->getMessage());
        return false;
    }
}

$method = $_SERVER['REQUEST_METHOD'];
header('Content-Type: application/json');
session_start();
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    switch ($method) {
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $comment_id = crearComentario($data['task_id'], $user_id, $data['comment']);
            echo json_encode(['comment_id' => $comment_id]);
            break;
        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            $updated = editarComentario($data['id'], $data['comment']);
            echo json_encode(['success' => $updated]);
            break;
        case 'GET':
            $task_id = $_GET['task_id'];
            $comments = obtenerComentariosPorTarea($task_id);
            echo json_encode($comments);
            break;
        case 'DELETE':
            $id = $_GET['id'];
            $deleted = eliminarComentario($id);
            echo json_encode(['success' => $deleted]);
            break;
        default:
            http_response_code(405);
            echo json_encode(["error" => "Método no permitido"]);
            break;
    }
} else {
    http_response_code(401);
    echo json_encode(["error" => "Sesión no activa"]);
}
?>
