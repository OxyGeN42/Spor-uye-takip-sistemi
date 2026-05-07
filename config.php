<?php
// ============================================================
//  FIGHT ACADEMY — Yapılandırma
//  Bu dosyadaki bilgileri kendi hosting bilgilerinle değiştir!
// ============================================================

define('DB_HOST', 'localhost');      // Genellikle localhost kalır
define('DB_NAME', 'fight_academy'); // cPanel'de oluşturacağın DB adı
define('DB_USER', 'root');          // cPanel DB kullanıcı adı
define('DB_PASS', '');              // cPanel DB şifresi

// Admin giriş bilgileri (değiştir!)
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', '$2y$12$tx.VoIP76H5sYfZAG.3C5O6N0sePOCDueTpU6XnSMx5xF4kNeegWG'); // şifre: "password"
// Yeni şifre hash'i için: https://bcrypt-generator.com/ (cost: 12)

define('SESSION_NAME', 'fa_session');
define('APP_NAME', 'Özgür Şaşmaz Fight Academy');

session_name(SESSION_NAME);
session_start();

// Veritabanı bağlantısı
function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]
            );
        } catch (PDOException $e) {
            die(json_encode(['error' => 'Veritabanı bağlantısı başarısız: ' . $e->getMessage()]));
        }
    }
    return $pdo;
}

// Giriş kontrolü
function requireLogin(): void {
    if (empty($_SESSION['logged_in'])) {
        header('Location: login.php');
        exit;
    }
}

// JSON response helper
function jsonResponse(array $data, int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

// XSS temizleme
function e(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}
