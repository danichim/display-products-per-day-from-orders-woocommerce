<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       lioncode.ro
 * @since      1.0.0
 *
 * @package    Orderspermeta
 * @subpackage Orderspermeta/admin/partials
 */

$path = preg_replace('/wp-content(?!.*wp-content).*/','',__DIR__);
include($path.'wp-load.php');
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<?php
$myoptions = get_option('settings_page_example_setting');

$totalComenzi = [];

global $wpdb;
$results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}posts WHERE post_type LIKE 'shop_order' ORDER BY ID DESC LIMIT {$myoptions}");

// Loop through each order post object
foreach( $results as $result ){
    $order_id = $result->ID; // The Order ID

    // Get an instance of the WC_Order Object
    $order    = wc_get_order( $result->ID );

    $created_date = $order->get_date_created();
    $order_data = $created_date->date('j F Y');

    $order_items = $order->get_items();

    foreach ( $order_items as $product ) {
    	if (isset($totalComenzi[$order_data])) {
    		if (isset($totalComenzi[$order_data][$product['name']])) {
				$totalComenzi[$order_data][$product['name']] += $product['quantity'];
			} else {
				$totalComenzi[$order_data][$product['name']] = 0;
				$totalComenzi[$order_data][$product['name']] += $product['quantity'];
			}
		} else {
		 	$totalComenzi[$order_data] = [];
		 	if (isset($totalComenzi[$order_data][$product['name']])) {
				$totalComenzi[$order_data][$product['name']] += $product['quantity'];
			} else {
				$totalComenzi[$order_data][$product['name']] = 0;
				$totalComenzi[$order_data][$product['name']] += $product['quantity'];
			}
		}
    }

}

ksort($totalComenzi);
$revesedDates = array_reverse($totalComenzi);

echo "<table class='paleBlueRows'>";
$index = 0;
foreach ($revesedDates as $key => $value) {
	
	$isEven = $index % 2 == 0;
	$className = $index % 2 == 0 ? 'impar' : '';
	echo "<tr class='{$className}'><td class='titlu'>{$key}</td><td class='titlu'>Cantitate</td><tr/>";
	foreach ($value as $product => $qty) {
		echo "<tr class='{$className}'><td>{$product}</td><td>{$qty}</td></tr>";
	}
	$index++;
    
}
echo '</table>';

?>