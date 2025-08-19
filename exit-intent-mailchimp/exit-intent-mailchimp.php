<?php
/*
Plugin Name: Exit Intent Mailchimp Popup
Description: Popup de suscripción a Mailchimp que aparece cuando el usuario intenta salir del sitio o al hacer clic en un elemento con el ID #popactive.
Version: 1.1.0
Author: ChatGPT
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

function eimp_enqueue_scripts() {
    wp_enqueue_style('eimp-style', plugin_dir_url(__FILE__) . 'assets/popup.css');
    wp_enqueue_script('eimp-script', plugin_dir_url(__FILE__) . 'assets/popup.js', array('jquery'), null, true);
    wp_localize_script('eimp-script', 'eimpAjax', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('eimp_nonce'),
    ));
}
add_action('wp_enqueue_scripts', 'eimp_enqueue_scripts');

function eimp_render_popup() {
    include plugin_dir_path(__FILE__) . 'templates/popup.php';
}
add_action('wp_footer', 'eimp_render_popup');

function eimp_subscribe() {
    check_ajax_referer('eimp_nonce', 'nonce');

    $email = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
    if (!is_email($email)) {
        wp_send_json_error(array('message' => __('Correo electrónico inválido.', 'eimp')));
    }

    $api_key = get_option('eimp_api_key');
    $list_id = get_option('eimp_list_id');
    if (!$api_key || !$list_id) {
        wp_send_json_error(array('message' => __('Mailchimp no está configurado.', 'eimp')));
    }

    $member_id = md5(strtolower($email));
    $data      = array(
        'email_address' => $email,
        'status'        => 'subscribed',
    );
    $json = wp_json_encode($data);
    $dc   = substr($api_key, strpos($api_key, '-') + 1);
    $url  = 'https://' . $dc . '.api.mailchimp.com/3.0/lists/' . $list_id . '/members/' . $member_id;

    $response = wp_remote_put($url, array(
        'headers' => array(
            'Authorization' => 'apikey ' . $api_key,
            'Content-Type'  => 'application/json',
        ),
        'body'    => $json,
    ));

    if (is_wp_error($response)) {
        wp_send_json_error(array('message' => __('Error de conexión.', 'eimp')));
    }

    $status = wp_remote_retrieve_response_code($response);
    if (200 === $status) {
        wp_send_json_success();
    }

    wp_send_json_error(array('message' => __('Error al suscribir.', 'eimp')));
}
add_action('wp_ajax_eimp_subscribe', 'eimp_subscribe');
add_action('wp_ajax_nopriv_eimp_subscribe', 'eimp_subscribe');

function eimp_register_settings() {
    register_setting('eimp_settings', 'eimp_api_key');
    register_setting('eimp_settings', 'eimp_list_id');
}
add_action('admin_init', 'eimp_register_settings');

function eimp_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html__('Exit Intent Mailchimp', 'eimp'); ?></h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('eimp_settings');
            do_settings_sections('eimp_settings');
            ?>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="eimp_api_key"><?php esc_html_e('Mailchimp API Key', 'eimp'); ?></label></th>
                    <td><input type="text" id="eimp_api_key" name="eimp_api_key" value="<?php echo esc_attr(get_option('eimp_api_key')); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="eimp_list_id"><?php esc_html_e('List ID', 'eimp'); ?></label></th>
                    <td><input type="text" id="eimp_list_id" name="eimp_list_id" value="<?php echo esc_attr(get_option('eimp_list_id')); ?>" class="regular-text" /></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

function eimp_add_admin_menu() {
    add_options_page('Exit Intent Mailchimp', 'Exit Intent Mailchimp', 'manage_options', 'eimp', 'eimp_settings_page');
}
add_action('admin_menu', 'eimp_add_admin_menu');

?>
