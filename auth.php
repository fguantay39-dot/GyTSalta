<?php
declare(strict_types=1);

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'httponly' => true,
    'samesite' => 'Strict',
]);
session_start();

header('Content-Type: application/json; charset=utf-8');

function respond(int $status, array $data): void
{
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function readJsonBody(): array
{
    $body = json_decode(file_get_contents('php://input'), true);
    return is_array($body) ? $body : [];
}

$action = $_GET['action'] ?? '';
if (!is_string($action)) {
    respond(400, ['message' => 'Acción inválida.']);
}
$action = trim($action);

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'me') {
    respond(200, ['user' => $_SESSION['user'] ?? null]);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(405, ['message' => 'Método no permitido.']);
}

$input = readJsonBody();

try {
    require __DIR__ . '/db.php';

    if ($action === 'register') {
        if (!isset($input['name']) || !is_string($input['name']) || trim($input['name']) === '') {
            respond(422, ['message' => 'El nombre y apellido es obligatorio.']);
        }
        if (!isset($input['email']) || !is_string($input['email']) || trim($input['email']) === '') {
            respond(422, ['message' => 'El correo electrónico es obligatorio.']);
        }
        if (!isset($input['password']) || !is_string($input['password']) || trim($input['password']) === '') {
            respond(422, ['message' => 'La contraseña es obligatoria.']);
        }

        $name = trim($input['name']);
        $email = strtolower(trim($input['email']));
        $password = $input['password'];

        if (strlen($name) > 120) {
            respond(422, ['message' => 'Ingresá un nombre de hasta 120 caracteres.']);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 254) {
            respond(422, ['message' => 'Ingresá un correo electrónico válido.']);
        }

        if (strlen($password) < 8) {
            respond(422, ['message' => 'La contraseña debe tener al menos 8 caracteres.']);
        }

        $statement = $pdo->prepare(
            'INSERT INTO users (name, email, password_hash) VALUES (:name, :email, :password_hash)'
        );

        try {
            $statement->execute([
                'name' => $name,
                'email' => $email,
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            ]);
        } catch (PDOException $error) {
            if ($error->getCode() === '23000') {
                respond(409, ['message' => 'Ya existe una cuenta con ese correo.']);
            }

            throw $error;
        }

        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => (int)$pdo->lastInsertId(),
            'name' => $name,
            'email' => $email,
        ];

        respond(201, ['user' => $_SESSION['user']]);
    }

    if ($action === 'login') {
        if (!isset($input['email']) || !is_string($input['email']) || trim($input['email']) === '') {
            respond(422, ['message' => 'El correo electrónico es obligatorio.']);
        }
        if (!isset($input['password']) || !is_string($input['password']) || trim($input['password']) === '') {
            respond(422, ['message' => 'La contraseña es obligatoria.']);
        }

        $email = strtolower(trim($input['email']));
        $password = $input['password'];

        if (
            strlen($email) > 254
            || filter_var($email, FILTER_VALIDATE_EMAIL) === false
        ) {
            respond(422, ['message' => 'Ingresá un correo electrónico válido.']);
        }

        $statement = $pdo->prepare(
            'SELECT id, name, email, password_hash FROM users WHERE email = :email LIMIT 1'
        );
        $statement->execute(['email' => $email]);
        $user = $statement->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            respond(401, ['message' => 'Correo o contraseña incorrectos.']);
        }

        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => (int)$user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
        ];

        respond(200, ['user' => $_SESSION['user']]);
    }

    if ($action === 'logout') {
        $_SESSION = [];
        session_destroy();
        respond(200, ['message' => 'Sesión cerrada.']);
    }

    respond(404, ['message' => 'Acción desconocida.']);
} catch (Throwable $error) {
    error_log($error->getMessage());
    respond(500, ['message' => 'Error del servidor. Revisá la conexión con MySQL en db.php.']);
}