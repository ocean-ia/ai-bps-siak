<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Data Assistant BPS Kabupaten Siak</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .sticky-form {
            position: fixed;
            bottom: 32px;
            left: 50%;
            transform: translateX(-50%);
            background: white;
            padding: 1rem;
            width: 100%;
            max-width: 896px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .main-content {
            padding-bottom: 40vh;
        }
        .message {
            margin-bottom: 1rem;
            padding: 1rem;
            border-radius: 0.5rem;
        }
        .user-message {
            background-color: #f3f4f6;
            margin-left: auto;
            margin-right: 2rem;
            max-width: 80%;
        }
        .ai-message {
            background-color: #e5e7eb;
            margin-right: auto;
            margin-left: 2rem;
            max-width: 80%;
        }
    </style>
</head>
<body class="bg-white min-h-screen flex flex-col">
    <div class="container mx-auto max-w-4xl px-4 py-8 flex-1 flex flex-col main-content">
        <div class="text-center mb-8">
            <div class="flex items-center justify-center mb-4">
                <img 
                    src="images/bps-siak-logo.png" 
                    alt="BPS Kabupaten Siak Logo" 
                    class="h-10 w-auto object-contain"
                />
            </div>
            <h1 class="text-3xl font-bold text-blue-500 mb-2">
                AI Data Assistant
            </h1>
        </div>

        <div class="bg-white rounded-lg p-4 flex-1 flex flex-col">
            <div id="chat-messages" class="flex-1 flex flex-col space-y-4">
                <?php
                session_start();
                if (!isset($_SESSION['messages'])) {
                    $_SESSION['messages'] = [];
                    echo '<div class="text-center text-gray-500 text-xl py-20">
                        Mulai mengajukan pertanyaan tentang data BPS Kabupaten Siak
                    </div>';
                } else {
                    foreach ($_SESSION['messages'] as $message) {
                        $class = $message['type'] === 'user' ? 'user-message' : 'ai-message';
                        echo "<div class='message {$class}'>{$message['content']}</div>";
                    }
                }
                ?>
            </div>
            
            <form method="POST" action="api/chat.php" class="sticky-form">
                <div class="flex gap-2 w-full">
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
                </div>
            </form>
        </div>
    </div>

    <div class="fixed bottom-0 left-0 w-full text-center py-2 text-sm text-gray-500 bg-white">
        AI Data Assistant dapat membuat kesalahan. Mohon periksa kembali informasi penting.
    </div>
</body>
</html>