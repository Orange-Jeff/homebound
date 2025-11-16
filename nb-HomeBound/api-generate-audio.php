<?php
// API endpoint to generate audio
header('Content-Type: application/json');

$config_file = __DIR__ . '/config.json';
$config = json_decode(file_get_contents($config_file), true);

$input = json_decode(file_get_contents('php://input'), true);
$text = $input['text'] ?? '';
$turn = $input['turn'] ?? 1;

if (empty($config['api_key'])) {
    echo json_encode(['success' => false, 'error' => 'No API key configured']);
    exit;
}

if (empty($text)) {
    echo json_encode(['success' => false, 'error' => 'Text is required']);
    exit;
}

$audio_url = generate_audio($text, $turn, $config['api_key']);

if ($audio_url === false) {
    echo json_encode(['success' => false, 'error' => 'Failed to generate audio']);
    exit;
}

echo json_encode(['success' => true, 'audio_url' => $audio_url]);

function generate_audio($text, $turn, $api_key) {
    $url = "https://texttospeech.googleapis.com/v1/text:synthesize?key={$api_key}";

    $data = [
        'input' => ['text' => $text],
        'voice' => [
            'languageCode' => 'en-US',
            'name' => 'en-US-Journey-F',
            'ssmlGender' => 'FEMALE'
        ],
        'audioConfig' => [
            'audioEncoding' => 'MP3',
            'speakingRate' => 1.0,
            'pitch' => 0.0
        ]
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code !== 200) {
        error_log("Audio generation error: HTTP $http_code - $response");
        return false;
    }

    $body = json_decode($response, true);

    if (!isset($body['audioContent'])) {
        return false;
    }

    $audio_data = base64_decode($body['audioContent']);
    $assets_dir = __DIR__ . '/generated-days/assets';

    if (!file_exists($assets_dir)) {
        mkdir($assets_dir, 0755, true);
    }

    $filename = 'turn_' . $turn . '_' . uniqid() . '.mp3';
    $file_path = $assets_dir . '/' . $filename;

    if (file_put_contents($file_path, $audio_data)) {
        return 'assets/' . $filename;
    }

    return false;
}
?>
