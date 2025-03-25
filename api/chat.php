
<?php
require_once 'config.php';
require_once 'utils/MessageFormatter.php';
require_once 'utils/SessionManager.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

SessionManager::init();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userMessage = $_POST['prompt'] ?? '';
    
    if (empty($userMessage)) {
        echo json_encode(['error' => 'No message provided']);
        exit();
    }

    SessionManager::addMessage('user', htmlspecialchars($userMessage));

    // Construct system message
    $system_message = "Anda adalah program AI yang dikembangkan untuk membantu pengguna data bps kabupaten siak dalam mencari data dan informasi statistik. Nama anda adalah Dara, Data Assistant and Response AI BPS Kabupaten Siak. Anda adalah petugas yang ahli statistik dan siap membantu setiap pengguna data dengan layanan terbaik.";
    
    // Add data categories and contact information
    $dataCategories = "Kategori data BPS Kabupaten Siak: Kependudukan (https://siakkab.bps.go.id/id/statistics-table?subject=519), Tenaga Kerja (https://siakkab.bps.go.id/id/statistics-table?subject=520), Pendidikan (https://siakkab.bps.go.id/id/statistics-table?subject=521), Kesehatan (https://siakkab.bps.go.id/id/statistics-table?subject=522), Konsumsi (https://siakkab.bps.go.id/id/statistics-table?subject=523), Perlindungan Sosial (https://siakkab.bps.go.id/id/statistics-table?subject=524), Pemukiman (https://siakkab.bps.go.id/id/statistics-table?subject=525)";
    
    $contactInfo = "Kontak BPS Siak: Instagram (http://s.bps.go.id/instagrambpssiak), Facebook (http://s.bps.go.id/facebookbpssiak), YouTube (http://s.bps.go.id/youtubebpssiak), Website (https://siakkab.bps.go.id), Email (bps1405@bps.go.id), WA (085183111405)";
    
    // Create proper conversation history for Gemini API
    $history = [
        [
            "role" => "user",
            "parts" => [["text" => $system_message]]
        ],
        [
            "role" => "model",
            "parts" => [["text" => "Halo! Saya Dara, Data Assistant and Response AI BPS Kabupaten Siak. Senang sekali bisa membantu Anda menemukan data dan informasi statistik yang Anda butuhkan."]]
        ],
        [
            "role" => "user",
            "parts" => [["text" => $dataCategories]]
        ],
        [
            "role" => "model",
            "parts" => [["text" => "Terima kasih atas informasi kategori data yang tersedia. Saya akan menggunakan informasi ini untuk membantu pengguna."]]
        ],
        [
            "role" => "user", 
            "parts" => [["text" => $contactInfo]]
        ],
        [
            "role" => "model",
            "parts" => [["text" => "Terima kasih atas informasi kontak BPS Kabupaten Siak. Saya akan memberikan informasi ini kepada pengguna jika diperlukan."]]
        ],
        [
            "role" => "user",
            "parts" => [["text" => "Nama kepala BPS Kabupaten Siak saat ini adalah Prayudho Bagus Jatmiko."]]
        ],
        [
            "role" => "model",
            "parts" => [["text" => "Terima kasih atas informasi bahwa Kepala BPS Kabupaten Siak saat ini adalah Bapak Prayudho Bagus Jatmiko."]]
        ],
        [
            "role" => "user",
            "parts" => [["text" => $userMessage]]
        ]
    ];

    $requestData = [
        'contents' => $history,
        'generationConfig' => [
            'maxOutputTokens' => 800,
            'temperature' => 0.7
        ]
    ];

    $ch = curl_init();
    $fullUrl = API_BASE_URL . '?key=' . GEMINI_API_KEY;
    
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    $verbose = fopen('php://temp', 'w+');
    curl_setopt($ch, CURLOPT_STDERR, $verbose);
    
    curl_setopt_array($ch, [
        CURLOPT_URL => $fullUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($requestData),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json'
        ],
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_TIMEOUT => 30
    ]);

    $response = curl_exec($ch);
    
    if ($response === false) {
        $error = curl_error($ch);
        $errorNo = curl_errno($ch);
        rewind($verbose);
        $verboseLog = stream_get_contents($verbose);
        error_log("CURL Error ($errorNo): $error");
        error_log("Verbose information: " . $verboseLog);
        $aiResponse = "Maaf, terjadi kesalahan dalam memproses permintaan Anda. Error: " . $error;
    } else {
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpCode !== 200) {
            error_log("HTTP Error: $httpCode, Response: $response");
            $aiResponse = "Maaf, terjadi kesalahan dalam memproses permintaan Anda. HTTP Code: " . $httpCode;
        } else {
            $result = json_decode($response, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log("JSON Decode Error: " . json_last_error_msg());
                $aiResponse = "Maaf, terjadi kesalahan dalam memproses format response.";
            } else {
                $aiResponse = $result['candidates'][0]['content']['parts'][0]['text'] ?? 'Maaf, saya tidak dapat memproses permintaan Anda saat ini.';
            }
        }
    }
    
    $formattedResponse = AIMessageFormatter::format($aiResponse);
    SessionManager::addMessage('ai', $formattedResponse);
    
    curl_close($ch);
    fclose($verbose);
    
    echo json_encode(['response' => $formattedResponse]);
    exit();
}

echo json_encode(['error' => 'Invalid request method']);
exit();
?>
