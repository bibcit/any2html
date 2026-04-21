<?php

/**
 * Plugin Name:       Bibcit Any2HTML
 * Description:       Convert Markdown to HTML inside the WordPress post editor using the Bibcit API. Requires a Bibcit API key obtained from bibcit.com. Your post content is sent to the Bibcit external API for conversion.
 * Version:           1.0.0
 * Requires at least: 5.9
 * Requires PHP:      7.4
 * Author:            Rupesh Kumar
 * Author URI:        https://github.com/leibra
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       any2html
 */

if (! defined('ABSPATH')) exit;

define('ANY2HTML_VERSION',        '1.0.0');
define('ANY2HTML_OPTION_KEY',     'any2html_api_key');
define('ANY2HTML_OPTION_STATUS',  'any2html_api_status');
define('ANY2HTML_OPTION_ENABLED', 'any2html_enabled');
define('BIBCIT_BASE_URL', 'https://www.bibcit.com');
define('ANY2HTML_API_BASE',       'https://api.bibcit.com');
define('ANY2HTML_PRIVACY_URL',    'https://www.bibcit.com/en/privacy');
define('ANY2HTML_TERMS_URL',      'https://www.bibcit.com/en/terms');

/* ── Settings page ─────────────────────────────────────────────────────── */

add_action('admin_menu', 'any2html_add_settings_page');
function any2html_add_settings_page()
{
    add_options_page(
        esc_html__('Bibcit Any2HTML', 'any2html'),
        esc_html__('Bibcit Any2HTML', 'any2html'),
        'manage_options',
        'any2html',
        'any2html_render_settings'
    );
}

/* Remove the auto-injected WP "Settings saved." notice on our page —
   we handle saving via AJAX and show our own inline message. */
add_action('admin_head', function () {
    $screen = get_current_screen();
    if ($screen && 'settings_page_any2html' === $screen->id) {
        remove_all_actions('admin_notices');
    }
});

function any2html_render_settings()
{
    if (! current_user_can('manage_options')) return;

    $key      = get_option(ANY2HTML_OPTION_KEY, '');
    $status   = get_option(ANY2HTML_OPTION_STATUS, '');
    $enabled  = get_option(ANY2HTML_OPTION_ENABLED, '0');
    $is_valid = ('valid' === $status);
?>
    <div class="wrap any2html-wrap">

        <div class="any2html-page-header">
            <div class="any2html-logo-badge" aria-hidden="true">A2H</div>
            <div class="any2html-header-text">
                <div class="any2html-page-title">
                    <?php esc_html_e('Bibcit Any2HTML', 'any2html'); ?>
                    <span class="any2html-version-badge">v<?php echo esc_html(ANY2HTML_VERSION); ?></span>
                </div>
                <p class="any2html-page-subtitle"><?php esc_html_e('Convert Markdown to HTML inside the post editor via the Bibcit API.', 'any2html'); ?></p>
            </div>
        </div>

        <div class="any2html-notice">
            <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M18 10A8 8 0 1 1 2 10a8 8 0 0 1 16 0zm-7-4a1 1 0 1 0-2 0v4a1 1 0 0 0 2 0V6zm-1 8a1 1 0 1 0 0-2 1 1 0 0 0 0 2z" clip-rule="evenodd" />
            </svg>
            <span>
                <?php esc_html_e('Post content is transmitted to api.bibcit.com for conversion.', 'any2html'); ?>
                <a href="<?php echo esc_url(ANY2HTML_PRIVACY_URL); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Privacy Policy', 'any2html'); ?></a>
                &middot;
                <a href="<?php echo esc_url(ANY2HTML_TERMS_URL); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Terms of Service', 'any2html'); ?></a>
            </span>
        </div>

        <form id="any2html-settings-form">
            <?php wp_nonce_field('any2html_save_settings', 'any2html_save_nonce'); ?>

            <div class="any2html-card">
                <div class="any2html-card-header">
                    <h2><?php esc_html_e('API Configuration', 'any2html'); ?></h2>
                </div>
                <div class="any2html-card-body">

                    <div class="any2html-field">
                        <label class="any2html-label" for="any2html_api_key">
                            <?php esc_html_e('API Key', 'any2html'); ?>
                        </label>
                        <p class="any2html-description">
                            <?php esc_html_e('Enter your Bibcit API key. Click Validate to verify it before saving.', 'any2html'); ?>
                            <a href="https://www.bibcit.com/en" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Get a key →', 'any2html'); ?></a>
                        </p>
                        <div class="any2html-input-row">
                            <div class="any2html-input-wrap">
                                <input type="password"
                                    id="any2html_api_key"
                                    name="<?php echo esc_attr(ANY2HTML_OPTION_KEY); ?>"
                                    value="<?php echo esc_attr($key); ?>"
                                    class="any2html-input"
                                    autocomplete="off"
                                    placeholder="Enter your api key here" />
                                <button type="button" class="any2html-eye" id="any2html-toggle-eye" aria-label="<?php esc_attr_e('Show/hide key', 'any2html'); ?>">
                                    <!-- eye: shown when input is password -->
                                    <svg id="any2html-icon-show" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                    <!-- eye-off: shown when input is text -->
                                    <svg id="any2html-icon-hide" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="display:none">
                                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94" />
                                        <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19" />
                                        <line x1="1" y1="1" x2="23" y2="23" />
                                    </svg>
                                </button>
                            </div>
                            <button type="button" id="any2html-validate-key" class="any2html-btn any2html-btn-outline">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <polyline points="20 6 9 17 4 12" />
                                </svg>
                                <?php esc_html_e('Validate Key', 'any2html'); ?>
                            </button>
                        </div>
                        <div id="any2html-key-status-wrap">
                            <?php if ($is_valid) : ?>
                                <span class="any2html-status-pill any2html-status-valid">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <polyline points="20 6 9 17 4 12" />
                                    </svg>
                                    <?php esc_html_e('Key validated', 'any2html'); ?>
                                </span>
                            <?php elseif ('invalid' === $status) : ?>
                                <span class="any2html-status-pill any2html-status-invalid">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <line x1="18" y1="6" x2="6" y2="18" />
                                        <line x1="6" y1="6" x2="18" y2="18" />
                                    </svg>
                                    <?php esc_html_e('Invalid key', 'any2html'); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <input type="hidden" id="any2html_api_status"
                            name="<?php echo esc_attr(ANY2HTML_OPTION_STATUS); ?>"
                            value="<?php echo esc_attr($status); ?>" />
                    </div>

                </div>
            </div>

            <div class="any2html-card">
                <div class="any2html-card-header">
                    <h2><?php esc_html_e('Plugin Status', 'any2html'); ?></h2>
                </div>
                <div class="any2html-card-body">

                    <div class="any2html-field any2html-toggle-field">
                        <div class="any2html-toggle-info">
                            <span class="any2html-label"><?php esc_html_e('Enable Markdown Conversion', 'any2html'); ?></span>
                            <p class="any2html-description">
                                <?php if ($is_valid) {
                                    esc_html_e('Activates the Markdown panel in the post editor.', 'any2html');
                                } else {
                                    esc_html_e('Validate your API key above before enabling the plugin.', 'any2html');
                                } ?>
                            </p>
                        </div>
                        <label class="any2html-toggle <?php echo $is_valid ? '' : 'any2html-toggle-locked'; ?>" id="any2html-toggle-label">
                            <input type="checkbox"
                                id="any2html_enabled"
                                name="<?php echo esc_attr(ANY2HTML_OPTION_ENABLED); ?>"
                                value="1"
                                <?php checked('1', $enabled); ?>
                                <?php disabled(false, $is_valid); ?> />
                            <span class="any2html-slider"></span>
                        </label>
                    </div>

                    <?php if (! $is_valid) : ?>
                        <div class="any2html-lock-notice">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                            <?php esc_html_e('A valid API key is required to enable this feature.', 'any2html'); ?>
                        </div>
                    <?php endif; ?>

                </div>
            </div>

            <div class="any2html-form-footer" id="any2html-form-footer" <?php if (! $is_valid) echo 'style="display:none"'; ?>>
                <button type="button" id="any2html-save-btn" class="any2html-btn any2html-btn-primary any2html-save-btn">
                    <?php esc_html_e('Save Settings', 'any2html'); ?>
                </button>
                <span id="any2html-save-status"></span>
            </div>

        </form>
    </div><!-- /.any2html-wrap -->
<?php
}

add_action('admin_init', 'any2html_register_settings');
function any2html_register_settings()
{
    register_setting('any2html_settings', ANY2HTML_OPTION_KEY,     ['sanitize_callback' => 'sanitize_text_field']);
    register_setting('any2html_settings', ANY2HTML_OPTION_STATUS,  ['sanitize_callback' => 'sanitize_text_field']);
    register_setting('any2html_settings', ANY2HTML_OPTION_ENABLED, ['sanitize_callback' => 'sanitize_text_field']);
}

/* ── AJAX: save settings ───────────────────────────────────────────────── */

add_action('wp_ajax_any2html_save_settings', 'any2html_ajax_save_settings');
function any2html_ajax_save_settings()
{
    check_ajax_referer('any2html_save_settings', 'nonce');
    if (! current_user_can('manage_options')) wp_send_json_error(null, 403);

    update_option(ANY2HTML_OPTION_KEY,     sanitize_text_field(wp_unslash($_POST['api_key']  ?? '')), false);
    update_option(ANY2HTML_OPTION_STATUS,  sanitize_text_field(wp_unslash($_POST['api_status'] ?? '')), false);
    update_option(ANY2HTML_OPTION_ENABLED, sanitize_text_field(wp_unslash($_POST['enabled']  ?? '0')), false);

    wp_send_json_success();
}

/* ── AJAX: validate key ────────────────────────────────────────────────── */

add_action('wp_ajax_any2html_validate_key', 'any2html_ajax_validate_key');
function any2html_ajax_validate_key()
{
    check_ajax_referer('any2html_validate');
    if (! current_user_can('manage_options')) wp_send_json_error(null, 403);

    $key      = sanitize_text_field(wp_unslash($_POST['api_key'] ?? ''));
    $response = wp_remote_get(ANY2HTML_API_BASE . '/validator/ckeyValidate?ckey=' . $key, [
        'timeout' => 10,
    ]);

    $valid = ! is_wp_error($response) && 200 === wp_remote_retrieve_response_code($response);
    update_option(ANY2HTML_OPTION_STATUS, $valid ? 'valid' : 'invalid', false);
    wp_send_json_success(['valid' => json_decode(wp_remote_retrieve_body($response), true)]);
}

/* ── AJAX: convert markdown ────────────────────────────────────────────── */

add_action('wp_ajax_any2html_convert', 'any2html_ajax_convert');
function any2html_ajax_convert()
{
    check_ajax_referer('any2html_convert');
    if (! current_user_can('edit_posts')) wp_send_json_error(['message' => 'Unauthorized'], 403);

    if ('valid' !== get_option(ANY2HTML_OPTION_STATUS) || '1' !== get_option(ANY2HTML_OPTION_ENABLED)) {
        wp_send_json_error(['message' => 'Plugin is disabled or API key is invalid.']);
    }

    $markdown = sanitize_textarea_field(wp_unslash($_POST['markdown'] ?? ''));
    $key      = get_option(ANY2HTML_OPTION_KEY, '');

    $response = wp_remote_post(ANY2HTML_API_BASE . '/api/massivemark/mtoh', [
        'headers' => [
            'Bibcit-Key'   => $key,
            'Content-Type' => 'text/plain;charset=UTF-8',
        ],
        'body'    => $markdown,
        'timeout' => 15,
    ]);

    if (is_wp_error($response)) {
        wp_send_json_error(['message' => $response->get_error_message()]);
    }

    $code = wp_remote_retrieve_response_code($response);
    $body = json_decode(wp_remote_retrieve_body($response), true);

    if (in_array($code, [401, 403], true)) {
        update_option(ANY2HTML_OPTION_STATUS, 'invalid', false);
        wp_send_json_error([
            'message'      => 'API key is unauthorized. Please re-validate your key.',
            'key_invalid'  => true,
            'settings_url' => admin_url('options-general.php?page=any2html'),
        ]);
    }

    if (200 !== $code || empty($body['htmlContent'])) {
        wp_send_json_error(['message' => $body['message'] ?? 'Conversion failed.']);
    }

    wp_send_json_success(['html' => $body['htmlContent']]);
}

/* ── Post editor meta box ──────────────────────────────────────────────── */

add_action('add_meta_boxes', 'any2html_add_meta_box');
function any2html_add_meta_box()
{
    if ('valid' !== get_option(ANY2HTML_OPTION_STATUS) || '1' !== get_option(ANY2HTML_OPTION_ENABLED)) return;

    foreach (get_post_types() as $post_type) {
        if (post_type_supports($post_type, 'editor')) {
            add_meta_box(
                'any2html_box',
                esc_html__('Bibcit Any2HTML — Markdown Converter', 'any2html'),
                'any2html_render_meta_box',
                $post_type
            );
        }
    }
}

function any2html_render_meta_box()
{
?>
    <div id="any2html-panel">
        <div class="any2html-md-toolbar">
            <span class="any2html-md-label">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                    <polyline points="14 2 14 8 20 8" />
                    <line x1="16" y1="13" x2="8" y2="13" />
                    <line x1="16" y1="17" x2="8" y2="17" />
                    <polyline points="10 9 9 9 8 9" />
                </svg>
                <?php esc_html_e('Markdown Input', 'any2html'); ?>
            </span>
            <button type="button" class="any2html-md-clear" id="any2html-md-clear" title="<?php esc_attr_e('Clear', 'any2html'); ?>">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="18" y1="6" x2="6" y2="18" />
                    <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
                <?php esc_html_e('Clear', 'any2html'); ?>
            </button>
        </div>
        <textarea id="any2html-input"
            placeholder="<?php esc_attr_e('# Heading\n\nPaste your **Markdown** here…', 'any2html'); ?>"
            rows="10"
            spellcheck="false"></textarea>
        <div class="any2html-md-footer">
            <button type="button" id="any2html-convert" class="any2html-btn any2html-btn-primary">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <polyline points="5 12 12 5 19 12" />
                    <polyline points="5 19 12 12 19 19" />
                </svg>
                <?php esc_html_e('Convert to HTML', 'any2html'); ?>
            </button>
            <span id="any2html-status"></span>
        </div>
    </div>
<?php
}

/* ── Enqueue assets ────────────────────────────────────────────────────── */

add_action('admin_enqueue_scripts', 'any2html_enqueue');
function any2html_enqueue($hook)
{
    $base = plugin_dir_url(__FILE__);

    wp_enqueue_style('any2html-style', $base . 'any2html.css', [], ANY2HTML_VERSION);

    if ('settings_page_any2html' === $hook) {
        wp_enqueue_script('any2html-settings', $base . 'any2html-settings.js', ['jquery'], ANY2HTML_VERSION, true);
        wp_localize_script('any2html-settings', 'any2htmlSettings', [
            'ajaxUrl'    => admin_url('admin-ajax.php'),
            'nonce'      => wp_create_nonce('any2html_validate'),
            'saveNonce'  => wp_create_nonce('any2html_save_settings'),
        ]);
    }

    if (in_array($hook, ['post.php', 'post-new.php'], true)) {
        wp_enqueue_script('any2html-editor', $base . 'any2html-editor.js', ['jquery'], ANY2HTML_VERSION, true);
        wp_localize_script('any2html-editor', 'any2htmlEditor', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('any2html_convert'),
        ]);
    }
}
