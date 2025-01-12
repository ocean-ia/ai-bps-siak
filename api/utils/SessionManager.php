<?php
class SessionManager {
    public static function init() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['messages'])) {
            $_SESSION['messages'] = [];
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
}
?>