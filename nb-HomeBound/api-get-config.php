<?php
// API endpoint to get configuration
header('Content-Type: application/json');

$config_file = __DIR__ . '/config.json';

if (file_exists($config_file)) {
    $config = json_decode(file_get_contents($config_file), true);
} else {
    $config = [
        'api_key' => '',
        'story_character' => 'a female janitor who accidentally activated a wormhole switch',
        'story_theme' => 'space adventure with humor and sarcasm',
        'story_goal' => 'trying to get home before her cat starves',
        'story_tone' => 'Family-friendly but humorous and wild',
        'character_personality' => 'sarcastic but positive, worried about getting in trouble'
    ];
}

echo json_encode($config);
?>
