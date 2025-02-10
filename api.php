<?php

// Устанавливаем заголовки CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");

// Путь к файлу базы данных SQLite
$dbFile = 'users.db';

// Создаём базу данных и таблицу, если они не существуют
if (!file_exists($dbFile)) {
    $db = new SQLite3($dbFile);
    $db->exec("CREATE TABLE users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT NOT NULL UNIQUE
    )");
    $db->close();
}

// Функция для подключения к базе данных
function getDbConnection() {
    global $dbFile;
    $db = new SQLite3($dbFile);
    return $db;
}

// Получить всех пользователей
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) { // Запрос на получение конкретного пользователя
        $id = intval($_GET['id']);
        $db = getDbConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        $user = $result->fetchArray(SQLITE3_ASSOC);

        if ($user) {
            echo json_encode($user);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'User not found.']);
        }
        $db->close();
    } else { // Запрос на получение всех пользователей
        $db = getDbConnection();
        $result = $db->query("SELECT * FROM users");
        $users = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $users[] = $row;
        }
        echo json_encode($users);
        $db->close();
    }
}

// Добавить нового пользователя
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $name = $data['name'] ?? null;
    $email = $data['email'] ?? null;

    if ($name && $email) {
        $db = getDbConnection();
        $stmt = $db->prepare("INSERT INTO users (name, email) VALUES (:name, :email)");
        $stmt->bindValue(':name', $name, SQLITE3_TEXT);
        $stmt->bindValue(':email', $email, SQLITE3_TEXT);
        $stmt->execute();
        http_response_code(201);
        echo json_encode(['message' => 'User created successfully.']);
        $db->close();
    } else {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid input.']);
    }
}

// Обновить пользователя
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    parse_str(file_get_contents("php://input"), $data);
    $id = intval($data['id']);
    $name = $data['name'] ?? null;
    $email = $data['email'] ?? null;

    if ($name && $email) {
        $db = getDbConnection();
        $stmt = $db->prepare("UPDATE users SET name = :name, email = :email WHERE id = :id");
        $stmt->bindValue(':name', $name, SQLITE3_TEXT);
        $stmt->bindValue(':email', $email, SQLITE3_TEXT);
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $stmt->execute();
        echo json_encode(['message' => 'User updated successfully.']);
        $db->close();
    } else {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid input.']);
    }
}

// Удалить пользователя
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    parse_str(file_get_contents("php://input"), $data);
    $id = intval($data['id']);

    $db = getDbConnection();
    $stmt = $db->prepare("DELETE FROM users WHERE id = :id");
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    $stmt->execute();
    echo json_encode(['message' => 'User deleted successfully.']);
    $db->close();
}
