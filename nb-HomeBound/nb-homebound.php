<?php
/**
 * Plugin Name: nb-HomeBound
 * Plugin URI: https://github.com/orangejeff/nb-homebound
 * Description: Create daily turn-based story adventures with AI-generated content. Simple standalone story generator with customizable themes.
 * Version: 1.0.0
 * Author: Orange Jeff
 * Author URI: https://orangejeff.com
 * License: MIT
 * Text Domain: nb-homebound
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Plugin constants
define('NB_HOMEBOUND_VERSION', '1.0.0');
define('NB_HOMEBOUND_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('NB_HOMEBOUND_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Shortcode to display HomeBound calendar
 * Usage: [homebound_calendar]
 */
function nb_homebound_calendar_shortcode($atts) {
    $atts = shortcode_atts(array(
        'path' => 'generated-days'
    ), $atts);

    $days_dir = NB_HOMEBOUND_PLUGIN_DIR . $atts['path'];
    $days_url = NB_HOMEBOUND_PLUGIN_URL . $atts['path'];

    if (!is_dir($days_dir)) {
        return '<p>No adventures available yet. Check back soon!</p>';
    }

    // Get index.html path
    $index_file = $days_dir . '/index.html';

    if (file_exists($index_file)) {
        // Serve the index page
        return '<iframe src="' . esc_url($days_url . '/index.html') . '" width="100%" height="800px" frameborder="0" style="border: none; border-radius: 8px;"></iframe>';
    }

    return '<p>HomeBound adventures are being prepared. Check back soon!</p>';
}
add_shortcode('homebound_calendar', 'nb_homebound_calendar_shortcode');

/**
 * Shortcode to display specific day
 * Usage: [homebound_day number="1"]
 */
function nb_homebound_day_shortcode($atts) {
    $atts = shortcode_atts(array(
        'number' => '1',
        'path' => 'generated-days'
    ), $atts);

    $day_file = NB_HOMEBOUND_PLUGIN_DIR . $atts['path'] . '/day-' . intval($atts['number']) . '.html';
    $day_url = NB_HOMEBOUND_PLUGIN_URL . $atts['path'] . '/day-' . intval($atts['number']) . '.html';

    if (file_exists($day_file)) {
        return '<iframe src="' . esc_url($day_url) . '" width="100%" height="800px" frameborder="0" style="border: none; border-radius: 8px;"></iframe>';
    }

    return '<p>This adventure is not available yet.</p>';
}
add_shortcode('homebound_day', 'nb_homebound_day_shortcode');

/**
 * Admin menu
 */
function nb_homebound_admin_menu() {
    add_menu_page(
        'HomeBound Admin',
        'HomeBound',
        'manage_options',
        'nb-homebound-admin',
        'nb_homebound_admin_page',
        'dashicons-games',
        30
    );
}
add_action('admin_menu', 'nb_homebound_admin_menu');

/**
 * Admin page - redirects to admin.php
 */
function nb_homebound_admin_page() {
    $admin_url = NB_HOMEBOUND_PLUGIN_URL . 'admin.php';
    ?>
    <div class="wrap">
        <h1>HomeBound Admin Console</h1>
        <p>Use the admin tool to create new story days:</p>
        <p><a href="<?php echo esc_url($admin_url); ?>" target="_blank" class="button button-primary button-large">Open Admin Console</a></p>

        <hr>

        <h2>Shortcodes</h2>
        <p>Use these shortcodes in your posts/pages:</p>
        <ul style="list-style: disc; margin-left: 2em;">
            <li><code>[homebound_calendar]</code> - Display the full mission calendar</li>
            <li><code>[homebound_day number="1"]</code> - Display a specific day's adventure</li>
        </ul>

        <hr>

        <h2>Quick Links</h2>
        <ul style="list-style: disc; margin-left: 2em;">
            <li><a href="<?php echo esc_url($admin_url); ?>" target="_blank">Admin Console (Create Days)</a></li>
            <li><a href="<?php echo esc_url(NB_HOMEBOUND_PLUGIN_URL . 'generated-days/index.html'); ?>" target="_blank">View Calendar</a></li>
        </ul>
    </div>
    <?php
}

/**
 * Activation hook
 */
function nb_homebound_activate() {
    // Create directories if they don't exist
    $dirs = [
        NB_HOMEBOUND_PLUGIN_DIR . 'generated-days',
        NB_HOMEBOUND_PLUGIN_DIR . 'generated-days/assets'
    ];

    foreach ($dirs as $dir) {
        if (!file_exists($dir)) {
            wp_mkdir_p($dir);
        }
    }

    // Create default config if it doesn't exist
    $config_file = NB_HOMEBOUND_PLUGIN_DIR . 'config.json';
    if (!file_exists($config_file)) {
        $default_config = [
            'api_key' => '',
            'story_character' => 'a female janitor who accidentally activated a wormhole switch',
            'story_theme' => 'space adventure with humor and sarcasm',
            'story_goal' => 'trying to get home before her cat starves',
            'story_tone' => 'Family-friendly but humorous and wild',
            'character_personality' => 'sarcastic but positive, worried about getting in trouble'
        ];
        file_put_contents($config_file, json_encode($default_config, JSON_PRETTY_PRINT));
    }
}
register_activation_hook(__FILE__, 'nb_homebound_activate');
?>
