<?php
/**
 * nb-HomeBound - Test API Connection
 * Version 1.3 - 2025-11-14 - Switched to v1 API endpoint and gemini-1.5-pro-latest model.
 */
require_once 'api-helpers.php';

// Test the connection to the Google Gemini API
function test_gemini_connection($api_key) {
    $model = 'gemini-1.5-pro-latest';
    $url = "https://generativelanguage.googleapis.com/v1/models/{$model}:generateContent?key={$api_key}";

    $data = [
        'contents' => [
            [
                'parts' => [
                    ['text' => 'Say "Hello" in one word.']
                ]
            ]
        ]
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);

    if ($curl_error) {
        return [
            'success' => false,
            'error' => 'Network error',
            'details' => $curl_error,
            'fix' => 'Check internet connection'
        ];
    }

    if ($http_code !== 200) {
        $error_body = json_decode($response, true);
        $error_msg = $error_body['error']['message'] ?? 'Unknown error';

        return [
            'success' => false,
            'error' => "API Error (HTTP $http_code)",
            'details' => $error_msg,
            'fix' => $http_code == 400 ? 'Check API key is valid' : 'Try again in a moment'
        ];
    }

    $body = json_decode($response, true);

    if (!isset($body['candidates'][0]['content']['parts'][0]['text'])) {
        return [
            'success' => false,
            'error' => 'Invalid API response',
            'details' => 'Response missing expected fields',
            'response' => $body
        ];
    }

    return [
        'success' => true,
        'message' => '✓ API connection successful!',
        'api_response' => $body['candidates'][0]['content']['parts'][0]['text'],
        'config_ok' => true,
        'curl_ok' => true,
        'key_ok' => true
    ];
}

// Diagnostic endpoint to test API connection
header('Content-Type: application/json');

$config_file = __DIR__ . '/config.json';

// Check config file exists
if (!file_exists($config_file)) {
    echo json_encode([
        'success' => false,
        'error' => 'Config file not found',
        'fix' => 'Save settings in admin.php first'
    ]);
    exit;
}

$config = json_decode(file_get_contents($config_file), true);

// Check API key
if (empty($config['api_key'])) {
    echo json_encode([
        'success' => false,
        'error' => 'No API key configured',
        'fix' => 'Add API key in Story Settings'
    ]);
    exit;
}

// Test API with simple request
$api_key = $config['api_key'];
$model = 'gemini-1.5-pro-latest'; // Switched from gemini-1.5-pro due to 404 error
$url = "https://generativelanguage.googleapis.com/v1/models/{$model}:generateContent?key={$api_key}";

$data = [
    'contents' => [
        [
            'parts' => [
                ['text' => 'Say "Hello" in one word.']
            ]
        ]
    ]
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_TIMEOUT, 15);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

if ($curl_error) {
    echo json_encode([
        'success' => false,
        'error' => 'Network error',
        'details' => $curl_error,
        'fix' => 'Check internet connection'
    ]);
    exit;
}

if ($http_code !== 200) {
    $error_body = json_decode($response, true);
    $error_msg = $error_body['error']['message'] ?? 'Unknown error';

    echo json_encode([
        'success' => false,
        'error' => "API Error (HTTP $http_code)",
        'details' => $error_msg,
        'fix' => $http_code == 400 ? 'Check API key is valid' : 'Try again in a moment'
    ]);
    exit;
}

$body = json_decode($response, true);

if (!isset($body['candidates'][0]['content']['parts'][0]['text'])) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid API response',
        'details' => 'Response missing expected fields',
        'response' => $body
    ]);
    exit;
}

// Success!
echo json_encode([
    'success' => true,
    'message' => '✓ API connection successful!',
    'api_response' => $body['candidates'][0]['content']['parts'][0]['text'],
    'config_ok' => true,
    'curl_ok' => true,
    'key_ok' => true
]);
?>
