<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Data Assistant BPS Kabupaten Siak</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto max-w-4xl px-4 py-8">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-blue-600 mb-2">
                Welcome to AI Data Assistant BPS Kabupaten Siak
            </h1>
            <p class="text-gray-600">
                Tanyakan informasi seputar data statistik Kabupaten Siak
            </p>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-4">
            <div id="chat-messages" class="h-[500px] overflow-y-auto mb-4 space-y-4">
                <div class="text-center text-gray-500 py-8">
                    Mulai mengajukan pertanyaan tentang data BPS Kabupaten Siak
                </div>
            </div>
            
            <form id="chat-form" class="flex gap-2">
                <input 
                    type="text" 
                    id="prompt-input"
                    name="prompt" 
                    placeholder="Contoh: Apa itu Badan Pusat Statistik?" 
                    class="flex-1 px-4 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500"
                >
                <button 
                    type="submit" 
                    class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors"
                >
                    Send
                </button>
            </form>
        </div>
    </div>

    <script>
        const form = document.getElementById('chat-form');
        const promptInput = document.getElementById('prompt-input');
        const chatMessages = document.getElementById('chat-messages');

        form.onsubmit = async (ev) => {
            ev.preventDefault();
            
            const userMessage = promptInput.value.trim();
            if (!userMessage) return;

            // Add user message to chat
            appendMessage(userMessage, true);
            promptInput.value = '';

            try {
                // Use relative path for API endpoint
                const response = await fetch('./api/chat.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ message: userMessage }),
                });

                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                const data = await response.json();
                
                if (!data.success) {
                    throw new Error(data.error || 'Unknown error occurred');
                }

                // Add AI response to chat
                appendMessage(data.response, false);

            } catch (error) {
                console.error('Error:', error);
                appendMessage('Terjadi kesalahan saat memproses pertanyaan Anda. Silakan coba lagi.', false, true);
            }
        };

        function appendMessage(message, isUser, isError = false) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `p-4 rounded-lg ${isUser ? 'bg-blue-100 ml-12' : 'bg-gray-100 mr-12'} ${isError ? 'bg-red-100' : ''}`;
            
            const textDiv = document.createElement('div');
            textDiv.className = 'text-gray-800';
            textDiv.textContent = message;
            
            messageDiv.appendChild(textDiv);
            chatMessages.appendChild(messageDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    </script>
</body>
</html>