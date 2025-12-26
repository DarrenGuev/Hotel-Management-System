<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['prompt'])) {

    $dataFile = __DIR__ . '/chatbot/data.txt';
    $data = file_exists($dataFile) ? file_get_contents($dataFile) : '';

    // Prepare the prompt for Ollama
    $prompt = "You are a helpful assistant for TravelMates Hotel, ready to assist guests with bookings, services, and travel information to ensure a comfortable stay.

INSTRUCTIONS:
1. ALWAYS respond in a friendly, conversational tone
2. For greetings (hello, hi, hey, good morning, etc.), respond warmly and ask how you can help
3. For questions about TravelMates, use ONLY the information provided in the dataset below
4. Keep responses concise (2-3 sentences maximum)
5. If asked about something not in the dataset, politely say you don't have that information
6. Be case-insensitive when matching questions

DATASET:
$data

USER MESSAGE: {$_POST['prompt']}

YOUR RESPONSE (keep it short and friendly):";

    $url = "http://127.0.0.1:11434/api/chat";

    $payload = json_encode([
        'model' => 'qwen3:0.6b',
        'messages' => [
            ['role' => 'user', 'content' => $prompt]
        ],
        'stream' => false
    ]);

    $options = [
        'http' => [
            'method'  => 'POST',
            'header'  => "Content-Type: application/json",
            'content' => $payload,
            'ignore_errors' => true
        ]
    ];

    $context  = stream_context_create($options);
    $response = file_get_contents($url, false, $context);

    $data = json_decode($response, true);
    $reply = $data['message']['content'] ?? '(no reply)';

    echo $reply;
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/chatbot.css">
</head>

<body>
    <div class="chat-container" id="chat">
        <div class="welcome-message">
            <div class="bot-avatar"><img src="../images/" alt="Bot"></div> <!--unfinished image path -->
            <div class="welcome-text">
                Hi! I am your personal assistant.<br>How can I help you?
            </div>
        </div>
    </div>

    <div class="input-container">
        <input type="text" id="userInput" placeholder="Type your message" onkeypress="handleKeyPress(event)">
        <button class="send-button" onclick="send_to_chat()" id="sendBtn">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                <path d="M22 2L11 13M22 2L15 22L11 13M22 2L2 9L11 13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
    </div>

    <div class="powered-by">
        Powered by <b>Ollama Model qwen3:0.6b</b>
    </div>

    <script>
        function handleKeyPress(event) {
            if (event.key === 'Enter') {
                send_to_chat();
            }
        }

        function send_to_chat() {
            const chat = document.getElementById("chat");
            const input = document.getElementById("userInput");
            const sendBtn = document.getElementById("sendBtn");
            const text = input.value.trim();

            if (!text) return;

            // Add user message
            const userMsg = document.createElement('div');
            userMsg.className = 'message user';
            userMsg.innerHTML = `<div class="message-content">${escapeHtml(text)}</div>`;
            chat.appendChild(userMsg);

            input.value = "";
            sendBtn.disabled = true;
            chat.scrollTop = chat.scrollHeight;

            // Add typing indicator
            const typingMsg = document.createElement('div');
            typingMsg.className = 'message bot';
            typingMsg.id = 'typing_' + Date.now();
            typingMsg.innerHTML = `
                <div class="bot-avatar"><img src="../images/" alt="Bot"></div> <!--unfinished image path -->
                <div class="message-content">
                    <div class="typing-indicator">
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                    </div>
                </div>
            `;
            chat.appendChild(typingMsg);
            chat.scrollTop = chat.scrollHeight;

            fetch("chatbot.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: "prompt=" + encodeURIComponent(text)
                })
                .then(res => res.text())
                .then(reply => {
                    // Remove typing indicator
                    typingMsg.remove();

                    // Add bot reply
                    const botMsg = document.createElement('div');
                    botMsg.className = 'message bot';
                    botMsg.innerHTML = `
                        <div class="bot-avatar"><img src="images/ollama.png" alt="Bot"></div> <!--unfinished image path -->
                        <div class="message-content">${escapeHtml(reply)}</div>
                    `;
                    chat.appendChild(botMsg);
                    chat.scrollTop = chat.scrollHeight;
                    sendBtn.disabled = false;
                })
                .catch(() => {
                    typingMsg.remove();
                    const errorMsg = document.createElement('div');
                    errorMsg.className = 'message bot';
                    errorMsg.innerHTML = `
                        <div class="bot-avatar"><img src="images/ollama.png" alt="Bot"></div> <!--unfinished image path -->
                        <div class="message-content">Sorry, I encountered an error. Please try again.</div>
                    `;
                    chat.appendChild(errorMsg);
                    chat.scrollTop = chat.scrollHeight;
                    sendBtn.disabled = false;
                });
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>

</body>

</html>