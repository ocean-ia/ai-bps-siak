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
    <title>DARA AI BPS Siak</title>
    <link rel="icon" type="image/x-icon" href="images/favicon_bps.png">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            height: 100vh;
            display: flex;
            flex-direction: column;
            margin: 0;
            padding: 0;
            width: 100%;
        }

        .chat-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            position: relative;
            width: 100%;
            max-width: 100%;
        }

        .messages-container {
            flex: 1;
            overflow-y: auto;
            padding: 1rem;
            gap: 1rem;
            display: flex;
            flex-direction: column;
            scroll-behavior: smooth;
            position: relative;
            padding-bottom: 80px;
            scrollbar-width: thin;
            scrollbar-color: rgba(156, 163, 175, 0.5) transparent;
            min-height: 0;
            max-height: calc(100vh - 160px);
            width: 100%;
        }

        .messages-container::-webkit-scrollbar {
            width: 6px;
        }

        .messages-container::-webkit-scrollbar-track {
            background: transparent;
        }

        .messages-container::-webkit-scrollbar-thumb {
            background-color: rgba(156, 163, 175, 0.5);
            border-radius: 3px;
        }

        .message {
            padding: 1rem;
            border-radius: 0.5rem;
            max-width: 80%;
            word-break: break-word;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.3s ease-in-out;
            transition: all 0.3s ease;
            opacity: 0;
            transform: translateY(20px);
            position: relative;
            margin: 1rem 0;
        }

        .message.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .message-content {
            width: 100%;
        }

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            position: absolute;
            object-fit: cover;
            border: 2px solid #fff;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            background-color: #f5f5f5;
        }

        .user-message {
            background-color: #e3f2fd;
            margin-left: auto;
            border: 1px solid #90caf9;
            position: relative;
            margin-right: 1rem;
        }

        .user-message .avatar {
            right: -50px;
            top: 0;
        }

        .ai-message {
            background-color: #f5f5f5;
            margin-right: auto;
            border: 1px solid #e0e0e0;
            position: relative;
            margin-left: 50px;
        }

        .ai-message .avatar {
            left: -50px;
            top: 0;
        }

        .typing-indicator {
            display: none;
            padding: 1rem;
            color: #6b7280;
            font-style: italic;
            position: absolute;
            bottom: 80px;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.9);
            border-top: 1px solid #e5e7eb;
            z-index: 10;
            backdrop-filter: blur(4px);
        }

        .typing-indicator.active {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .typing-dots {
            display: flex;
            gap: 0.25rem;
        }

        .typing-dot {
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background-color: #6b7280;
            animation: typingDot 1.4s infinite ease-in-out;
        }

        .typing-dot:nth-child(2) {
            animation-delay: 0.2s;
        }

        .typing-dot:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes typingDot {

            0%,
            60%,
            100% {
                transform: translateY(0);
            }

            30% {
                transform: translateY(-4px);
            }
        }

        .sticky-header {
            position: sticky;
            top: 0;
            background: white;
            z-index: 50;
            border-bottom: 1px solid #e5e7eb;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 0.75rem 0;
            width: 100%;
        }

        .header-title {
            font-size: 1.125rem;
            color: #2563eb;
            font-weight: 600;
        }

        .logo-container {
            display: flex;
            align-items: center;
        }

        .logo-container img {
            height: 2.5rem;
            width: auto;
            object-fit: contain;
        }

        .content-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: linear-gradient(to bottom, #f8fafc, #f1f5f9);
            overflow: hidden;
            height: 100%;
            width: 100%;
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

        .input-container {
            padding: 1rem;
            background: white;
            border-top: 1px solid #e5e7eb;
            position: sticky;
            bottom: 0;
            width: 100%;
        }

        .container {
            width: 100%;
            max-width: 100%;
            padding-left: 1rem;
            padding-right: 1rem;
        }
    </style>
</head>

<body>
    <header class="sticky-header">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between">
                <div class="logo-container">
                    <img
                        src="images/bps-siak-logo.png"
                        alt="BPS Kabupaten Siak Logo"
                        class="h-10" />
                </div>
                <h1 class="header-title">
                    <span class="block sm:hidden">DARA AI</span>
                    <span class="hidden sm:block">Data Assistant and Response AI</span>
                </h1>
            </div>
        </div>
    </header>

    <div class="content-wrapper">
        <div class="container mx-auto px-4 flex-1 flex flex-col">
            <div class="bg-white shadow-lg flex-1 flex flex-col chat-container">
                <div class="messages-container" id="chat-messages">
                    <?php
                    $messages = SessionManager::getMessages();
                    if (empty($messages)) {
                        $alternatif = [
                            "Mari jelajahi data Siak bersama.",
                            "Siap bantu temukan informasi statistik yang kamu butuhkan.",
                            "Langsung aja mulai eksplorasi data BPS Siak.",
                            "Statistik resmi, akses cepat. Mau mulai dari mana?",
                            "Data BPS Siak, satu pertanyaan bisa buka banyak wawasan.",
                            "Data Siak, siap dijelajahi. Kamu mulai dari mana?",
                            "Statistik Siak tersedia, langsung aja temukan jawabannya.",
                            "Dari angka ke wawasan—siap bantu jawab pertanyaanmu.",
                            "Data bukan sekadar angka, ayo gali maknanya bersama.",
                            "Satu pertanyaan bisa buka banyak cerita statistik Siak.",
                            "Siap jawab pertanyaanmu tentang data Siak, langsung mulai aja.",
                            "Data BPS Siak tersedia, tinggal pilih topikmu.",
                            "Akses cepat ke statistik Siak, apa yang mau kamu ketahui?",
                            "Mulai percakapanmu dengan data, kami siap membantu.",
                            "Statistik akurat, respons instan—silakan ajukan pertanyaan."
                        ];
                        $pilihan = $alternatif[array_rand($alternatif)];
                        echo '<div class="empty-state">';
                        echo '<p class="text-xl mb-2">' . $pilihan . '</p>';
                        echo '</div>';
                    } else {
                        foreach ($messages as $message) {
                            $class = $message['type'] === 'user' ? 'user-message' : 'ai-message';
                            $avatar = $message['type'] === 'ai' ? '<img src="images/default-avatar.png" alt="Dara AI" class="avatar">' : '';
                            echo "<div class='message {$class} visible'>
                                    {$avatar}
                                    <div class='message-content'>{$message['content']}</div>
                                  </div>";
                        }
                    }
                    ?>
                </div>

                <div class="typing-indicator" id="typing-indicator">
                    DARA sedang mengetik...
                    <div class="typing-dots">
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                    </div>
                </div>

                <div class="input-container">
                    <form id="chat-form" class="flex gap-2">
                        <input
                            type="text"
                            name="prompt"
                            id="prompt-input"
                            placeholder="Contoh: Apa itu Badan Pusat Statistik?"
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                            required>
                        <button
                            type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Kirim
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center py-2 text-sm text-gray-500 bg-white border-t w-full">
        DARA AI dapat membuat kesalahan. Mohon periksa kembali informasi penting.
    </div>

    <script>
        const messagesContainer = document.getElementById('chat-messages');
        const chatForm = document.getElementById('chat-form');
        const promptInput = document.getElementById('prompt-input');
        const typingIndicator = document.getElementById('typing-indicator');

        function scrollToBottom(smooth = true) {
            if (messagesContainer) {
                const lastMessage = messagesContainer.lastElementChild;
                if (lastMessage) {
                    lastMessage.scrollIntoView({
                        behavior: smooth ? 'smooth' : 'auto',
                        block: 'end'
                    });
                }
            }
        }

        function appendMessage(content, type) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${type}-message`;

            if (type === 'ai') {
                const avatar = document.createElement('img');
                avatar.src = 'images/default-avatar.png';
                avatar.alt = 'Dara AI';
                avatar.className = 'avatar';
                messageDiv.appendChild(avatar);
            }

            const contentDiv = document.createElement('div');
            contentDiv.className = 'message-content';
            contentDiv.innerHTML = content;
            messageDiv.appendChild(contentDiv);

            messagesContainer.appendChild(messageDiv);

            requestAnimationFrame(() => {
                messageDiv.classList.add('visible');
                scrollToBottom();
            });
        }

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, {
            threshold: 0.1
        });

        document.querySelectorAll('.message').forEach(message => {
            observer.observe(message);
        });

        chatForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const prompt = promptInput.value.trim();
            if (!prompt) return;

            appendMessage(prompt, 'user');
            promptInput.value = '';
            promptInput.disabled = true;
            typingIndicator.classList.add('active');
            scrollToBottom();

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

        scrollToBottom(false);
        window.addEventListener('resize', () => scrollToBottom(false));
    </script>
</body>

</html>