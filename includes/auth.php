<?php
/**
 * includes/auth.php
 * Autenticación y manejo de sesión usando la conexión mysqli desde php/conexion.php.
 *
 * Ajustes:
 * - Busca y requirea php/conexion.php (intenta varias rutas relativas comunes).
 * - Usa la función conexion() del archivo para obtener $mysqli.
 * - Provee funciones: is_logged_in(), current_user(), login(), require_login(), logout().
 * - Usa password_verify() si la DB almacena hashes; si no, hace fallback plaintext y re-hashea al primer login.
 *
 * Coloca este archivo en includes/auth.php y ajusta las rutas si tu estructura de carpetas difiere.
 */

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/**
 * Intentar incluir php/conexion.php probando rutas relativas comunes.
 * Si tu includes/auth.php está en otra ubicación, ajusta la ruta manualmente.
 */
$included = false;
$possible_paths = [
    __DIR__ . '/../php/conexion.php',   // includes/ .. /php/conexion.php
    __DIR__ . '/php/conexion.php',      // includes/php/conexion.php
    __DIR__ . '/../../php/conexion.php' // includes/../../php/conexion.php
];

foreach ($possible_paths as $p) {
    if (file_exists($p)) {
        require_once $p;
        $included = true;
        break;
    }
}

// Obtener la conexión mysqli en $mysqli si es posible
$mysqli = null;
if (isset($GLOBALS['mysqli']) && $GLOBALS['mysqli'] instanceof mysqli) {
    $mysqli = $GLOBALS['mysqli'];
} elseif (isset($conexion) && $conexion instanceof mysqli) {
    // si conexion.php define $conexion
    $mysqli = $conexion;
} elseif (function_exists('conexion')) {
    // si conexion.php define function conexion()
    $tmp = conexion();
    if ($tmp instanceof mysqli) $mysqli = $tmp;
}

// Helper: verificar que tenemos mysqli
if (!($mysqli instanceof mysqli)) {
    error_log("includes/auth.php: no se pudo obtener una conexión mysqli. Revisa la ruta a php/conexion.php.");
    // No abortar inmediatamente — las páginas pueden verificar la conexión antes de usar login()
}

/**
 * is_logged_in - devuelve true si hay usuario en sesión
 */
function is_logged_in() {
    return !empty($_SESSION['user_id']);
}

/**
 * current_user - devuelve arreglo con info del usuario o null
 */
function current_user() {
    if (!is_logged_in()) return null;
    return [
        'id'       => $_SESSION['user_id'] ?? null,
        'username' => $_SESSION['username'] ?? null,
        'rol'      => $_SESSION['rol'] ?? null,
        'afiliado' => $_SESSION['afiliado'] ?? null,
    ];
}

/**
 * login - autentica usuario usando la tabla `participantes` (ajusta si tu tabla es distinta).
 *
 * Retorna true si login exitoso, false en caso contrario.
 */
function login($username, $password) {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    global $mysqli;

    if (!$username || !$password) return false;

    try {
        if (!($mysqli instanceof mysqli)) {
            error_log("includes/auth.php: login() - no hay conexión mysqli disponible.");
            return false;
        }

        $sql = "SELECT id, nombre, apellido, password, nivel, afiliado, login_name
                FROM participantes
                WHERE login_name = ?
                LIMIT 1";
        if (!$stmt = $mysqli->prepare($sql)) {
            error_log("includes/auth.php: fallo prepare SELECT: " . $mysqli->error);
            return false;
        }
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $res = $stmt->get_result();
        $user = $res->fetch_assoc();
        $stmt->close();

        if (!$user) return false;

        $stored = $user['password'] ?? '';

        // 1) Intento seguro: password_verify (hash en DB)
        if ($stored && password_verify($password, $stored)) {
            // rehash si es necesario
            if (password_needs_rehash($stored, PASSWORD_DEFAULT)) {
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                if ($u = $mysqli->prepare("UPDATE participantes SET password = ? WHERE id = ?")) {
                    $u->bind_param('si', $newHash, $user['id']);
                    $u->execute();
                    $u->close();
                }
            }

            session_regenerate_id(true);
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = trim(($user['nombre'] ?? '') . ' ' . ($user['apellido'] ?? ''));
            $_SESSION['rol']      = $user['nivel'];
            $_SESSION['afiliado'] = $user['afiliado'] ?? '';
            return true;
        }

        // 2) Fallback temporal: comparación en texto plano (migrar luego)
        if ($stored === $password) {
            // rehasear y actualizar
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            if ($u = $mysqli->prepare("UPDATE participantes SET password = ? WHERE id = ?")) {
                $u->bind_param('si', $newHash, $user['id']);
                $u->execute();
                $u->close();
            }

            session_regenerate_id(true);
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = trim(($user['nombre'] ?? '') . ' ' . ($user['apellido'] ?? ''));
            $_SESSION['rol']      = $user['nivel'];
            $_SESSION['afiliado'] = $user['afiliado'] ?? '';
            return true;
        }

        return false;
    } catch (Throwable $e) {
        error_log("includes/auth.php - login() error: " . $e->getMessage());
        return false;
    }
}

/**
 * require_login - redirige a la página de login si no hay sesión.
 * Ajusta la ruta del login si la tuya es distinta.
 */
function require_login() {
    if (!is_logged_in()) {
        header("Location: https://www.cleanworkorangemx.com/cwo/login.php");
        exit;
    }
}

/**
 * logout - limpia la sesión y cookie
 */
function logout() {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}
?>