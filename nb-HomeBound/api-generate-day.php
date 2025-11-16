<?php
/**
 * nb-HomeBound - Generate Full Day
 * Version 1.3 - 2025-11-14 - Switched to v1 API endpoint and gemini-1.5-pro-latest model.
 */

require_once 'api-helpers.php';

// Shared function to call Gemini API
function call_gemini_api($api_key, $prompt, $model = 'gemini-1.5-pro-latest') {
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
            'temperature' => 0.9,
            'topK' => 40,
            'topP' => 0.95,
            'maxOutputTokens' => 2048,
            'responseMimeType' => 'application/json'
        ]
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_TIMEOUT, 45);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);

    if ($curl_error) {
        error_log("HomeBound cURL Error: $curl_error");
        return false;
    }

    if ($http_code !== 200) {
        error_log("HomeBound API Error: HTTP $http_code - $response");
        return false;
    }

    $body = json_decode($response, true);

    if (!isset($body['candidates'][0]['content']['parts'][0]['text'])) {
        error_log("HomeBound: Invalid API response structure - " . print_r($body, true));
        return false;
    }

    $content_text = trim($body['candidates'][0]['content']['parts'][0]['text']);
    $parsed = json_decode($content_text, true);

    if ($parsed === null) {
        error_log("HomeBound: Failed to parse JSON from Gemini - $content_text");
        return false;
    }

    return $parsed;
}

// API endpoint to generate complete day with all turns
header('Content-Type: application/json');

$config_file = __DIR__ . '/config.json';
$config = json_decode(file_get_contents($config_file), true);

$input = json_decode(file_get_contents('php://input'), true);
$generate_audio = $input['generate_audio'] ?? false;

if (empty($config['api_key'])) {
    echo json_encode(['success' => false, 'error' => 'No API key configured']);
    exit;
}

$day_data = generate_entire_day($config, $generate_audio);

if ($day_data === false) {
    // Check error log for details
    $error_msg = 'Failed to generate story. Check your API key and internet connection.';
    error_log("HomeBound: generate_entire_day returned false");
    echo json_encode(['success' => false, 'error' => $error_msg]);
    exit;
}

echo json_encode(['success' => true, 'data' => $day_data]);

function generate_entire_day($config, $generate_audio) {
    $day_data = ['planet_name' => ''];

    // Generate 3 turns
    for ($turn = 1; $turn <= 3; $turn++) {
        $turn_content = generate_turn_content($turn, $config);

        if ($turn_content === false) {
            error_log("HomeBound: Failed to generate turn $turn - check API key and quota");
            return false;
        }

        error_log("HomeBound: Turn $turn generated successfully");

        // Store planet name from first turn
        if ($turn === 1 && isset($turn_content['planet_name'])) {
            $day_data['planet_name'] = $turn_content['planet_name'];
        }

        // Map turn content
        $day_data["turn{$turn}_para1"] = $turn_content['para1'] ?? '';
        $day_data["turn{$turn}_para2"] = $turn_content['para2'] ?? '';
        $day_data["turn{$turn}_para3"] = $turn_content['para3'] ?? '';
        $day_data["turn{$turn}_death_desc"] = $turn_content['death_desc'] ?? '';
        $day_data["turn{$turn}_choice_a"] = $turn_content['choice_a'] ?? '';
        $day_data["turn{$turn}_choice_b"] = $turn_content['choice_b'] ?? '';

        // Generate image for this turn
        $image_prompt = build_image_prompt($turn, $day_data);
        $image_url = generate_image($image_prompt, $turn, $config['api_key']);
        if ($image_url) {
            $day_data["turn{$turn}_image_url"] = $image_url;
        }

        // Generate audio if requested
        if ($generate_audio) {
            $full_text = ($turn_content['para1'] ?? '') . ' ' .
                        ($turn_content['para2'] ?? '') . ' ' .
                        ($turn_content['para3'] ?? '');
            $audio_url = generate_audio($full_text, $turn, $config['api_key']);
            if ($audio_url) {
                $day_data["turn{$turn}_audio_url"] = $audio_url;
            }
        }

        // Small delay to respect rate limits
        usleep(500000); // 0.5 seconds
    }

    $day_data['home_trip_desc'] = "Against all odds, you've navigated through the challenges and found your way back home! Mission accomplished!";

    return $day_data;
}

function generate_turn_content($turn_number, $config) {
    $api_key = $config['api_key'];
    $model = 'gemini-1.5-pro-latest'; // Use the stable model
    $url = "https://generativelanguage.googleapis.com/v1/models/{$model}:generateContent?key={$api_key}";

    $prompt = build_story_prompt($turn_number, $config);

    $data = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $prompt]
                ]
            ]
        ],
        'generationConfig' => [
            'temperature' => 0.9,
            'topK' => 40,
            'topP' => 0.95,
            'maxOutputTokens' => 2048,
            'responseMimeType' => 'application/json'
        ]
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_TIMEOUT, 45);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);

    if ($curl_error) {
        error_log("HomeBound cURL Error: $curl_error");
        return false;
    }

    if ($http_code !== 200) {
        error_log("HomeBound API Error: HTTP $http_code - $response");
        return false;
    }

    $body = json_decode($response, true);

    if (!isset($body['candidates'][0]['content']['parts'][0]['text'])) {
        error_log("HomeBound: Invalid API response structure - " . print_r($body, true));
        return false;
    }

    $content_text = trim($body['candidates'][0]['content']['parts'][0]['text']);
    $parsed = json_decode($content_text, true);

    if ($parsed === null) {
        error_log("HomeBound: Failed to parse JSON from Gemini - $content_text");
        return false;
    }

    return $parsed;
}

function build_story_prompt($turn_number, $config) {
    $is_final_turn = ($turn_number === 3);

    $prompt = "You are creating a daily interactive adventure story. ";
    $prompt .= "CHARACTER: " . $config['story_character'] . ". ";
    $prompt .= "PERSONALITY: " . $config['character_personality'] . ". ";
    $prompt .= "THEME: " . $config['story_theme'] . ". ";
    $prompt .= "GOAL: " . $config['story_goal'] . ". ";
    $prompt .= "TONE: " . $config['story_tone'] . ".\n\n";

    $prompt .= "TURN: This is turn $turn_number of 3.\n\n";

    if ($is_final_turn) {
        $prompt .= "This is the FINAL turn - there should be a very high chance of success on both choices.\n\n";
    }

    $prompt .= "Generate the following in JSON format:\n";
    $prompt .= "{\n";
    $prompt .= '  "planet_name": "Creative location name",'."\n";
    $prompt .= '  "para1": "First paragraph (100 words)",'."\n";
    $prompt .= '  "para2": "Second paragraph (100 words)",'."\n";
    $prompt .= '  "para3": "Third paragraph (100 words)",'."\n";
    $prompt .= '  "death_desc": "Failure description",'."\n";
    $prompt .= '  "choice_a": "First choice",'."\n";
    $prompt .= '  "choice_b": "Second choice"'."\n";
    $prompt .= "}\n";

    return $prompt;
}

function build_image_prompt($turn, $story_data) {
    $planet = $story_data['planet_name'] ?? 'alien location';
    $base_prompt = "Sci-fi scene, dramatic lighting, detailed, high quality, ";

    switch ($turn) {
        case 1:
            $base_prompt .= "arrival at {$planet}, dramatic arrival scene";
            break;
        case 2:
            $base_prompt .= "adventure on {$planet}, exploration scene";
            break;
        case 3:
            $base_prompt .= "climactic scene on {$planet}, intense action";
            break;
    }

    return $base_prompt;
}

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
