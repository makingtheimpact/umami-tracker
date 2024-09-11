<?php
/**
 * Plugin Name: Umami Analytics Plugin
 * Plugin URI: https://makingtheimpact.com/umami-analytics-plugin
 * Description: Easily integrate Umami Analytics with your WordPress site. Umami is a simple, fast, privacy-focused alternative to Google Analytics.
 * Version: 1.0.23
 * Author: Making The Impact LLC
 * Author URI: https://makingtheimpact.com
 * License: GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain: umami-analytics
 * Domain Path: /languages
 */

/**
 * Add Umami tracking code to the site.
 *
 * This function adds the Umami tracking code to the site's header
 * for non-logged-in users and non-administrators.
 *
 * @since 1.0.0
 */
function umami_add_tracking_code() {
    if (!is_user_logged_in() && !current_user_can('manage_options')) {
        $website_id = get_option('umami_website_id');
        $analytics_url = get_option('umami_analytics_url');
        
        if (empty($analytics_url)) {
            $analytics_url = 'https://tracktheimpact.net/umami.js';
        } else {
            $analytics_url = trailingslashit($analytics_url) . 'umami.js';
        }

        if (!empty($website_id) && !empty($analytics_url)) {
            $tracking_code = '<script async defer data-website-id="' . esc_attr($website_id) . '" src="' . esc_url($analytics_url) . '"></script>';
            echo $tracking_code;
        }
    }
}
add_action('wp_head', 'umami_add_tracking_code');

/**
 * Add plugin settings page to WordPress admin.
 *
 * This function adds a new submenu page under the Settings menu
 * for configuring the Umami Analytics plugin.
 *
 * @since 1.0.0
 */
function umami_add_settings_page() {
    // Add menu item under Settings
    add_options_page('Umami Tracking Plugin Settings', 'Umami Tracking', 'manage_options', 'umami-tracking-settings', 'umami_render_settings_page');
}
add_action('admin_menu', 'umami_add_settings_page');

/**
 * Render the plugin settings page.
 *
 * This function outputs the HTML for the plugin's settings page,
 * including form fields for the Website ID and Analytics URL.
 *
 * @since 1.0.0
 */
function umami_render_settings_page() {
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.', 'umami-analytics'));
    }
    
    // Check if form is submitted and verify nonce
    if (isset($_POST['submit']) && check_admin_referer('umami_settings_action', 'umami_settings_nonce')) {
        // Process form submission here
        $website_id = sanitize_text_field($_POST['umami_website_id']);
        $analytics_url = esc_url_raw($_POST['umami_analytics_url']);
        
        update_option('umami_website_id', $website_id);
        update_option('umami_analytics_url', $analytics_url);
        
        echo '<div class="updated"><p>' . __('Settings saved.', 'umami-analytics') . '</p></div>';
    }
    
    // Get website ID and analytics URL from plugin settings
    $website_id = get_option('umami_website_id');
    $analytics_url = get_option('umami_analytics_url');
    
    if (!umami_is_configured()) {
        echo '<div class="notice notice-warning"><p>' . __('Umami Analytics is not fully configured. Please enter your Website ID and Analytics URL below.', 'umami-analytics') . '</p></div>';
    }
    
    // Output settings page HTML
    ?>
    <div class="wrap">
        <h1><?php _e('Umami Tracking Plugin Settings', 'umami-analytics'); ?></h1>
        <form method="post" action="">
            <?php wp_nonce_field('umami_settings_action', 'umami_settings_nonce'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><label for="umami_website_id"><?php _e('Website ID:', 'umami-analytics'); ?></label></th>
                    <td><input type="text" name="umami_website_id" id="umami_website_id" value="<?php echo esc_attr($website_id); ?>" placeholder="df373ef6-0873-3851-7b04-cfc23410f0e8" style="min-width: 300px" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="umami_analytics_url"><?php _e('Analytics URL:', 'umami-analytics'); ?></label></th>
                    <td><input type="text" name="umami_analytics_url" id="umami_analytics_url" value="<?php echo esc_attr($analytics_url); ?>" placeholder="https://yourtrackingsite.com" style="min-width: 300px" /></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
        
        <h2><?php _e('Access Your Analytics', 'umami-analytics'); ?></h2>
        <p><?php _e('Click the button below to open your Umami Analytics dashboard in a new tab:', 'umami-analytics'); ?></p>
        <p><a href="<?php echo esc_url($analytics_url); ?>" target="_blank" class="button button-primary"><?php _e('Open Umami Dashboard', 'umami-analytics'); ?></a></p>
        
        <h2><?php _e('Privacy Policy Content', 'umami-analytics'); ?></h2>
        <p><?php _e('You can copy the text below to add information about Umami Analytics to your privacy policy:', 'umami-analytics'); ?></p>
        <textarea id="umami-privacy-policy-text" rows="6" style="width: 100%;" readonly>
<?php echo esc_textarea(umami_get_privacy_policy_text()); ?>
        </textarea>
        <button id="umami-copy-privacy-policy" class="button"><?php _e('Copy to Clipboard', 'umami-analytics'); ?></button>
        <script>
        jQuery(document).ready(function($) {
            $('#umami-copy-privacy-policy').click(function() {
                $('#umami-privacy-policy-text').select();
                document.execCommand('copy');
                $(this).text('<?php _e('Copied!', 'umami-analytics'); ?>');
                setTimeout(() => {
                    $(this).text('<?php _e('Copy to Clipboard', 'umami-analytics'); ?>');
                }, 2000);
            });
        });
        </script>
        
        <p><?php _e('Thanks for using my free plugin! For custom designed WordPress sites, plugins, graphic design, tech support, and more, check out', 'umami-analytics'); ?> <a href="https://makingtheimpact.com"><?php _e('Making The Impact LLC!', 'umami-analytics'); ?></a></p>
    </div>
    <?php
}

/**
 * Get the privacy policy text for Umami Analytics.
 *
 * This function returns a string containing privacy policy information
 * related to the use of Umami Analytics on the site.
 *
 * @since 1.0.21
 *
 * @return string The privacy policy text.
 */
function umami_get_privacy_policy_text() {
    $content = __('This site uses Umami Analytics to track visitor information. Umami is privacy-focused and does not use cookies or collect personal data.', 'umami-analytics') . "\n\n";
    $content .= __('Umami Analytics does not track or store any personal information about visitors. It only collects anonymous usage data such as page views, referrers, and browser information.', 'umami-analytics');
    return $content;
}

/**
 * Add a menu item for viewing Umami Analytics.
 *
 * This function adds a new menu item under the "Analytics" menu
 * for viewing Umami Analytics data.
 *
 * @since 1.0.21
 */
function umami_add_admin_menu() {
    add_menu_page('View Analytics', 'View Analytics', 'manage_options', 'umami-analytics', 'umami_render_analytics_page', 'dashicons-chart-area', 2);
}
add_action('admin_menu', 'umami_add_admin_menu');

/**
 * Render the Umami Analytics page.
 *
 * This function outputs the HTML for the Umami Analytics page,
 * which displays the Umami Analytics dashboard URL and provides
 * some tips for using Umami Analytics.
 *
 * @since 1.0.21
 */
function umami_render_analytics_page() {
    // Check if user is allowed to manage options
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.', 'umami-analytics'));
    }
    // Get analytics URL from plugin settings
    $analytics_url = esc_url_raw(get_option('umami_analytics_url'));
    // Output analytics page HTML
    ?>
    <div class="wrap">
        <h1><?php _e('Umami Analytics Dashboard', 'umami-analytics'); ?></h1>
        <p><?php _e('Due to security restrictions, we cannot embed the Umami Analytics dashboard directly within WordPress. However, you can easily access your dashboard by clicking the button below:', 'umami-analytics'); ?></p>
        <p><a href="<?php echo esc_url($analytics_url); ?>" target="_blank" class="button button-primary button-large"><?php _e('Open Umami Dashboard', 'umami-analytics'); ?></a></p>
        <h2><?php _e('Tips for Using Umami Analytics', 'umami-analytics'); ?></h2>
        <ul>
            <li><?php _e('Ensure you\'re logged in to your Umami account to view your analytics data.', 'umami-analytics'); ?></li>
            <li><?php _e('The dashboard provides real-time visitor data, page views, and other valuable insights.', 'umami-analytics'); ?></li>
            <li><?php _e('You can customize your dashboard view and create custom reports within the Umami interface.', 'umami-analytics'); ?></li>
        </ul>
        <p><?php _e('If you encounter any issues accessing your dashboard, please verify that your Website ID and Analytics URL are correctly set in the', 'umami-analytics'); ?> <a href="<?php echo admin_url('options-general.php?page=umami-tracking-settings'); ?>"><?php _e('plugin settings', 'umami-analytics'); ?></a>.</p>
        <p><?php _e('Thanks for using my free plugin! For custom designed WordPress sites, plugins, graphic design, tech support, and more, check out', 'umami-analytics'); ?> <a href="https://makingtheimpact.com"><?php _e('Making The Impact LLC!', 'umami-analytics'); ?></a></p>
    </div>
    <?php
}

/**
 * Load the plugin's textdomain for internationalization.
 *
 * This function loads the plugin's translated strings.
 *
 * @since 1.0.21
 */
function umami_load_textdomain() {
    load_plugin_textdomain('umami-analytics', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}
add_action('plugins_loaded', 'umami_load_textdomain');

/**
 * Add a settings link to the plugin's action links on the Plugins page.
 *
 * This function adds a "Settings" link to the plugin's action links,
 * allowing quick access to the plugin's settings page.
 *
 * @since 1.0.21
 *
 * @param array $links An array of plugin action links.
 * @return array Modified array of plugin action links.
 */
function umami_add_settings_link($links) {
    $settings_link = '<a href="options-general.php?page=umami-tracking-settings">' . __('Settings', 'umami-analytics') . '</a>';
    array_push($links, $settings_link);
    return $links;
}
$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'umami_add_settings_link');

/**
 * Add privacy policy content for Umami Analytics.
 *
 * This function adds privacy policy content to the site's privacy policy page.
 *
 * @since 1.0.21
 */
function umami_privacy_policy_content() {
    if (!function_exists('wp_add_privacy_policy_content')) {
        return;
    }

    $content = '<p>' . umami_get_privacy_policy_text() . '</p>';
    wp_add_privacy_policy_content(
        'Umami Analytics',
        wp_kses_post($content)
    );
}
add_action('admin_init', 'umami_privacy_policy_content');

/**
 * Add a dashboard widget for Umami Analytics.
 *
 * This function registers a new dashboard widget that provides
 * quick access to the Umami Analytics dashboard.
 *
 * @since 1.0.22
 */
function umami_add_dashboard_widget() {
    wp_add_dashboard_widget(
        'umami_dashboard_widget',
        'Umami Analytics Overview',
        'umami_dashboard_widget_content'
    );
}
add_action('wp_dashboard_setup', 'umami_add_dashboard_widget');

/**
 * Render the content for the Umami Analytics dashboard widget.
 *
 * This function outputs the HTML content for the dashboard widget,
 * including a link to open the Umami Analytics dashboard.
 *
 * @since 1.0.22
 */
function umami_dashboard_widget_content() {
    if (!umami_is_configured()) {
        echo '<p>' . __('Umami Analytics is not fully configured. Please set it up in the', 'umami-analytics') . ' <a href="' . admin_url('options-general.php?page=umami-tracking-settings') . '">' . __('settings page', 'umami-analytics') . '</a>.</p>';
    } else {
        $analytics_url = get_option('umami_analytics_url');
        echo '<p>' . __('Quick access to your Umami Analytics dashboard:', 'umami-analytics') . '</p>';
        echo '<a href="' . esc_url($analytics_url) . '" target="_blank" class="button button-primary">' . __('Open Umami Dashboard', 'umami-analytics') . '</a>';
    }
}

/**
 * Check if Umami Analytics settings are configured.
 *
 * @return bool True if both website ID and analytics URL are set, false otherwise.
 */
function umami_is_configured() {
    $website_id = get_option('umami_website_id');
    $analytics_url = get_option('umami_analytics_url');
    return !empty($website_id) && !empty($analytics_url);
}
