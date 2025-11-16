<?php
/**
 * nb-HomeBound Admin Tool
 * Version 1.3 - 2025-11-14 - Switched to v1 API endpoint and gemini-1.5-pro-latest model.
 * Creates daily turn-based story adventures with AI-generated content
 */

// Configuration file path
$config_file = __DIR__ . '/config.json';
$days_dir = __DIR__ . '/generated-days';
$assets_dir = __DIR__ . '/generated-days/assets';

// Load configuration
function load_config() {
    global $config_file;
    $config = [];
    if (file_exists($config_file)) {
        $config = json_decode(file_get_contents($config_file), true);
    } else {
        $config = get_default_config();
    }

    // If API key is empty in config, try to load it from gemini-api-key.txt
    if (empty($config['api_key'])) {
        $key_file = __DIR__ . '/../gemini-api-key.txt';
        if (file_exists($key_file)) {
            $key = trim(file_get_contents($key_file));
            if (!empty($key)) {
                $config['api_key'] = $key;
                save_config($config); // Save it for next time
            }
        }
    }
    return $config;
}

// Get default configuration
function get_default_config() {
    return [
        'api_key' => '',
        'story_character' => 'a female janitor who accidentally activated a wormhole switch',
        'story_theme' => 'space adventure with humor and sarcasm',
        'story_goal' => 'trying to get home before her cat starves',
        'story_tone' => 'Family-friendly but humorous and wild',
        'character_personality' => 'sarcastic but positive, worried about getting in trouble'
    ];
}

// Save configuration
function save_config($config) {
    global $config_file;
    file_put_contents($config_file, json_encode($config, JSON_PRETTY_PRINT));
}

// Get next day number
function get_next_day_number() {
    global $days_dir;
    if (!is_dir($days_dir)) {
        mkdir($days_dir, 0755, true);
    }

    $files = glob($days_dir . '/day-*.html');
    if (empty($files)) {
        return 1;
    }

    $numbers = array_map(function($file) {
        preg_match('/day-(\d+)\.html$/', $file, $matches);
        return isset($matches[1]) ? intval($matches[1]) : 0;
    }, $files);

    return max($numbers) + 1;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Save settings
    if (isset($_POST['save_settings'])) {
        $config = load_config();
        $config['api_key'] = $_POST['api_key'] ?? '';
        $config['story_character'] = $_POST['story_character'] ?? '';
        $config['story_theme'] = $_POST['story_theme'] ?? '';
        $config['story_goal'] = $_POST['story_goal'] ?? '';
        $config['story_tone'] = $_POST['story_tone'] ?? '';
        $config['character_personality'] = $_POST['character_personality'] ?? '';
        save_config($config);
        $message = "Settings saved successfully!";
    }

    // Save day
    if (isset($_POST['save_day'])) {
        $day_data = [
            'day_number' => intval($_POST['day_number']),
            'planet_name' => $_POST['planet_name'] ?? '',
            'turn1_para1' => $_POST['turn1_para1'] ?? '',
            'turn1_para2' => $_POST['turn1_para2'] ?? '',
            'turn1_para3' => $_POST['turn1_para3'] ?? '',
            'turn1_choice_a' => $_POST['turn1_choice_a'] ?? '',
            'turn1_choice_b' => $_POST['turn1_choice_b'] ?? '',
            'turn1_death_desc' => $_POST['turn1_death_desc'] ?? '',
            'turn1_image_url' => $_POST['turn1_image_url'] ?? '',
            'turn1_audio_url' => $_POST['turn1_audio_url'] ?? '',
            'turn2_para1' => $_POST['turn2_para1'] ?? '',
            'turn2_para2' => $_POST['turn2_para2'] ?? '',
            'turn2_para3' => $_POST['turn2_para3'] ?? '',
            'turn2_choice_a' => $_POST['turn2_choice_a'] ?? '',
            'turn2_choice_b' => $_POST['turn2_choice_b'] ?? '',
            'turn2_death_desc' => $_POST['turn2_death_desc'] ?? '',
            'turn2_image_url' => $_POST['turn2_image_url'] ?? '',
            'turn2_audio_url' => $_POST['turn2_audio_url'] ?? '',
            'turn3_para1' => $_POST['turn3_para1'] ?? '',
            'turn3_para2' => $_POST['turn3_para2'] ?? '',
            'turn3_para3' => $_POST['turn3_para3'] ?? '',
            'turn3_choice_a' => $_POST['turn3_choice_a'] ?? '',
            'turn3_choice_b' => $_POST['turn3_choice_b'] ?? '',
            'turn3_death_desc' => $_POST['turn3_death_desc'] ?? '',
            'turn3_image_url' => $_POST['turn3_image_url'] ?? '',
            'turn3_audio_url' => $_POST['turn3_audio_url'] ?? '',
            'home_trip_desc' => $_POST['home_trip_desc'] ?? ''
        ];

        // Generate the day HTML file
        generate_day_html($day_data);

        // Regenerate index
        generate_index_page();

        $message = "Day {$day_data['day_number']} created successfully!";
    }
}

$config = load_config();
$next_day = get_next_day_number();
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>nb-HomeBound Admin Console</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    <style>
        body {
            background-image: url('https://www.transparenttextures.com/patterns/low-contrast-linen.png');
            background-repeat: repeat;
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
    </style>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#E8772E",
                        "accent": "#00A9A5",
                        "background-dark": "#1A1A1A",
                        "text-light": "#F0EAD6",
                        "text-muted": "#a09c90",
                    },
                    fontFamily: {
                        "display": ["Space Grotesk", "sans-serif"]
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-background-dark font-display text-text-light">
    <div class="relative flex min-h-screen w-full flex-col">
        <!-- Header -->
        <header class="sticky top-0 z-10 flex items-center justify-between border-b border-primary/30 bg-background-dark/80 p-4 backdrop-blur-sm">
            <span class="material-symbols-outlined text-text-light text-3xl">terminal</span>
            <h1 class="text-xl font-bold uppercase tracking-widest text-primary">HomeBound Admin Console</h1>
            <a href="generated-days/index.html" target="_blank" class="material-symbols-outlined text-text-light text-3xl hover:text-primary">open_in_new</a>
        </header>

        <main class="flex-1 p-4 max-w-6xl mx-auto w-full">
            <?php if (isset($message)): ?>
            <div class="mb-4 rounded-lg border border-green-500/30 bg-green-500/10 p-4">
                <p class="text-green-400 font-medium"><?php echo htmlspecialchars($message); ?></p>
            </div>
            <?php endif; ?>

            <div class="flex flex-col gap-4">

                <!-- Settings Accordion -->
                <details class="flex flex-col rounded-lg border border-primary/30 bg-background-dark/50 px-4 py-2 group">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-6 py-2">
                        <p class="text-base font-bold uppercase text-primary">Story Settings</p>
                        <span class="material-symbols-outlined text-primary transition-transform group-open:rotate-180">expand_more</span>
                    </summary>
                    <form method="POST" class="flex flex-col gap-4 pt-4">
                        <label class="flex flex-col">
                            <p class="pb-2 text-sm font-medium uppercase text-text-light">Google AI API Key</p>
                            <input type="text" name="api_key" value="<?php echo htmlspecialchars($config['api_key']); ?>"
                                   class="form-input rounded-lg border border-primary/30 bg-black/30 p-3 text-text-light placeholder:text-text-muted focus:border-primary/50 focus:outline-0 focus:ring-0"
                                   placeholder="Your API key from aistudio.google.com">
                            <p class="pt-1 text-xs text-text-muted">One key for text, images, and audio generation</p>
                        </label>

                        <label class="flex flex-col">
                            <p class="pb-2 text-sm font-medium uppercase text-text-light">Story Character</p>
                            <input type="text" name="story_character" value="<?php echo htmlspecialchars($config['story_character']); ?>"
                                   class="form-input rounded-lg border border-primary/30 bg-black/30 p-3 text-text-light placeholder:text-text-muted focus:border-primary/50 focus:outline-0 focus:ring-0"
                                   placeholder="e.g., a female janitor who accidentally activated a wormhole">
                        </label>

                        <label class="flex flex-col">
                            <p class="pb-2 text-sm font-medium uppercase text-text-light">Story Theme</p>
                            <input type="text" name="story_theme" value="<?php echo htmlspecialchars($config['story_theme']); ?>"
                                   class="form-input rounded-lg border border-primary/30 bg-black/30 p-3 text-text-light placeholder:text-text-muted focus:border-primary/50 focus:outline-0 focus:ring-0"
                                   placeholder="e.g., space adventure with humor">
                        </label>

                        <label class="flex flex-col">
                            <p class="pb-2 text-sm font-medium uppercase text-text-light">Story Goal</p>
                            <input type="text" name="story_goal" value="<?php echo htmlspecialchars($config['story_goal']); ?>"
                                   class="form-input rounded-lg border border-primary/30 bg-black/30 p-3 text-text-light placeholder:text-text-muted focus:border-primary/50 focus:outline-0 focus:ring-0"
                                   placeholder="e.g., trying to get home">
                        </label>

                        <label class="flex flex-col">
                            <p class="pb-2 text-sm font-medium uppercase text-text-light">Character Personality</p>
                            <input type="text" name="character_personality" value="<?php echo htmlspecialchars($config['character_personality']); ?>"
                                   class="form-input rounded-lg border border-primary/30 bg-black/30 p-3 text-text-light placeholder:text-text-muted focus:border-primary/50 focus:outline-0 focus:ring-0"
                                   placeholder="e.g., sarcastic but positive">
                        </label>

                        <label class="flex flex-col">
                            <p class="pb-2 text-sm font-medium uppercase text-text-light">Story Tone</p>
                            <input type="text" name="story_tone" value="<?php echo htmlspecialchars($config['story_tone']); ?>"
                                   class="form-input rounded-lg border border-primary/30 bg-black/30 p-3 text-text-light placeholder:text-text-muted focus:border-primary/50 focus:outline-0 focus:ring-0"
                                   placeholder="e.g., Family-friendly but humorous">
                        </label>

                        <div class="flex gap-3">
                            <button type="submit" name="save_settings"
                                    class="flex-1 rounded-lg bg-primary py-3 text-base font-bold uppercase tracking-wider text-background-dark transition-transform hover:scale-[1.02]">
                                Save Settings
                            </button>
                        </div>
                    </form>

                    <!-- Test API Section (Outside Form) -->
                    <div class="mt-4 pt-4 border-t border-primary/20">
                        <button type="button" id="test-api-btn"
                                class="w-full rounded-lg bg-accent/20 border border-accent text-accent px-6 py-3 font-bold uppercase hover:bg-accent/30 transition-all">
                            🔍 Test API Connection
                        </button>
                        <div id="test-api-result" class="mt-3 text-sm"></div>
                    </div>
                </details>

                <!-- Create Day Accordion -->
                <details class="flex flex-col rounded-lg border border-primary/30 bg-background-dark/50 px-4 py-2 group" open>
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-6 py-2">
                        <p class="text-base font-bold uppercase text-primary">Create Day <?php echo $next_day; ?></p>
                        <span class="material-symbols-outlined text-primary transition-transform group-open:rotate-180">expand_more</span>
                    </summary>

                    <div class="pt-4">
                        <!-- AI Generation Section -->
                        <div class="mb-6 rounded-lg border border-accent/30 bg-accent/10 p-4">
                            <h3 class="text-lg font-bold text-accent mb-3">🤖 AI Story Generation</h3>
                            <div class="flex gap-3">
                                <button type="button" id="generate-full-day"
                                        class="flex-1 rounded-lg bg-accent py-3 text-base font-bold uppercase tracking-wider text-background-dark transition-transform hover:scale-[1.02]">
                                    Generate Complete Day
                                </button>
                                <label class="flex items-center gap-2 text-text-light">
                                    <input type="checkbox" id="generate-audio" checked class="form-checkbox rounded border-accent/50 bg-black/30 text-accent focus:ring-accent">
                                    <span class="text-sm">Audio</span>
                                </label>
                            </div>
                            <div id="ai-status" class="mt-3 text-center text-sm font-medium text-accent"></div>
                        </div>

                        <form method="POST" id="day-form">
                            <input type="hidden" name="day_number" value="<?php echo $next_day; ?>">

                            <!-- Planet Name -->
                            <div class="mb-4">
                                <label class="flex flex-col">
                                    <div class="flex items-center justify-between pb-2">
                                        <p class="text-sm font-medium uppercase text-text-light">Planet Name</p>
                                        <button type="button" class="ai-field-btn text-xs bg-accent/20 hover:bg-accent/30 text-accent px-3 py-1 rounded" data-field="planet_name">AI Fill</button>
                                    </div>
                                    <input type="text" name="planet_name" id="planet_name"
                                           class="form-input rounded-lg border border-primary/30 bg-black/30 p-3 text-text-light placeholder:text-text-muted focus:border-primary/50 focus:outline-0 focus:ring-0">
                                </label>
                            </div>

                            <?php for ($turn = 1; $turn <= 3; $turn++): ?>
                            <div class="mb-6 rounded-lg border border-primary/20 bg-black/20 p-4">
                                <h3 class="text-lg font-bold text-primary mb-4">Turn <?php echo $turn; ?></h3>

                                <div class="space-y-4">
                                    <label class="flex flex-col">
                                        <div class="flex items-center justify-between pb-2">
                                            <p class="text-sm font-medium text-text-light">Paragraph 1</p>
                                            <button type="button" class="ai-field-btn text-xs bg-accent/20 hover:bg-accent/30 text-accent px-3 py-1 rounded" data-field="turn<?php echo $turn; ?>_para1" data-turn="<?php echo $turn; ?>">AI Fill</button>
                                        </div>
                                        <textarea name="turn<?php echo $turn; ?>_para1" id="turn<?php echo $turn; ?>_para1" rows="3"
                                                  class="form-input rounded-lg border border-primary/30 bg-black/30 p-3 text-text-light placeholder:text-text-muted focus:border-primary/50 focus:outline-0 focus:ring-0"></textarea>
                                    </label>

                                    <label class="flex flex-col">
                                        <div class="flex items-center justify-between pb-2">
                                            <p class="text-sm font-medium text-text-light">Paragraph 2</p>
                                            <button type="button" class="ai-field-btn text-xs bg-accent/20 hover:bg-accent/30 text-accent px-3 py-1 rounded" data-field="turn<?php echo $turn; ?>_para2" data-turn="<?php echo $turn; ?>">AI Fill</button>
                                        </div>
                                        <textarea name="turn<?php echo $turn; ?>_para2" id="turn<?php echo $turn; ?>_para2" rows="3"
                                                  class="form-input rounded-lg border border-primary/30 bg-black/30 p-3 text-text-light placeholder:text-text-muted focus:border-primary/50 focus:outline-0 focus:ring-0"></textarea>
                                    </label>

                                    <label class="flex flex-col">
                                        <div class="flex items-center justify-between pb-2">
                                            <p class="text-sm font-medium text-text-light">Paragraph 3</p>
                                            <button type="button" class="ai-field-btn text-xs bg-accent/20 hover:bg-accent/30 text-accent px-3 py-1 rounded" data-field="turn<?php echo $turn; ?>_para3" data-turn="<?php echo $turn; ?>">AI Fill</button>
                                        </div>
                                        <textarea name="turn<?php echo $turn; ?>_para3" id="turn<?php echo $turn; ?>_para3" rows="3"
                                                  class="form-input rounded-lg border border-primary/30 bg-black/30 p-3 text-text-light placeholder:text-text-muted focus:border-primary/50 focus:outline-0 focus:ring-0"></textarea>
                                    </label>

                                    <div class="grid grid-cols-2 gap-4">
                                        <label class="flex flex-col">
                                            <div class="flex items-center justify-between pb-2">
                                                <p class="text-sm font-medium text-text-light">Choice A</p>
                                                <button type="button" class="ai-field-btn text-xs bg-accent/20 hover:bg-accent/30 text-accent px-3 py-1 rounded" data-field="turn<?php echo $turn; ?>_choice_a" data-turn="<?php echo $turn; ?>">AI</button>
                                            </div>
                                            <input type="text" name="turn<?php echo $turn; ?>_choice_a" id="turn<?php echo $turn; ?>_choice_a"
                                                   class="form-input rounded-lg border border-primary/30 bg-black/30 p-3 text-text-light placeholder:text-text-muted focus:border-primary/50 focus:outline-0 focus:ring-0">
                                        </label>

                                        <label class="flex flex-col">
                                            <div class="flex items-center justify-between pb-2">
                                                <p class="text-sm font-medium text-text-light">Choice B</p>
                                                <button type="button" class="ai-field-btn text-xs bg-accent/20 hover:bg-accent/30 text-accent px-3 py-1 rounded" data-field="turn<?php echo $turn; ?>_choice_b" data-turn="<?php echo $turn; ?>">AI</button>
                                            </div>
                                            <input type="text" name="turn<?php echo $turn; ?>_choice_b" id="turn<?php echo $turn; ?>_choice_b"
                                                   class="form-input rounded-lg border border-primary/30 bg-black/30 p-3 text-text-light placeholder:text-text-muted focus:border-primary/50 focus:outline-0 focus:ring-0">
                                        </label>
                                    </div>

                                    <label class="flex flex-col">
                                        <div class="flex items-center justify-between pb-2">
                                            <p class="text-sm font-medium text-text-light">Death Description</p>
                                            <button type="button" class="ai-field-btn text-xs bg-accent/20 hover:bg-accent/30 text-accent px-3 py-1 rounded" data-field="turn<?php echo $turn; ?>_death_desc" data-turn="<?php echo $turn; ?>">AI Fill</button>
                                        </div>
                                        <textarea name="turn<?php echo $turn; ?>_death_desc" id="turn<?php echo $turn; ?>_death_desc" rows="2"
                                                  class="form-input rounded-lg border border-primary/30 bg-black/30 p-3 text-text-light placeholder:text-text-muted focus:border-primary/50 focus:outline-0 focus:ring-0"></textarea>
                                    </label>

                                    <div class="grid grid-cols-2 gap-4">
                                        <label class="flex flex-col">
                                            <div class="flex items-center justify-between pb-2">
                                                <p class="text-sm font-medium text-text-light">Image URL</p>
                                                <button type="button" class="ai-image-btn text-xs bg-accent/20 hover:bg-accent/30 text-accent px-3 py-1 rounded" data-turn="<?php echo $turn; ?>">Generate Image</button>
                                            </div>
                                            <input type="text" name="turn<?php echo $turn; ?>_image_url" id="turn<?php echo $turn; ?>_image_url"
                                                   class="form-input rounded-lg border border-primary/30 bg-black/30 p-3 text-text-light placeholder:text-text-muted focus:border-primary/50 focus:outline-0 focus:ring-0">
                                        </label>

                                        <label class="flex flex-col">
                                            <div class="flex items-center justify-between pb-2">
                                                <p class="text-sm font-medium text-text-light">Audio URL</p>
                                                <button type="button" class="ai-audio-btn text-xs bg-accent/20 hover:bg-accent/30 text-accent px-3 py-1 rounded" data-turn="<?php echo $turn; ?>">Generate Audio</button>
                                            </div>
                                            <input type="text" name="turn<?php echo $turn; ?>_audio_url" id="turn<?php echo $turn; ?>_audio_url"
                                                   class="form-input rounded-lg border border-primary/30 bg-black/30 p-3 text-text-light placeholder:text-text-muted focus:border-primary/50 focus:outline-0 focus:ring-0">
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <?php endfor; ?>

                            <!-- Home Description -->
                            <div class="mb-6">
                                <label class="flex flex-col">
                                    <div class="flex items-center justify-between pb-2">
                                        <p class="text-sm font-medium uppercase text-text-light">Home Trip Description</p>
                                        <button type="button" class="ai-field-btn text-xs bg-accent/20 hover:bg-accent/30 text-accent px-3 py-1 rounded" data-field="home_trip_desc">AI Fill</button>
                                    </div>
                                    <textarea name="home_trip_desc" id="home_trip_desc" rows="3"
                                              class="form-input rounded-lg border border-primary/30 bg-black/30 p-3 text-text-light placeholder:text-text-muted focus:border-primary/50 focus:outline-0 focus:ring-0"></textarea>
                                </label>
                            </div>

                            <button type="submit" name="save_day"
                                    class="w-full rounded-lg bg-primary py-3 text-base font-bold uppercase tracking-wider text-background-dark transition-transform hover:scale-[1.02]">
                                💾 Save Day <?php echo $next_day; ?>
                            </button>
                        </form>
                    </div>
                </details>

            </div>
        </main>
    </div>

    <script src="js/admin-ai.js"></script>
</body>
</html>

<?php
// Function to generate day HTML file
function generate_day_html($data) {
    global $days_dir;

    $day_num = $data['day_number'];
    $template = file_get_contents(__DIR__ . '/day-template.html');

    // Replace all placeholders
    foreach ($data as $key => $value) {
        $template = str_replace("{{" . $key . "}}", htmlspecialchars($value), $template);
    }

    file_put_contents($days_dir . "/day-{$day_num}.html", $template);
}

// Function to generate index page
function generate_index_page() {
    global $days_dir;

    $files = glob($days_dir . '/day-*.html');
    $days = [];

    foreach ($files as $file) {
        preg_match('/day-(\d+)\.html$/', $file, $matches);
        if (isset($matches[1])) {
            $days[] = intval($matches[1]);
        }
    }

    sort($days);

    $index_template = file_get_contents(__DIR__ . '/index-template.html');

    // Generate day cards HTML
    $cards_html = '';
    foreach ($days as $day) {
        $cards_html .= <<<HTML
            <a href="day-{$day}.html" class="day-card block rounded-lg border border-primary/30 bg-black/20 p-6 transition-all hover:border-primary hover:scale-105">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-2xl font-bold text-primary">Day {$day}</h3>
                    <span class="material-symbols-outlined text-primary text-3xl">rocket_launch</span>
                </div>
                <p class="text-text-muted text-sm">Click to play this adventure</p>
            </a>
HTML;
    }

    $index_html = str_replace('{{day_cards}}', $cards_html, $index_template);
    file_put_contents($days_dir . '/index.html', $index_html);
}
?>
