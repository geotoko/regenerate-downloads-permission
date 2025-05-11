function regenerate_download_permissions_for_past_orders($customer_email = '') {
    // Get all completed orders
    $args = array(
        'status' => 'completed',
        'limit' => -1,
        'customer' => $customer_email ? array($customer_email) : '',
    );

    $orders = wc_get_orders($args);

    foreach ($orders as $order) {
        foreach ($order->get_items() as $item) {
            $product_id = $item->get_product_id();
            $variation_id = $item->get_variation_id();
            $product = wc_get_product($variation_id ? $variation_id : $product_id);

            // Regenerate download permissions if the product is downloadable
            if ($product && $product->is_downloadable()) {
                wc_downloadable_file_permission($product_id, $variation_id, $order);
            }
        }
    }

    echo "Download permissions regenerated for " . count($orders) . " orders.";
}

// Regenerate for all customers
add_action('init', function() {
    if (is_admin()) {
        regenerate_download_permissions_for_past_orders();
    }
});
