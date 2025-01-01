const form = document.getElementById('chat-form');
const promptInput = document.getElementById('prompt-input');
const chatMessages = document.getElementById('chat-messages');
const welcomeMessage = document.getElementById('welcome-message');
let isFirstSubmit = true;

form.onsubmit = async (ev) => {
    ev.preventDefault();
    
    const userMessage = promptInput.value.trim();
    if (!userMessage) return;

    if (isFirstSubmit) {
        isFirstSubmit = false;
        form.classList.add('sticky-form');
        welcomeMessage.style.display = 'none';
    }

    appendMessage(userMessage, true);
    promptInput.value = '';

    try {
        const response = await fetch('api/chat.php', {
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
    
    // If it's not a user message, render HTML content
    if (!isUser) {
        textDiv.innerHTML = message;
    } else {
        textDiv.textContent = message;
    }
    
    messageDiv.appendChild(textDiv);
    chatMessages.appendChild(messageDiv);
    window.scrollTo({
        top: document.body.scrollHeight,
        behavior: 'smooth'
    });
}