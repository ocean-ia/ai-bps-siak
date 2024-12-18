<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Replace with your actual Gemini API key
$API_KEY = 'YOUR_API_KEY';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Read raw POST data
        $rawData = file_get_contents('php://input');
        if (!$rawData) {
            throw new Exception('No data received');
        }

        // Decode JSON data
        $data = json_decode($rawData, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON data');
        }

        $userMessage = $data['message'] ?? '';
        if (empty($userMessage)) {
            throw new Exception('Message is required');
        }

        // Initialize cURL
        if (!function_exists('curl_init')) {
            throw new Exception('cURL is not enabled on this server');
        }

        // Prepare the request to Gemini API
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent';
        
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

        $requestData = [
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
        if ($ch === false) {
            throw new Exception('Failed to initialize cURL');
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

        $response = curl_exec($ch);
        
        if ($response === false) {
            throw new Exception('cURL error: ' . curl_error($ch));
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpCode !== 200) {
            throw new Exception('HTTP error: ' . $httpCode);
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