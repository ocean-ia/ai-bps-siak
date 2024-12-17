<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/vendor/autoload.php';

// Initialize Gemini API
function initGeminiAI() {
    $API_KEY = 'YOUR_API_KEY'; // Replace with your actual API key
    
    // Initialize Google Client
    $client = new Google\Client();
    $client->setDeveloperKey($API_KEY);
    
    return $client;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        $userMessage = $data['message'] ?? '';
        
        if (empty($userMessage)) {
            throw new Exception('Message is required');
        }

        $client = initGeminiAI();
        
        // Initialize chat history
        $history = [
            [
                "role" => "user",
                "parts" => [["text" => "Anda adalah ahli statistik yang bertugas sebagai petugas pelayanan statistik di BPS Kabupaten Siak"]]
            ],
            [
                "role" => "model",
                "parts" => [["text" => "Baik, saya berperan sebagai ahli statistik yang bertugas di BPS Kabupaten Siak..."]]
            ]
        ];

        // Prepare the request to Gemini API
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent';
        $data = [
            'contents' => array_merge($history, [
                [
                    'role' => 'user',
                    'parts' => [['text' => $userMessage]]
                ]
            ]),
            'generationConfig' => [
                'maxOutputTokens' => 100,
                'temperature' => 0.7
            ]
        ];

        // Make request to Gemini API
        $ch = curl_init($url . '?key=' . $API_KEY);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch));
        }
        
        curl_close($ch);
        
        $result = json_decode($response, true);
        
        if (isset($result['error'])) {
            throw new Exception($result['error']['message']);
        }

        // Extract the generated text from response
        $generatedText = $result['candidates'][0]['content']['parts'][0]['text'] ?? 'No response generated';

        echo json_encode([
            'success' => true,
            'response' => $generatedText
        ]);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
}
?>