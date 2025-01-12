<?php
require_once 'api/utils/SessionManager.php';
SessionManager::init();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Data Assistant BPS Kabupaten Siak</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .chat-container {
            height: calc(100vh - 200px);
            display: flex;
            flex-direction: column;
        }
        .messages-container {
            flex-grow: 1;
            overflow-y: auto;
            display: flex;
            flex-direction: column-reverse;
            padding: 1rem;
            gap: 1rem;
        }
        .message {
            padding: 1rem;
            border-radius: 0.5rem;
            max-width: 80%;
            word-break: break-word;
        }
        .user-message {
            background-color: #f3f4f6;
            margin-left: auto;
        }
        .ai-message {
            background-color: #e5e7eb;
            margin-right: auto;
        }
        .input-container {
            background: white;
            border-top: 1px solid #e5e7eb;
            padding: 1rem;
            position: sticky;
            bottom: 0;
        }
        .sticky-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: white;
            z-index: 50;
            border-bottom: 1px solid #e5e7eb;
        }
        .content-wrapper {
            padding-top: 120px;
        }
    </style>
</head>
<body class="bg-white min-h-screen flex flex-col">
    <header class="sticky-header">
        <div class="container mx-auto max-w-4xl px-4 py-4">
            <div class="flex items-center justify-center gap-4">
                <img 
                    src="images/bps-siak-logo.png" 
                    alt="BPS Kabupaten Siak Logo" 
                    class="h-10 w-auto object-contain"
                />
                <h1 class="text-2xl font-bold text-blue-500">
                    AI Data Assistant
                </h1>
            </div>
        </div>
    </header>

    <div class="container mx-auto max-w-4xl px-4 flex-1 flex flex-col content-wrapper">
        <div class="bg-white rounded-lg shadow-lg flex-1 flex flex-col chat-container">
            <div class="messages-container" id="chat-messages">
                <?php
                $messages = SessionManager::getMessages();
                if (empty($messages)) {
                    echo '<div class="text-center text-gray-500 text-xl py-20">
                        Mulai mengajukan pertanyaan tentang data BPS Kabupaten Siak
                    </div>';
                } else {
                    foreach ($messages as $message) {
                        $class = $message['type'] === 'user' ? 'user-message' : 'ai-message';
                        echo "<div class='message {$class}'>{$message['content']}</div>";
                    }
                }
                ?>
            </div>
            
            <div class="input-container">
                <form method="POST" action="api/chat.php" class="flex gap-2">
                    <input 
                        type="text" 
                        name="prompt" 
                        placeholder="Contoh: Apa itu Badan Pusat Statistik?" 
                        class="flex-1 px-4 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500"
                        required
                    >
                    <button 
                        type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors"
                    >
                        Kirim
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="text-center py-2 text-sm text-gray-500 bg-white border-t">
        AI Data Assistant dapat membuat kesalahan. Mohon periksa kembali informasi penting.
    </div>
</body>
</html>