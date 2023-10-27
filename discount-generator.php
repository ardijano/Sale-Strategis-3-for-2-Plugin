<?php
/*
Plugin Name: Sale Strategist
Description: A plugin to set discounts based on specific product attributes and categories.
Version: 1.0
Author: Ardijan
*/

function add_settings_link($links) {
    $settings_link = '<a href="admin.php?page=discount-settings">Settings</a>';
    array_unshift($links, $settings_link);
    return $links;
}



// Activation hook
function activate_discount_settings() {
    // Additional activation code, if needed
}
register_activation_hook(__FILE__, 'activate_discount_settings');

// Deactivation hook
function deactivate_discount_settings() {
    // Additional deactivation code, if needed
}
register_deactivation_hook(__FILE__, 'deactivate_discount_settings');

// Add a settings page
function discount_settings_page() {
    if (isset($_POST['save_settings'])) {
        $attributes = array();
        $categories = array();

        for ($index = 0; isset($_POST['attribute_slug_' . $index]); $index++) {
            $attribute_slug = sanitize_text_field($_POST['attribute_slug_' . $index]);
            $value_to_check = sanitize_text_field($_POST['value_to_check_' . $index]);
            $discount_text = sanitize_text_field($_POST['discount_text_' . $index]);

            $attributes[] = array(
                'attribute_slug' => $attribute_slug,
                'value_to_check' => $value_to_check,
                'discount_text' => $discount_text
            );
        }

        for ($index = 0; isset($_POST['category_slug_' . $index]); $index++) {
            $category = sanitize_text_field($_POST['category_slug_' . $index]);
            $discount_text = sanitize_text_field($_POST['category_discount_text_' . $index]);

            $categories[] = array(
                'category' => $category,
                'discount_text' => $discount_text
            );
        }

        update_option('attribute_value_discount_attributes', $attributes);
        update_option('category_discount_categories', $categories);

        $display_badge_attribute = isset($_POST['_display_badge_attribute']) ? 'yes' : 'no';
        $display_size_attribute = isset($_POST['_display_size_attribute']) ? 'yes' : 'no';
        update_option('_display_badge_attribute', $display_badge_attribute);
        update_option('_display_size_attribute', $display_size_attribute);
    }

    if (isset($_POST['add_attribute_field'])) {
        // Add an empty field for attributes
        $attributes = get_option('attribute_value_discount_attributes', array());
        $attributes[] = array(
            'attribute_slug' => '',
            'value_to_check' => '',
            'discount_text' => ''
        );
        update_option('attribute_value_discount_attributes', $attributes);
    }

    if (isset($_POST['add_category_field'])) {
        // Add an empty field for categories
        $categories = get_option('category_discount_categories', array());
        $categories[] = array(
            'category' => '',
            'discount_text' => ''
        );
        update_option('category_discount_categories', $categories);
    }

    if (isset($_POST['remove_attribute'])) {
        $attributes = get_option('attribute_value_discount_attributes', array());
        $remove_index = intval($_POST['remove_attribute']);
        if (isset($attributes[$remove_index])) {
            unset($attributes[$remove_index]);
        }
        update_option('attribute_value_discount_attributes', array_values($attributes)); // Re-index the array
    }

    if (isset($_POST['remove_category'])) {
        $categories = get_option('category_discount_categories', array());
        $remove_index = intval($_POST['remove_category']);
        if (isset($categories[$remove_index])) {
            unset($categories[$remove_index]);
        }
        update_option('category_discount_categories', array_values($categories)); // Re-index the array
    }

    echo '<div class="wrap">';
    echo '<h2>Attribute and Category Discount Settings</h2>';
    echo '<form method="post">';

    // Display existing attributes and their discount text
    $attributes = get_option('attribute_value_discount_attributes', array());
    echo '<h3>Attribute Discounts</h3>';
    echo '<table>';
    foreach ($attributes as $index => $attribute_data) {
        $attribute_slug = $attribute_data['attribute_slug'];
        $value_to_check = $attribute_data['value_to_check'];
        $discount_text = $attribute_data['discount_text'];

        echo '<tr>';
        echo '<td><label for="attribute_slug_' . $index . '">Attribute Slug: </label></td>';
        echo '<td><input type="text" id="attribute_slug_' . $index . '" name="attribute_slug_' . $index . '" value="' . esc_attr($attribute_slug) . '" /></td>';
        echo '<td><label for="value_to_check_' . $index . '">Value to Check: </label></td>';
        echo '<td><input type="text" id="value_to_check_' . $index . '" name="value_to_check_' . $index . '" value="' . esc_attr($value_to_check) . '" /></td>';
        echo '<td><label for="discount_text_' . $index . '">Discount Text: </label></td>';
        echo '<td><input type="text" id="discount_text_' . $index . '" name="discount_text_' . $index . '" value="' . esc_attr($discount_text) . '" /></td>';
        echo '<td><button type="submit" name="remove_attribute" value="' . $index . '">Remove</button></td>';
        echo '</tr>';
    }
    echo '</table>';

    // Display existing categories and their discount text
    $categories = get_option('category_discount_categories', array());
    echo '<h3>Category Discounts</h3>';
    echo '<table>';
    foreach ($categories as $index => $category_data) {
        $category = $category_data['category'];
        $category_discount_text = $category_data['discount_text'];

        echo '<tr>';
        echo '<td><label for="category_slug_' . $index . '">Category Slug or ID: </label></td>';
        echo '<td><input type="text" id="category_slug_' . $index . '" name="category_slug_' . $index . '" value="' . esc_attr($category) . '" /></td>';
        echo '<td><label for="category_discount_text_' . $index . '">Discount Text: </label></td>';
        echo '<td><input type="text" id="category_discount_text_' . $index . '" name="category_discount_text_' . $index . '" value="' . esc_attr($category_discount_text) . '" /></td>';
        echo '<td><button type="submit" name="remove_category" value="' . $index . '">Remove</button></td>';
        echo '</tr>';
    }
    echo '</table>';

    // Add checkboxes for displaying attributes and categories
    echo '<h3>Display Options</h3>';
    echo '<label for="_display_badge_attribute">Display Badge Attribute on Collection Page: </label>';
    echo '<input type="checkbox" id="_display_badge_attribute" name="_display_badge_attribute" ';
    checked(get_option('_display_badge_attribute'), 'yes');
    echo ' /><br>';


    // Add buttons to add new fields and save settings
    echo '<div style="display:flex;flex-direction:column; gap:1rem;margin-top:1rem;">';
    echo '<button style="width:max-content" type="submit" name="add_attribute_field">Add New Attribute Field</button>';
    echo '<button style="width:max-content" type="submit" name="add_category_field">Add New Category Field</button>';
    echo '</div>';
    echo '<br>';
    echo '<button style="margin:20px 0;" type="submit" name="save_settings" class="button button-primary">Save Settings</button>';
    echo '</form>';
    echo '</div>';
}

add_action('admin_menu', 'add_discount_settings_page');

function add_discount_settings_page() {
    add_menu_page('Discount Settings', 'Sale Strategist', 'manage_options', 'discount-settings', 'discount_settings_page');
}

add_action('woocommerce_cart_calculate_fees', 'apply_discounts', 10, 1);

function apply_discounts($cart) {
    if (is_admin() && !defined('DOING_AJAX')) {
        return;
    }

    // Apply attribute-based discounts
    $attributes = get_option('attribute_value_discount_attributes', array());
    foreach ($attributes as $attribute_data) {
        $attribute_slug = $attribute_data['attribute_slug'];
        $value_to_check = $attribute_data['value_to_check'];
        $discount_text = $attribute_data['discount_text'];

        if (empty($attribute_slug) || empty($value_to_check)) {
            continue; // Skip this attribute if no slug or value specified
        }

        $product_count = 0;
        $group_lowest_price = PHP_INT_MAX;

        foreach ($cart->get_cart() as $cart_item) {
            $product_id = $cart_item['product_id'];
            $product = wc_get_product($product_id);
            $attribute_value = $product->get_attribute($attribute_slug);
            if (str_contains($attribute_value, $value_to_check)) {
                $product_count++;

                $line_total = $cart_item['line_total'];
                if ($line_total < $group_lowest_price) {
                    $group_lowest_price = $line_total;
                }

                if ($product_count % 3 === 0) {
                    // Calculate the discount for each group of 3 products based on the lowest price
                    $discount = -($group_lowest_price);
                    $cart->add_fee($discount_text, $discount, false);

                    // Reset the lowest price for the next group
                    $group_lowest_price = PHP_INT_MAX;
                }
            }
        }
    }

    // Apply category-based discounts
    $categories = get_option('category_discount_categories', array());
    foreach ($categories as $category_data) {
        $category = $category_data['category'];

        if (empty($category)) {
            continue; // Skip if no category specified
        }

        $category_discount_text = $category_data['discount_text'];
        $product_count = 0;
        $group_lowest_price = PHP_INT_MAX;

        foreach ($cart->get_cart() as $cart_item) {
            if (has_term($category, 'product_cat', $cart_item['product_id'])) {
                $product_count++;

                $line_total = $cart_item['line_total'];
                if ($line_total < $group_lowest_price) {
                    $group_lowest_price = $line_total;
                }

                if ($product_count % 3 === 0) {
                    // Calculate the discount for each group of 3 products based on the lowest price
                    $discount = -($group_lowest_price);
                    $cart->add_fee($category_discount_text , $discount, false);

                    // Reset the lowest price for the next group
                    $group_lowest_price = PHP_INT_MAX;
                }
            }
        }
    }
}

add_action('woocommerce_before_shop_loop_item_title', 'display_dynamic_discount_badge_on_collection_page');

function display_dynamic_discount_badge_on_collection_page() {
    global $product;

    // Get the list of attributes set up in the plugin settings
    $attributes = get_option('attribute_value_discount_attributes', array());

    foreach ($attributes as $attribute_data) {
        $attribute_slug = $attribute_data['attribute_slug'];
        $discount_text = $attribute_data['discount_text'];

        if (!empty($attribute_slug) && !empty($discount_text)) {
            // Check if the product has the attribute and its value matches the attribute's "Value to Check"
            $product_attribute_value = $product->get_attribute($attribute_slug);

            if (!empty($product_attribute_value) && strpos($product_attribute_value, $attribute_data['value_to_check']) !== false) {
                // Display the discount badge with the dynamic discount text
                echo '<div class="product-discount-badge">';
                echo   '<div>' . esc_html($discount_text) . '</div>';
                echo '<style>.product-discount-badge {
                    background: #fff;
                    color: #000;
                    line-height: 2em;
                    position: absolute;
                    padding: 0.2rem 0.4rem;
                    top: 75%;
                    right: 6%;
                }
                @media (max-width: 480px) {
                    .product-discount-badge {
                    top: 65%;
                    font-size: 12px;
                  }
                  }
                </style>';
                echo '</div>';
                break; // Exit the loop after finding the first matching attribute
            }
        }
    }
}
