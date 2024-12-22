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
            bottom: 32px; /* Height of the footer */
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
            padding-bottom: 50px; /* Space for form + footer */
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
            <div id="chat-messages" class="flex-1 space-y-4">
                <div id="welcome-message" class="text-center text-gray-500 text-xl py-20">
                    Mulai mengajukan pertanyaan tentang data BPS Kabupaten Siak
                </div>
            </div>
            
            <form id="chat-form" class="flex gap-1 bg-white">
                <div class="flex gap-2 w-full">
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
                        Kirim
                    </button>
                </div>
            </form>
            <div class="fixed bottom-0 left-0 w-full text-center py-2 text-sm text-gray-500 bg-white">
                AI Data Assistant dapat membuat kesalahan. Mohon periksa kembali informasi penting.
            </div>
        </div>
    </div>

    <script src="js/chat.js"></script>
</body>
</html>