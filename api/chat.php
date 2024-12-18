<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Replace with your actual Gemini API key
$API_KEY = 'YOUR_API_KEY';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Check if cURL is installed
        if (!function_exists('curl_init')) {
            throw new Exception('cURL is not installed on the server');
        }

        // Get and validate input
        $input = file_get_contents('php://input');
        if (!$input) {
            throw new Exception('No input received');
        }

        $data = json_decode($input, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON: ' . json_last_error_msg());
        }

        $userMessage = $data['message'] ?? '';
        if (empty($userMessage)) {
            throw new Exception('Message is required');
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
                'maxOutputTokens' => 800,
                'temperature' => 0.7
            ]
        ];

        // Initialize cURL session
        $ch = curl_init($url . '?key=' . $API_KEY);
        if ($ch === false) {
            throw new Exception('Failed to initialize cURL');
        }

        // Set cURL options
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($requestData),
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_VERBOSE => true
        ]);

        // Execute cURL request
        $response = curl_exec($ch);
        
        // Check for cURL errors
        if ($response === false) {
            $error = curl_error($ch);
            $info = curl_getinfo($ch);
            curl_close($ch);
            throw new Exception('cURL error: ' . $error . '. Info: ' . json_encode($info));
        }

        // Get HTTP status code
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception('HTTP error: ' . $httpCode . '. Response: ' . $response);
        }

        // Parse response
        $result = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Failed to parse API response: ' . json_last_error_msg());
        }

        if (isset($result['error'])) {
            throw new Exception($result['error']['message'] ?? 'Unknown API error');
        }

        // Extract the generated text from response
        $generatedText = $result['candidates'][0]['content']['parts'][0]['text'] ?? null;
        if ($generatedText === null) {
            throw new Exception('No response text in API result: ' . json_encode($result));
        }

        echo json_encode([
            'success' => true,
            'response' => $generatedText
        ]);

    } catch (Exception $e) {
        error_log('Chat API Error: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error' => 'Method not allowed'
    ]);
}
?>