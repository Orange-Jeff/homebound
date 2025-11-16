<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>nb-HomeBound - Installation Check</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #221910 0%, #1a1410 100%);
            color: #F0EAD6;
            padding: 40px 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: rgba(0,0,0,0.3);
            border: 1px solid rgba(232, 119, 46, 0.3);
            border-radius: 12px;
            padding: 40px;
        }
        h1 {
            color: #E8772E;
            margin-bottom: 10px;
            font-size: 2.5em;
        }
        h2 {
            color: #E8772E;
            margin: 30px 0 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid rgba(232, 119, 46, 0.3);
        }
        .status {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px;
            margin: 10px 0;
            background: rgba(0,0,0,0.2);
            border-left: 4px solid #666;
            border-radius: 4px;
        }
        .status.pass {
            border-left-color: #4CAF50;
            background: rgba(76, 175, 80, 0.1);
        }
        .status.fail {
            border-left-color: #f44336;
            background: rgba(244, 67, 54, 0.1);
        }
        .status.warn {
            border-left-color: #ff9800;
            background: rgba(255, 152, 0, 0.1);
        }
        .icon {
            font-size: 20px;
            font-weight: bold;
        }
        .links {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 20px;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #E8772E;
            color: #221910;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            transition: transform 0.2s;
        }
        .btn:hover {
            transform: scale(1.05);
        }
        .btn.secondary {
            background: rgba(232, 119, 46, 0.2);
            color: #E8772E;
            border: 1px solid #E8772E;
        }
        code {
            background: rgba(0,0,0,0.4);
            padding: 2px 6px;
            border-radius: 3px;
            color: #00A9A5;
            font-size: 0.9em;
        }
        .info-box {
            background: rgba(0, 169, 165, 0.1);
            border: 1px solid rgba(0, 169, 165, 0.3);
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }
        ul { margin: 10px 0 10px 30px; }
        li { margin: 8px 0; line-height: 1.6; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 nb-HomeBound</h1>
        <p style="color: #a09c90; margin-bottom: 30px;">AI-Powered Daily Story Adventure Generator</p>

        <h2>Installation Check</h2>

        <?php
        // Check PHP version
        $php_version = phpversion();
        $php_ok = version_compare($php_version, '7.4.0', '>=');
        ?>
        <div class="status <?php echo $php_ok ? 'pass' : 'fail'; ?>">
            <span class="icon"><?php echo $php_ok ? '✓' : '✗'; ?></span>
            <div>
                <strong>PHP Version:</strong> <?php echo $php_version; ?>
                <?php if (!$php_ok): ?>
                    <br><small>⚠️ Requires PHP 7.4 or higher</small>
                <?php endif; ?>
            </div>
        </div>

        <?php
        // Check cURL
        $curl_ok = function_exists('curl_version');
        ?>
        <div class="status <?php echo $curl_ok ? 'pass' : 'fail'; ?>">
            <span class="icon"><?php echo $curl_ok ? '✓' : '✗'; ?></span>
            <div>
                <strong>cURL Extension:</strong> <?php echo $curl_ok ? 'Enabled' : 'Disabled'; ?>
                <?php if (!$curl_ok): ?>
                    <br><small>⚠️ Required for AI generation</small>
                <?php endif; ?>
            </div>
        </div>

        <?php
        // Check directories
        $generated_dir = __DIR__ . '/generated-days';
        $assets_dir = __DIR__ . '/generated-days/assets';
        $dirs_ok = is_dir($generated_dir) && is_writable($generated_dir);
        ?>
        <div class="status <?php echo $dirs_ok ? 'pass' : 'warn'; ?>">
            <span class="icon"><?php echo $dirs_ok ? '✓' : '!'; ?></span>
            <div>
                <strong>Generated Days Directory:</strong> <?php echo $dirs_ok ? 'Writable' : 'Not writable'; ?>
                <?php if (!$dirs_ok): ?>
                    <br><small>⚠️ Run: chmod 755 generated-days</small>
                <?php endif; ?>
            </div>
        </div>

        <?php
        // Check config
        $config_file = __DIR__ . '/config.json';
        $config_exists = file_exists($config_file);
        $config = $config_exists ? json_decode(file_get_contents($config_file), true) : [];
        $api_key_set = !empty($config['api_key']);
        ?>
        <div class="status <?php echo $api_key_set ? 'pass' : 'warn'; ?>">
            <span class="icon"><?php echo $api_key_set ? '✓' : '!'; ?></span>
            <div>
                <strong>API Key:</strong> <?php echo $api_key_set ? 'Configured' : 'Not configured'; ?>
                <?php if (!$api_key_set): ?>
                    <br><small>⚠️ Configure in admin.php → Story Settings</small>
                <?php endif; ?>
            </div>
        </div>

        <?php
        // Check if any days exist
        $day_files = glob($generated_dir . '/day-*.html');
        $days_exist = !empty($day_files);
        ?>
        <div class="status <?php echo $days_exist ? 'pass' : 'warn'; ?>">
            <span class="icon"><?php echo $days_exist ? '✓' : '!'; ?></span>
            <div>
                <strong>Generated Adventures:</strong> <?php echo $days_exist ? count($day_files) . ' days' : 'None yet'; ?>
                <?php if (!$days_exist): ?>
                    <br><small>ℹ️ Create your first day in admin.php</small>
                <?php endif; ?>
            </div>
        </div>

        <h2>Quick Start</h2>

        <div class="info-box">
            <strong style="color: #00A9A5;">First Time Setup:</strong>
            <ol style="margin-top: 10px;">
                <li>Get free API key: <a href="https://aistudio.google.com/app/apikey" target="_blank" style="color: #E8772E;">aistudio.google.com/app/apikey</a></li>
                <li>Open <strong>admin.php</strong> below</li>
                <li>Click "Story Settings" → Paste API key → Save</li>
                <li>Click "Generate Complete Day" button</li>
                <li>Click "Save Day" when done</li>
                <li>View your calendar!</li>
            </ol>
        </div>

        <div class="links">
            <a href="admin.php" class="btn">📝 Open Admin Console</a>
            <a href="generated-days/index.html" class="btn secondary">📅 View Calendar</a>
        </div>

        <h2>Features</h2>
        <ul>
            <li><strong>Customizable Story Themes</strong> - Not locked into janitor storyline!</li>
            <li><strong>AI Text Generation</strong> - Gemini 2.5 Pro creates engaging stories</li>
            <li><strong>AI Image Generation</strong> - Nano Banana creates sci-fi scenes</li>
            <li><strong>AI Audio Narration</strong> - Google TTS voices your stories</li>
            <li><strong>No Database</strong> - Static HTML pages, easy to share</li>
            <li><strong>Cookie Progress</strong> - Players' last day automatically tracked</li>
            <li><strong>WordPress Ready</strong> - Use <code>[homebound_calendar]</code> shortcode</li>
        </ul>

        <h2>Story Customization</h2>
        <p>In <strong>admin.php → Story Settings</strong>, you can customize:</p>
        <ul>
            <li><strong>Story Character</strong> - Change from janitor to wizard, detective, etc.</li>
            <li><strong>Story Theme</strong> - Fantasy quest, zombie survival, time travel, etc.</li>
            <li><strong>Story Goal</strong> - What is the character trying to accomplish?</li>
            <li><strong>Character Personality</strong> - Sarcastic, brave, nervous, etc.</li>
            <li><strong>Story Tone</strong> - Family-friendly, dark comedy, serious mystery, etc.</li>
        </ul>
        <p style="margin-top: 10px; color: #00A9A5;">The 3-turn gameplay structure stays the same, but the story theme is completely yours!</p>

        <h2>WordPress Shortcodes</h2>
        <ul>
            <li><code>[homebound_calendar]</code> - Display full mission calendar</li>
            <li><code>[homebound_day number="1"]</code> - Display specific day</li>
        </ul>

        <h2>Documentation</h2>
        <ul>
            <li><a href="README.md" style="color: #E8772E;">README.md</a> - Full documentation</li>
            <li><a href="QUICKSTART.md" style="color: #E8772E;">QUICKSTART.md</a> - 5-minute setup guide</li>
        </ul>

        <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid rgba(232, 119, 46, 0.3); text-align: center; color: #a09c90;">
            <p>nb-HomeBound v1.0.0 | Created by Orange Jeff | MIT License</p>
            <p style="margin-top: 10px;">Ready for distribution &amp; customization! 🚀</p>
        </div>
    </div>
</body>
</html>
