<?php
/**
 * nb-HomeBound - Generate Single Field
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
    curl_close($ch);

    if ($http_code !== 200) {
        error_log("Gemini API Error: HTTP $http_code - $response");
        return false;
    }

    $body = json_decode($response, true);

    if (!isset($body['candidates'][0]['content']['parts'][0]['text'])) {
        error_log("Invalid Gemini response structure");
        return false;
    }

    $content_text = trim($body['candidates'][0]['content']['parts'][0]['text']);
    $content_data = json_decode($content_text, true);

    if (!$content_data) {
        error_log("Failed to parse JSON response from Gemini");
        return false;
    }

    return $content_data;
}

// API endpoint to generate a single field
header('Content-Type: application/json');

$config_file = __DIR__ . '/config.json';
$config = json_decode(file_get_contents($config_file), true);

$input = json_decode(file_get_contents('php://input'), true);
$field = $input['field'] ?? '';
$turn_number = $input['turn_number'] ?? 1;

if (empty($config['api_key'])) {
    echo json_encode(['success' => false, 'error' => 'No API key configured']);
    exit;
}

// Generate content for the requested field
$content = call_gemini_api($config['api_key'], build_story_prompt($turn_number, $config), 'gemini-1.5-pro-latest');

if ($content === false) {
    echo json_encode(['success' => false, 'error' => 'Failed to generate content']);
    exit;
}

// Map field to content key
$field_map = [
    'planet_name' => 'planet_name',
    'turn1_para1' => 'para1', 'turn1_para2' => 'para2', 'turn1_para3' => 'para3',
    'turn1_choice_a' => 'choice_a', 'turn1_choice_b' => 'choice_b', 'turn1_death_desc' => 'death_desc',
    'turn2_para1' => 'para1', 'turn2_para2' => 'para2', 'turn2_para3' => 'para3',
    'turn2_choice_a' => 'choice_a', 'turn2_choice_b' => 'choice_b', 'turn2_death_desc' => 'death_desc',
    'turn3_para1' => 'para1', 'turn3_para2' => 'para2', 'turn3_para3' => 'para3',
    'turn3_choice_a' => 'choice_a', 'turn3_choice_b' => 'choice_b', 'turn3_death_desc' => 'death_desc',
    'home_trip_desc' => 'home_trip_desc'
];

$content_key = $field_map[$field] ?? null;

if ($field === 'home_trip_desc') {
    $home_desc = "Against all odds, you've navigated through the challenges and found your way back home! Mission accomplished!";
    echo json_encode(['success' => true, 'content' => $home_desc]);
} elseif ($content_key && isset($content[$content_key])) {
    echo json_encode(['success' => true, 'content' => $content[$content_key]]);
} else {
    echo json_encode(['success' => false, 'error' => 'Field not found in generated content']);
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
    } else {
        $prompt .= "This is an early turn - choices have moderate risk of failure or setbacks.\n\n";
    }

    $prompt .= "Generate the following in JSON format:\n";
    $prompt .= "{\n";
    $prompt .= '  "planet_name": "Creative location name",'."\n";
    $prompt .= '  "para1": "First paragraph - What they see and initial situation (100 words)",'."\n";
    $prompt .= '  "para2": "Second paragraph - Details of the predicament (100 words)",'."\n";
    $prompt .= '  "para3": "Third paragraph - The dilemma requiring a choice (100 words)",'."\n";
    $prompt .= '  "death_desc": "Description of failure if wrong choice made",'."\n";
    $prompt .= '  "choice_a": "First choice option (action to take)",'."\n";
    $prompt .= '  "choice_b": "Second choice option (different action)"'."\n";
    $prompt .= "}\n\n";
    $prompt .= "Make it creative, entertaining, and memorable!";

    return $prompt;
}
?>
