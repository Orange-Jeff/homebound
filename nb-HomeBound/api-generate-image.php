<?php
// API endpoint to generate image
header('Content-Type: application/json');

$config_file = __DIR__ . '/config.json';
$config = json_decode(file_get_contents($config_file), true);

$input = json_decode(file_get_contents('php://input'), true);
$prompt = $input['prompt'] ?? '';
$turn = $input['turn'] ?? 1;

if (empty($config['api_key'])) {
    echo json_encode(['success' => false, 'error' => 'No API key configured']);
    exit;
}

if (empty($prompt)) {
    echo json_encode(['success' => false, 'error' => 'Prompt is required']);
    exit;
}

$image_url = generate_image($prompt, $turn, $config['api_key']);

if ($image_url === false) {
    echo json_encode(['success' => false, 'error' => 'Failed to generate image']);
    exit;
}

echo json_encode(['success' => true, 'image_url' => $image_url]);

function generate_image($prompt, $turn, $api_key) {
    $model = 'gemini-2.5-flash-image';
    $url = "https://generativelanguage.googleapis.com/v1/models/{$model}:generateContent?key={$api_key}";

    $data = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $prompt]
                ]
            ]
        ],
        'generationConfig' => [
            'responseMimeType' => 'image/png'
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
        error_log("Image generation error: HTTP $http_code - $response");
        return false;
    }

    $body = json_decode($response, true);
    $candidates = $body['candidates'] ?? [];

    foreach ($candidates as $candidate) {
        $parts = $candidate['content']['parts'] ?? [];
        foreach ($parts as $part) {
            if (isset($part['inlineData']['data'])) {
                $b64 = $part['inlineData']['data'];
                $image_data = base64_decode($b64);

                if ($image_data !== false) {
                    $assets_dir = __DIR__ . '/generated-days/assets';
                    if (!file_exists($assets_dir)) {
                        mkdir($assets_dir, 0755, true);
                    }

                    $filename = 'turn_' . $turn . '_' . uniqid() . '.png';
                    $file_path = $assets_dir . '/' . $filename;

                    if (file_put_contents($file_path, $image_data)) {
                        return 'assets/' . $filename;
                    }
                }
            }
        }
    }

    return false;
}
?>
