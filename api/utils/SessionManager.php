<?php
class SessionManager {
    public static function init() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['messages'])) {
            $_SESSION['messages'] = [];
            // Add welcome message for new sessions
            self::addMessage('ai', 'Halo Sahabat Data!<br><br>Selamat datang di layanan Statistik BPS Kabupaten Siak.<br><br>Sahabat Data dapat bertanya seputar data statistik di BPS Kabupaten Siak.');
        }
    }

    public static function addMessage($type, $content) {
        $_SESSION['messages'][] = [
            'type' => $type,
            'content' => $content
        ];
    }

    public static function getMessages() {
        return $_SESSION['messages'] ?? [];
    }

    public static function clearSession() {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        session_start();
        $_SESSION['messages'] = [];
    }
}
?>