<?php

/**
 * @wordpress-plugin
 * Plugin Name:       Molin AI
 * Plugin URI:        https://blog.molin.ai/how-to-embed-molin-into-your-woocommerce-shop/
 * Description:       Reduce your support by 70% with AI
 * Version:           1.0.0
 * Author:            Molin AI Ltd
 * Author URI:        https://molin.ai/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       molin-ai
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

register_activation_hook(__FILE__, function () {
    // no-op for now
});

register_deactivation_hook(__FILE__, function () {
    // no-op for now
});

add_action('wp_head', function () {
    // option keys must be prefixed with the plugin name
    $widgetId = get_option('molinai_widget_id', '');

    // add our script if the widget ID is set
    if (!empty($widgetId)) {
        echo '<script type="module" src="https://widget.molin.ai/shop-ai.js?w=' . esc_attr($widgetId) . '"></script>';
    }
});

add_action('admin_menu', function () {
    $svg = file_get_contents(plugin_dir_path(__FILE__) . 'logo.svg');
    $encoded_icon = 'data:image/svg+xml;base64,' . base64_encode($svg);

    add_menu_page(
        'Molin AI', // page title
        'Molin AI', // menu title
        'manage_options', // capability
        'molin-ai', // menu slug
        function () {
?>
        <div class="wrap">
            <div id="button-container" style="position: absolute; right: 20px;">
                <a href="https://molin.ai/app/shop-ai/design" target="_blank" class="customize-widget-button button">Customize AI</a>
                <div id="tooltip-text" style="display: none; position: absolute; background: #f8f8f8; padding: 10px; border: 1px solid #ccc; border-radius: 5px; width: 300px; right: 0;">
                    Go to molin.ai to customize your AI, check conversations, see reports, and much more.
                </div>
            </div>
            <form method="post" action="options.php">
                <?php
                settings_fields(
                    'molinai_settings' // option group
                );
                do_settings_sections(
                    'molin-ai' // page
                );
                submit_button();
                ?>
            </form>
        </div>

        <script type="text/javascript">
            const btnEl = document.querySelector(".customize-widget-button");
            const tooltipEl = document.querySelector("#tooltip-text");

            btnEl.addEventListener("mouseover", () => {
                tooltipEl.style.display = "block";
            });

            btnEl.addEventListener("mouseout", () => {
                tooltipEl.style.display = "none";
            });
        </script>
    <?php
        },
        $encoded_icon, // icon
        100 // position
    );
});

add_action('admin_init', function () {
    // register a new setting
    register_setting('molinai_settings', 'molinai_widget_id');

    // register a new section in the "molin-ai" page
    add_settings_section(
        'molinai_section', // id
        'Molin AI', // title
        function () {
            echo '<p>Enter the details for your Molin AI widget below:</p>';
        },
        'molin-ai' // page
    );

    // register a new field in the section, inside the "molin-ai" page
    add_settings_field(
        'molinai_field', // id
        'Widget ID:', // title
        function () {
            $value = get_option('molinai_widget_id');
            $helpTextFormatted = sprintf(
                'Check the <a href="%s" target="_blank">Publish</a> page on Molin AI for help.',
                'https://molin.ai/app/shop-ai/publish'
            );
    ?>
        <div style="display: flex; align-items: center;">
            <input type="text" name="molinai_widget_id" value="<?php echo esc_attr($value) ?>" />
            <p style="margin-left: 10px;"><?php echo wp_kses_post($helpTextFormatted) ?></p>
        </div>
<?php
        },
        'molin-ai', // page
        'molinai_section' // section
    );
});
