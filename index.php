<?php
require_once 'api/utils/SessionManager.php';
SessionManager::clearSession(); // Always start fresh
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
            padding: 1rem;
            gap: 1rem;
            display: flex;
            flex-direction: column;
            scroll-behavior: smooth;
        }
        .message {
            padding: 1rem;
            border-radius: 0.5rem;
            max-width: 80%;
            word-break: break-word;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
            animation: fadeIn 0.3s ease-in-out;
            transition: background-color 0.3s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .message.highlight {
            background-color: #f0f9ff;
        }
        .user-message {
            background-color: #e3f2fd;
            margin-left: auto;
            border: 1px solid #90caf9;
            position: relative;
        }
        .ai-message {
            background-color: #f5f5f5;
            margin-right: auto;
            border: 1px solid #e0e0e0;
            position: relative;
        }
        .input-container {
            background: white;
            border-top: 1px solid #e5e7eb;
            padding: 1rem;
            position: sticky;
            bottom: 0;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
        }
        .sticky-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: white;
            z-index: 50;
            border-bottom: 1px solid #e5e7eb;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .content-wrapper {
            padding-top: 100px;
            background: linear-gradient(to bottom, #f8fafc, #f1f5f9);
        }
        .logo-container {
            background: white;
            padding: 0.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: #6b7280;
            text-align: center;
            padding: 2rem;
        }
        .typing-indicator {
            display: none;
            padding: 1rem;
            color: #6b7280;
            font-style: italic;
        }
        .typing-indicator.active {
            display: block;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <header class="sticky-header">
        <div class="container mx-auto max-w-4xl px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="logo-container">
                        <img 
                            src="images/bps-siak-logo.png" 
                            alt="BPS Kabupaten Siak Logo" 
                            class="h-12 w-auto object-contain"
                        />
                    </div>
                    <h1 class="text-2xl font-bold text-blue-600">
                        AI Data Assistant BPS Kabupaten Siak
                    </h1>
                </div>
            </div>
        </div>
    </header>

    <div class="container mx-auto max-w-4xl px-4 flex-1 flex flex-col content-wrapper">
        <div class="bg-white rounded-lg shadow-lg flex-1 flex flex-col chat-container">
            <div class="messages-container" id="chat-messages">
                <?php
                $messages = SessionManager::getMessages();
                if (empty($messages)) {
                    echo '<div class="empty-state">
                        <p class="text-xl mb-2">Mulai mengajukan pertanyaan tentang data BPS Kabupaten Siak</p>
                        <p class="text-sm">Ketik pertanyaan Anda di bawah ini</p>
                    </div>';
                } else {
                    foreach ($messages as $message) {
                        $class = $message['type'] === 'user' ? 'user-message' : 'ai-message';
                        echo "<div class='message {$class}'>{$message['content']}</div>";
                    }
                }
                ?>
            </div>
            
            <div class="typing-indicator" id="typing-indicator">
                AI sedang mengetik...
            </div>
            
            <div class="input-container">
                <form id="chat-form" class="flex gap-2">
                    <input 
                        type="text" 
                        name="prompt" 
                        id="prompt-input"
                        placeholder="Contoh: Apa itu Badan Pusat Statistik?" 
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                        required
                    >
                    <button 
                        type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
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

    <script>
        const messagesContainer = document.getElementById('chat-messages');
        const chatForm = document.getElementById('chat-form');
        const promptInput = document.getElementById('prompt-input');
        const typingIndicator = document.getElementById('typing-indicator');

        function scrollToBottom(smooth = true) {
            if (messagesContainer) {
                messagesContainer.scrollTo({
                    top: messagesContainer.scrollHeight,
                    behavior: smooth ? 'smooth' : 'auto'
                });
            }
        }

        function appendMessage(content, type) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${type}-message`;
            messageDiv.innerHTML = content;
            messagesContainer.appendChild(messageDiv);
            
            // Add highlight effect
            setTimeout(() => {
                messageDiv.classList.add('highlight');
                scrollToBottom();
                // Remove highlight after animation
                setTimeout(() => {
                    messageDiv.classList.remove('highlight');
                }, 1000);
            }, 100);
        }

        chatForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const prompt = promptInput.value.trim();
            if (!prompt) return;

            appendMessage(prompt, 'user');
            promptInput.value = '';
            promptInput.disabled = true;
            typingIndicator.classList.add('active');

            try {
                const formData = new FormData();
                formData.append('prompt', prompt);

                const response = await fetch('api/chat.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();
                typingIndicator.classList.remove('active');
                appendMessage(data.response, 'ai');
            } catch (error) {
                typingIndicator.classList.remove('active');
                appendMessage('Maaf, terjadi kesalahan dalam memproses permintaan Anda.', 'ai');
                console.error('Error:', error);
            }

            promptInput.disabled = false;
            promptInput.focus();
        });

        // Initial scroll to bottom
        scrollToBottom(false);
    </script>
</body>
</html>