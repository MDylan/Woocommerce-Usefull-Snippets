<?php

/**
 * Plugin Name: WooCommerce Update Order Payment Method
 * Description: You can update order payment method in WooCommerce Order Edit page
 * Author: David Molnar
 */

add_action('woocommerce_admin_order_data_after_order_details', 'custom_admin_edit_payment_method');
add_action('woocommerce_before_order_object_save', 'custom_save_payment_method');

function custom_admin_edit_payment_method($order)
{
    if (!$order || !is_a($order, 'WC_Order')) {
        return;
    }

    $available_gateways = WC()->payment_gateways->get_available_payment_gateways();
    $current_payment_method = $order->get_payment_method();
    $current_payment_method_title = $order->get_payment_method_title();

    echo '<p class="form-field form-field-wide">';
    echo '<label for="szu_custom_payment_method">' . __('Payment method:', 'woocommerce') . ' (' . $current_payment_method_title . ')</label>';
    echo '<select name="szu_custom_payment_method" id="szu_custom_payment_method">';
    foreach ($available_gateways as $gateway) {
        echo '<option value="' . esc_attr($gateway->id) . '"' . selected($current_payment_method, $gateway->id, false) . '>';
        echo esc_html($gateway->get_title());
        echo '</option>';
    }
    echo '</select>';
    echo '</p>';
}

function custom_save_payment_method($order)
{

    if (!$order || !is_a($order, 'WC_Order')) {
        return;
    }

    if (isset($_POST['szu_custom_payment_method'])) {
        $new_payment_method = sanitize_text_field($_POST['szu_custom_payment_method']);

        // Save new payment method
        if (!empty($new_payment_method)) {
            $order->set_payment_method($new_payment_method);
            $available_gateways = WC()->payment_gateways->get_available_payment_gateways();

            //Save new payment method title
            foreach ($available_gateways as $gateway) {
                if (esc_attr($gateway->id) == $new_payment_method) {
                    $order->set_payment_method_title($gateway->get_title());
                }
            }
        }
    }
}
