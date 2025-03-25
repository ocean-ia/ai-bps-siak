
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

    // Construct system message and initial greeting
    $system_message = "Anda adalah program AI yang dikembangkan untuk membantu pengguna data bps kabupaten siak dalam mencari data dan informasi statistik. Nama anda adalah Dara, Data Assistant and Response AI BPS Kabupaten Siak. Anda adalah petugas yang ahli statistik dan siap membantu setiap pengguna data dengan layanan terbaik.";
    
    $initial_greeting = "Halo! Saya Dara, Data Assistant and Response AI BPS Kabupaten Siak. Senang sekali bisa membantu Anda menemukan data dan informasi statistik yang Anda butuhkan dari BPS Kabupaten Siak. Sebagai petugas ahli statistik, saya siap memberikan layanan terbaik. Silakan sampaikan pertanyaan atau kebutuhan data Anda secara detail agar saya dapat memberikan informasi yang akurat dan tepat. Semakin detail pertanyaan Anda, semakin akurat pula hasil yang saya berikan. Apa yang dapat saya bantu?";

    // Add data categories information as context
    $dataCategories = "Kategori Kependudukan dan Migrasi: https://siakkab.bps.go.id/id/statistics-table?subject=519, 
    Tenaga Kerja: https://siakkab.bps.go.id/id/statistics-table?subject=520, 
    Pendidikan: https://siakkab.bps.go.id/id/statistics-table?subject=521,
    Kesehatan: https://siakkab.bps.go.id/id/statistics-table?subject=522,
    Konsumsi dan Pendapatan: https://siakkab.bps.go.id/id/statistics-table?subject=523,
    Perlindungan Sosial: https://siakkab.bps.go.id/id/statistics-table?subject=524,
    Pemukiman dan Perumahan: https://siakkab.bps.go.id/id/statistics-table?subject=525,
    Hukum dan Kriminal: https://siakkab.bps.go.id/id/statistics-table?subject=526,
    Budaya: https://siakkab.bps.go.id/id/statistics-table?subject=527,
    Aktivitas Politik dan Komunitas Lainnya: https://siakkab.bps.go.id/id/statistics-table?subject=528,
    Penggunaan Waktu: https://siakkab.bps.go.id/id/statistics-table?subject=529";

    // Add contact information
    $contactInfo = "Instagram: http://s.bps.go.id/instagrambpssiak
    Facebook: http://s.bps.go.id/facebookbpssiak
    YouTube: http://s.bps.go.id/youtubebpssiak
    Website: https://siakkab.bps.go.id
    Email: bps1405@bps.go.id
    WA: 085183111405
    Alamat: http://s.bps.go.id/alamatbpssiak";
    
    // Construct conversation history with proper formatting
    $history = [];
    
    // Add system message
    $history[] = [
        "role" => "user",
        "parts" => [["text" => $system_message]]
    ];
    
    // Add AI response
    $history[] = [
        "role" => "model",
        "parts" => [["text" => $initial_greeting]]
    ];
    
    // Add information about data categories
    $history[] = [
        "role" => "user",
        "parts" => [["text" => "Berikut adalah kategori data yang tersedia: " . $dataCategories]]
    ];
    
    // Add model acknowledgment
    $history[] = [
        "role" => "model",
        "parts" => [["text" => "Terima kasih atas informasi kategori data yang tersedia. Saya akan menggunakan informasi ini untuk membantu pengguna menemukan data yang mereka butuhkan."]]
    ];
    
    // Add contact information
    $history[] = [
        "role" => "user",
        "parts" => [["text" => "Berikut adalah informasi kontak BPS Kabupaten Siak: " . $contactInfo]]
    ];
    
    // Add model acknowledgment
    $history[] = [
        "role" => "model",
        "parts" => [["text" => "Terima kasih atas informasi kontak BPS Kabupaten Siak. Saya akan memberikan informasi ini kepada pengguna jika mereka membutuhkan kontak langsung."]]
    ];

    // Add information about BPS leadership
    $history[] = [
        "role" => "user",
        "parts" => [["text" => "Nama kepala BPS Kabupaten Siak saat ini adalah Prayudho Bagus Jatmiko."]]
    ];
    
    // Add model acknowledgment
    $history[] = [
        "role" => "model",
        "parts" => [["text" => "Terima kasih atas informasi bahwa Kepala BPS Kabupaten Siak saat ini adalah Bapak Prayudho Bagus Jatmiko. Saya akan menyampaikan informasi ini jika ditanyakan."]]
    ];

    // Add current user message
    $history[] = [
        "role" => "user",
        "parts" => [["text" => $userMessage]]
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
