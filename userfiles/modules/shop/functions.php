<?

/**
 *
 * Shop module api
 *
 * @package		modules
 * @subpackage		shop
 * @since		Version 0.1
 */

// ------------------------------------------------------------------------

if (!defined("MODULE_DB_TABLE_SHOP")) {
	define('MODULE_DB_TABLE_SHOP', MW_TABLE_PREFIX . 'cart');
}

if (!defined("MODULE_DB_TABLE_SHOP_ORDERS")) {
	define('MODULE_DB_TABLE_SHOP_ORDERS', MW_TABLE_PREFIX . 'cart_orders');
}

action_hook('mw_db_init', 'mw_shop_module_init_db');

function mw_shop_module_init_db() {
	$function_cache_id = false;

	$args = func_get_args();

	foreach ($args as $k => $v) {

		$function_cache_id = $function_cache_id . serialize($k) . serialize($v);
	}

	$function_cache_id = __FUNCTION__ . crc32($function_cache_id);

	$cache_content = cache_get_content($function_cache_id, 'db');

	if (($cache_content) != false) {

		return $cache_content;
	}

	$table_name = MODULE_DB_TABLE_SHOP;

	$fields_to_add = array();
	$fields_to_add[] = array('title', 'TEXT default NULL');
	$fields_to_add[] = array('is_active', "char(1) default 'y'");
	$fields_to_add[] = array('to_table_id', 'int(11) default NULL');
	$fields_to_add[] = array('to_table', 'varchar(350)  default NULL ');
	$fields_to_add[] = array('updated_on', 'datetime default NULL');
	$fields_to_add[] = array('created_on', 'datetime default NULL');
	$fields_to_add[] = array('price', 'float default NULL');
	$fields_to_add[] = array('currency', 'varchar(33)  default NULL ');
	$fields_to_add[] = array('session_id', 'varchar(255)  default NULL ');
	$fields_to_add[] = array('qty', 'int(11) default NULL');
	$fields_to_add[] = array('other_info', 'TEXT default NULL');
	$fields_to_add[] = array('order_completed', "char(1) default 'n'");
	$fields_to_add[] = array('order_id', 'varchar(255)  default NULL ');
	$fields_to_add[] = array('skip_promo_code', "char(1) default 'n'");
	$fields_to_add[] = array('created_by', 'int(11) default NULL');
	$fields_to_add[] = array('custom_fields_data', 'TEXT default NULL');

	set_db_table($table_name, $fields_to_add);

	// db_add_table_index ( 'title', $table_name, array ('title' ), "FULLTEXT" );
	db_add_table_index('to_table', $table_name, array('to_table'));
	db_add_table_index('to_table_id', $table_name, array('to_table_id'));

	db_add_table_index('session_id', $table_name, array('session_id'));

	$table_name = MODULE_DB_TABLE_SHOP_ORDERS;

	$fields_to_add = array();

	$fields_to_add[] = array('updated_on', 'datetime default NULL');
	$fields_to_add[] = array('created_on', 'datetime default NULL');
	$fields_to_add[] = array('country', 'varchar(255)  default NULL ');
	$fields_to_add[] = array('promo_code', 'TEXT default NULL');
	$fields_to_add[] = array('amount', 'float default NULL');
	$fields_to_add[] = array('transaction_id', 'TEXT default NULL');
	$fields_to_add[] = array('shipping_service', 'TEXT default NULL');
	$fields_to_add[] = array('shipping', 'float default NULL');
	$fields_to_add[] = array('currency', 'varchar(33)  default NULL ');

	$fields_to_add[] = array('currency_code', 'varchar(33)  default NULL ');

	$fields_to_add[] = array('first_name', 'TEXT default NULL');

	$fields_to_add[] = array('last_name', 'TEXT default NULL');

	$fields_to_add[] = array('email', 'TEXT default NULL');

	$fields_to_add[] = array('city', 'TEXT default NULL');

	$fields_to_add[] = array('state', 'TEXT default NULL');

	$fields_to_add[] = array('zip', 'TEXT default NULL');
	$fields_to_add[] = array('address', 'TEXT default NULL');
	$fields_to_add[] = array('address2', 'TEXT default NULL');
	$fields_to_add[] = array('phone', 'TEXT default NULL');

	$fields_to_add[] = array('created_by', 'int(11) default NULL');
	$fields_to_add[] = array('edited_by', 'int(11) default NULL');
	$fields_to_add[] = array('session_id', 'varchar(255)  default NULL ');
	$fields_to_add[] = array('order_completed', "char(1) default 'n'");
	$fields_to_add[] = array('is_paid', "char(1) default 'n'");
	$fields_to_add[] = array('url', 'TEXT default NULL');
	$fields_to_add[] = array('user_ip', 'varchar(255)  default NULL ');
	$fields_to_add[] = array('items_count', 'int(11) default NULL');
	$fields_to_add[] = array('payment_gw', 'TEXT  default NULL ');
	$fields_to_add[] = array('payment_verify_token', 'TEXT  default NULL ');
	$fields_to_add[] = array('payment_amount', 'float default NULL');

	$fields_to_add[] = array('payment_status', 'varchar(255)  default NULL ');

	$fields_to_add[] = array('payment_email', 'TEXT default NULL');

	$fields_to_add[] = array('payment_name', 'TEXT default NULL');

	$fields_to_add[] = array('payment_country', 'TEXT default NULL');

	$fields_to_add[] = array('payment_address', 'TEXT default NULL');

	$fields_to_add[] = array('payment_city', 'TEXT default NULL');
	$fields_to_add[] = array('payment_state', 'TEXT default NULL');
	$fields_to_add[] = array('payment_zip', 'TEXT default NULL');

	$fields_to_add[] = array('payer_id', 'TEXT default NULL');

	$fields_to_add[] = array('payer_status', 'TEXT default NULL');
	$fields_to_add[] = array('payment_type', 'TEXT default NULL');
	$fields_to_add[] = array('order_status', 'TEXT default NULL');

	$fields_to_add[] = array('payment_shipping', 'float default NULL');

	$fields_to_add[] = array('is_active', "char(1) default 'y'");
	$fields_to_add[] = array('to_table_id', 'int(11) default NULL');
	$fields_to_add[] = array('to_table', 'varchar(350)  default NULL ');
	$fields_to_add[] = array('price', 'float default NULL');
	$fields_to_add[] = array('other_info', 'TEXT default NULL');
	$fields_to_add[] = array('order_id', 'varchar(255)  default NULL ');
	$fields_to_add[] = array('skip_promo_code', "char(1) default 'n'");

	set_db_table($table_name, $fields_to_add);

	// db_add_table_index ( 'title', $table_name, array ('title' ), "FULLTEXT" );
	db_add_table_index('to_table', $table_name, array('to_table'));
	db_add_table_index('to_table_id', $table_name, array('to_table_id'));

	db_add_table_index('session_id', $table_name, array('session_id'));

	/*$fields_to_add [] = array ('content_subtype', 'varchar(150) default "none"' );
	 $fields_to_add [] = array ('content_subtype_value', 'varchar(150) default "none"' );

	 $fields_to_add [] = array ('content_layout_file', 'varchar(150) default NULL' );
	 $fields_to_add [] = array ('content_layout_name', 'varchar(50) default NULL' );
	 $fields_to_add [] = array ('content_layout_style', 'varchar(50) default NULL' );

	 $fields_to_add [] = array ('content_url', 'varchar(150) default NULL' );
	 //$fields_to_add [] = array ('content_url_md5', 'varchar(33) default NULL' );

	 $fields_to_add [] = array ('content_filename', 'varchar(150) default NULL' );
	 $fields_to_add [] = array ('content_filename_sync_with_editor', 'char(1) default "n"' );
	 //$fields_to_add [] = array ('model_group', 'varchar(150) default NULL' );
	 $fields_to_add [] = array ('content_parent', 'int(11) default 0' );
	 $fields_to_add [] = array ('content_title', 'varchar(150) default NULL' );
	 $fields_to_add [] = array ('content_meta_title', 'varchar(600) default NULL' );
	 $fields_to_add [] = array ('content_meta_description', 'TEXT default NULL' );
	 $fields_to_add [] = array ('content_meta_keywords', 'TEXT default NULL' );

	 $fields_to_add [] = array ('is_active', 'char(1) default "y"' );
	 $fields_to_add [] = array ('is_home', 'char(1) default "n"' );
	 $fields_to_add [] = array ('is_featured', 'char(1) default "n"' );
	 $fields_to_add [] = array ('is_pinged', 'char(1) default "n"' );
	 $fields_to_add [] = array ('comments_enabled', 'char(1) default "y"' );

	 $fields_to_add [] = array ('expires_on', 'datetime default NULL' );

	 $fields_to_add [] = array ('visible_on_frontend', 'char(1) default "y"' );

	 $fields_to_add [] = array ('original_link', 'TEXT default NULL' );

	 $fields_to_add [] = array ('require_login', "ENUM('n', 'y') default 'n'" );
	 $fields_to_add [] = array ('created_by', 'int(11) default NULL' );
	 $fields_to_add [] = array ('edited_by', 'int(11) default NULL' );
	 $fields_to_add [] = array ('is_special', 'char(1) default "n"' );*/

	//add fulltext search
	//ALTER TABLE articles ADD FULLTEXT(body, title);
	//$sql = "alter table $table_name add FULLTEXT (content_url, content_title, content_body)  ";
	//$this->db->query ( $sql );

	/*
	 db_add_table_index ( 'content_type', $table_name, array ('content_type' ) );
	 db_add_table_index( 'content_subtype', $table_name, array ('content_subtype' ) );
	 db_add_table_index ( 'content_title', $table_name, array ('content_subtype' ), "FULLTEXT" );
	 db_add_table_index ( 'content_body', $table_name, array ('content_body' ), "FULLTEXT" );
	 db_add_table_index ( 'content_description', $table_name, array ('content_description' ), "FULLTEXT" );
	 db_add_table_index ( 'content_url', $table_name, array ('content_url' ), "FULLTEXT" );

	 db_add_table_index ( 'created_by', $table_name, array ('created_by' ) );
	 db_add_table_index( 'edited_by', $table_name, array ('edited_by' ) );

	 */

	cache_store_data(true, $function_cache_id, $cache_group = 'db');
	// $fields = (array_change_key_case ( $fields, CASE_LOWER ));
	return true;

	//print '<li'.$cls.'><a href="'.admin_url().'view:settings">newsl etenewsl etenewsl etenewsl etenewsl etenewsl etenewsl etenewsl etenewsl etenewsl etenewsl etenewsl etenewsl etenewsl etenewsl etenewsl eter</a></li>';
}

action_hook('mw_admin_header_menu_start', 'mw_print_admin_menu_shop_btn');

function mw_print_admin_menu_shop_btn() {
	$active = url_param('view');
	$cls = '';
	if ($active == 'shop') {
		$cls = ' class="active" ';
	}
	print '<li' . $cls . '><a href="' . admin_url() . 'view:shop">Online Shop</a></li>';
}

api_expose('update_order');
/**
 * update_order
 *
 * updates order by parameters
 *
 * @package		modules
 * @subpackage	shop
 * @subpackage	shop\orders
 * @category	shop module api
 */
function update_order($params = false) {

	$params2 = array();
	if ($params == false) {
		$params = array();
	}
	if (is_string($params)) {
		$params = parse_str($params, $params2);
		$params = $params2;
	}
	if (is_admin() == false) {

		error("You must be admin");
	}

	$table = MODULE_DB_TABLE_SHOP_ORDERS;
	$params['table'] = $table;

	//  d($params);
	return save_data($table, $params);

}

function get_orders($params = false) {

	$params2 = array();
	if ($params == false) {
		$params = array();
	}
	if (is_string($params)) {
		$params = parse_str($params, $params2);
		$params = $params2;
	}
	if (is_admin() == false) {
		if (!isset($params['payment_verify_token']))
			error("You must be admin");
	}

	$table = MODULE_DB_TABLE_SHOP_ORDERS;
	$params['table'] = $table;

	//  d($params);
	return get($params);

}

function cart_sum($return_amount = true) {
	if (!session_id() and !headers_sent()) {
		session_start();
	}

	$sid = session_id();
	$diferent_items = 0;
	$amount = floatval(0.00);
	$table_cart = MODULE_DB_TABLE_SHOP;
	$sumq = " SELECT  price, qty FROM $table_cart where order_completed='n'  and session_id='{$sid}'  ";
	$sumq = db_query($sumq);
	if (isarr($sumq)) {
		foreach ($sumq as $value) {
			$diferent_items++;
			$amount = $amount + (intval($value['qty']) * floatval($value['price']));
		}
	}
	if ($return_amount == false) {
		return $diferent_items;
	}
	return $amount;
}

api_expose('checkout_ipn1');

function checkout_ipn1() {

	$cache_gr = 'ipn';
	$cache_id = '1';
	$c = cache_get_content($cache_id, $cache_gr);
	d($c);
	$c = checkout_ipn($c);
	d($c);
}

api_expose('checkout_ipn');

function checkout_ipn($data) {
	if (!session_id() and !headers_sent()) {
		session_start();
	}
	$sid = session_id();

	$cache_gr = 'ipn';
	$cache_id = md5(serialize($data));
	if (isset($_GET) and !empty($_GET) and !empty($_POST)) {
		$data = $_REQUEST;
	}
	if (isset($data['payment_verify_token'])) {
		$payment_verify_token = trim($data['payment_verify_token']);
	}

	if (isset($_REQUEST['payment_verify_token'])) {
		$payment_verify_token = ($_REQUEST['payment_verify_token']);
	}

	if (isset($_REQUEST['payment_gw'])) {
		$data['payment_gw'] = ($_REQUEST['payment_gw']);
	}
	$data['payment_gw'] = str_replace('..', '', $data['payment_gw']);

	$hostname = get_domain_from_str($_SERVER['REMOTE_ADDR']);

	cache_save($_REQUEST, $cache_id, $cache_gr);
	//d($payment_verify_token);
	$ord_data = get_orders('no_cache=1&limit=1&tansaction_id=[is]NULL&payment_verify_token=' . $payment_verify_token . '');
	// d($ord_data);

	if (!isset($ord_data[0]) or !isarr($ord_data[0])) {
		error('Order is completed or expired.');
	} else {

		$ord_data = $ord_data[0];
		$ord = $ord_data['id'];
	}

	$payment_verify_token_data = decrypt_var($payment_verify_token);

	if (!isarr($payment_verify_token_data)) {
		error('Invalid token.');
	}
	$table_cart = MODULE_DB_TABLE_SHOP;
	$table_orders = MODULE_DB_TABLE_SHOP_ORDERS;

	$shop_dir = module_dir('shop');
	$shop_dir = $shop_dir . DS . 'payments' . DS . 'gateways' . DS;
	$gw_process = $shop_dir . $data['payment_gw'] . '_checkout_ipn.php';
	$update_order = array();
	//$update_order['id'] = $ord;
	if (is_file($gw_process)) {
		include $gw_process;
	}
	if (!empty($update_order)) {
		$update_order['id'] = $ord;
		$update_order['is_paid'] = 'y';
		//$update_order['debug'] = 'y';
		$update_order['order_completed'] = 'y';
		$update_order['payment_gw'] = $data['payment_gw'];
		mw_var('FORCE_SAVE', $table_orders);
		mw_var('FORCE_ANON_UPDATE', $table_orders);
		save_data($table_orders, $update_order);

		if ($ord > 0) {

			$q = " UPDATE $table_cart set 
				order_completed='y', order_id='{$ord}' 
				where order_completed='n'   ";
			//d($q);
			db_q($q);

			$q = " UPDATE $table_orders set 
				order_completed='y'   
				where order_completed='n' and 
				id='{$ord}'  ";
			//	 d($q);
			db_q($q);
			cache_clean_group('cart/global');
			cache_clean_group('cart_orders/global');
			return true;
		}
	}
	//
	return false;
}

api_expose('checkout');

function checkout($data) {
	if (!session_id() and !headers_sent()) {
		session_start();
	}
	$sid = session_id();
	$cart = array();
	$table_cart = MODULE_DB_TABLE_SHOP;
	$table_orders = MODULE_DB_TABLE_SHOP_ORDERS;
	$cart['session_id'] = $sid;
	$cart['order_completed'] = 'n';
	$cart['limit'] = 1;
	$mw_process_payment = true;
	if (isset($_GET['mw_payment_success'])) {
		$mw_process_payment = false;
	}

	if (isset($_REQUEST['mw_payment_success']) and intval($_REQUEST['mw_payment_success']) == 1 and isset($_SESSION['order_id'])) {

		$_SESSION['mw_payment_success'] = true;
		$ord = $_SESSION['order_id'];
		if ($ord > 0) {
			$q = " UPDATE $table_cart set 
				order_completed='y', order_id='{$ord}' 
				where order_completed='n'   and session_id='{$sid}'  ";
			//d($q);
			db_q($q);

			$q = " UPDATE $table_orders set 
				order_completed='y'   
				where order_completed='n' and 
				id='{$ord}' and 
				session_id='{$sid}'  ";
			//d($q);
			db_q($q);

		}

		cache_clean_group('cart/global');
		cache_clean_group('cart_orders/global');
		if (isset($_GET['return_to'])) {
			$return_to = urldecode($_GET['return_to']);
			safe_redirect($return_to);
		}
	}

	$check_cart = get_cart($cart);
	if (!isarr($check_cart)) {
		error('Your cart is empty');
	} else {

		if (!isset($data['payment_gw']) and $mw_process_payment == true) {
			error('No payment method is specified');
		} else {
			if ($mw_process_payment == true) {
				$gw_check = payment_options('payment_gw_' . $data['payment_gw']);
				if (isarr($gw_check[0])) {
					$gateway = $gw_check[0];
				} else {
					error('No such payment gateway is activated');
				}

			}
		}

		$shiping_country = false;
		$shiping_cost_max = false;
		$shiping_cost = false;
		$shiping_cost_above = false;
		if (isset($_SESSION['shiping_country'])) {
			$shiping_country = $_SESSION['shiping_country'];
		}
		if (isset($_SESSION['shiping_cost_max'])) {
			$shiping_cost_max = $_SESSION['shiping_cost_max'];
		}
		if (isset($_SESSION['shiping_cost'])) {
			$shiping_cost = $_SESSION['shiping_cost'];
		}
		if (isset($_SESSION['shiping_cost_above'])) {
			$shiping_cost_above = $_SESSION['shiping_cost_above'];
		}

		//post any of those on the form
		$flds_from_data = array('first_name', 'last_name', 'email', 'country', 'city', 'state', 'zip', 'address', 'address2', 'payment_email', 'payment_name', 'payment_country', 'payment_address', 'payment_city', 'payment_state', 'payment_zip', 'phone', 'promo_code', 'payment_gw');

		$posted_fields = array();
		$place_order = array();
		//$place_order['order_id'] = "ORD-" . date("YmdHis") . '-' . $cart['session_id'];

		$return_url_after = '';
		if (isAjax()) {
			$place_order['url'] = curent_url(true);
			$return_url_after = '&return_to=' . urlencode($_SERVER['HTTP_REFERER']);
		} elseif (isset($_SERVER['HTTP_REFERER'])) {
			$place_order['url'] = $_SERVER['HTTP_REFERER'];
			$return_url_after = '&return_to=' . urlencode($_SERVER['HTTP_REFERER']);
		} else {
			$place_order['url'] = curent_url();

		}

		$place_order['session_id'] = $sid;

		$place_order['order_completed'] = 'n';
		$items_count = 0;
		foreach ($flds_from_data as $value) {
			if (isset($data[$value]) and ($data[$value]) != false) {
				$place_order[$value] = $data[$value];
				$posted_fields[$value] = $data[$value];

			}
		}
		$amount = cart_sum();
		if ($amount == 0) {
			error('Cart sum is 0?');
			return;
		}
		$place_order['amount'] = $amount;

		if (isset($data['shipping_gw'])) {
			$place_order['shipping_service'] = $data['shipping_gw'];
		}

		if (intval($shiping_cost_above) > 0 and intval($shiping_cost_max) > 0) {
			if ($amount > $shiping_cost_above) {
				$shiping_cost = $shiping_cost_max;
			}
		}

		$place_order['shipping'] = $shiping_cost;

		$items_count = cart_sum(false);
		$place_order['items_count'] = $items_count;

		$cart_checksum = md5($sid . serialize($check_cart));

		$place_order['item_name'] = 'Order' . ' ' . $cart_checksum . '' . $amount;

		$place_order['payment_verify_token'] = encrypt_var($place_order);

		if ($mw_process_payment == true) {
			$shop_dir = module_dir('shop');
			$shop_dir = $shop_dir . DS . 'payments' . DS . 'gateways' . DS;
			$gw_process = $shop_dir . $data['payment_gw'] . '_process.php';

			$mw_return_url = api_url('checkout') . '?mw_payment_success=1' . $return_url_after;
			$mw_cancel_url = api_url('checkout') . '?mw_payment_failure=1' . $return_url_after;
			$mw_ipn_url = api_url('checkout_ipn') . '?payment_gw=' . $data['payment_gw'] . '&payment_verify_token=' . $place_order['payment_verify_token'];

			if (is_file($gw_process)) {
				require_once $gw_process;
			} else {
				error('Payment gateway\'s process file not found.');
			}
			// $q = " DELETE FROM $table_orders  	where order_completed='n'  and session_id='{$sid}' and is_paid='n' ";

			// db_q($q);

			define('FORCE_SAVE', $table_orders);
			$ord = save_data($table_orders, $place_order);

			$q = " UPDATE $table_cart set 
				order_id='{$ord}' 
				where order_completed='n'  and session_id='{$sid}'  ";

			db_q($q);

			if (isset($place_order['order_completed']) and $place_order['order_completed'] == 'y') {
				if (isset($place_order['is_paid']) and $place_order['is_paid'] == 'y') {
					$q = " UPDATE $table_cart set 
				order_completed='y', order_id='{$ord}' 
				where order_completed='n'   ";

					db_q($q);

					$q = " UPDATE $table_orders set 
				order_completed='y'   
				where order_completed='n' and 
				id='{$ord}'  ";

					db_q($q);
					cache_clean_group('cart/global');
					cache_clean_group('cart_orders/global');
				}

				//$_SESSION['mw_payment_success'] = true;
			}

			$_SESSION['order_id'] = $ord;
		}

		exit();

		return ($ord);

	}

	//d($check_cart);
}

api_expose('update_cart');

function update_cart($data) {

	if (!session_id() and !headers_sent()) {
		session_start();
	}

	if (!isset($data['for'])) {
		$data['for'] = 'table_content';
	}

	if (!isset($data['for']) or !isset($data['for_id'])) {
		error('Invalid data');
	}

	$data['for'] = db_get_assoc_table_name($data['for']);

	$for = $data['for'];
	$for_id = intval($data['for_id']);
	$update_qty = 0;

	if ($for_id == 0) {

		error('Invalid data');
	}

	if ($data['for'] == 'table_content') {
		$cont = get_content_by_id($for_id);

		if ($cont == false) {
			error('Invalid product?');
		} else {
			if (isarr($cont) and isset($cont['title'])) {
				$data['title'] = $cont['title'];
			}
		}
	}

	if (isset($data['qty'])) {
		$update_qty = intval($data['qty']);
		unset($data['qty']);
	}

	$cfs = array();
	$cfs = get_custom_fields($for, $for_id, 1);
	if ($cfs == false) {
		error('Invalid data');
	}

	$add = array();
	$prices = array();
	$found_price = false;
	foreach ($data as $k => $item) {
		$found = false;
		foreach ($cfs as $cf) {
			if (isset($cf['custom_field_type']) and $cf['custom_field_type'] != 'price') {

				if (isset($cf['custom_field_name']) and $cf['custom_field_name'] == $k) {
					$found = true;
					if (is_array($item)) {

						if (isarr($cf['custom_field_values'])) {

							$vi = 0;
							foreach ($item as $ik => $item_value) {

								if (in_array($item_value, $cf['custom_field_values'])) {

								} else {
									unset($item[$ik]);
								}

								$vi++;
							}
						}
						// d($item);
					} else {
						if ($cf['custom_field_value'] != $item) {
							unset($item);
						}
					}
					//   d($k);
					//
				}
			} elseif (isset($cf['custom_field_type']) and $cf['custom_field_type'] == 'price') {
				$prices[] = $cf['custom_field_value'];
			}
		}

		if (isarr($prices)) {

			foreach ($prices as $price) {
				if ($price == $item) {
					$found = true;
					if ($found_price == false) {
						$found_price = $item;
					}
					// d($item);
				} else {
					// unset($item);
				}
			}

			// d($prices);
		}

		//if (isset($item)) {
		if ($found == true) {
			$add[$k] = ($item);
		}
		// }
	}
	if ($found_price == false) {
		// $found_price = 0;
		error('Invalid data: Please post a "price" field with <input name="price"> ');
	}

	if (isarr($prices)) {
		ksort($add);
		asort($add);
		$table = MODULE_DB_TABLE_SHOP;
		$cart = array();
		$cart['to_table'] = ($data['for']);
		$cart['to_table_id'] = intval($data['for_id']);
		$cart['title'] = ($data['title']);
		$cart['price'] = floatval($found_price);
		$cart['custom_fields_data'] = encode_var($add);
		$cart['order_completed'] = 'n';
		$cart['session_id'] = session_id();
		$cart['one'] = 1;
		$cart['limit'] = 1;
		//  $cart['no_cache'] = 1;
		$checkz = get_cart($cart);
		// d($checkz);
		if ($checkz != false and isarr($checkz)) {
			//    d($check);
			$cart['id'] = $checkz['id'];
			if ($update_qty > 0) {
				$cart['qty'] = $checkz['qty'] + $update_qty;
			} else {
				$cart['qty'] = $checkz['qty'] + 1;
			}

			//
		} else {

			if ($update_qty > 0) {
				$cart['qty'] = $update_qty;
			} else {
				$cart['qty'] = 1;
			}
		}
		//
		mw_var('FORCE_SAVE', $table);

		$cart_s = save_data($table, $cart);
		return ($cart_s);
	} else {
		error('Invalid cart items');
	}

	//  d($data);
	exit ;
}

api_expose('update_cart_item_qty');

function update_cart_item_qty($data) {

	if (!isset($data['id'])) {
		error('Invalid data');
	}

	if (!isset($data['qty'])) {
		error('Invalid data');
	}
	if (!session_id() and !headers_sent()) {
		session_start();
	}
	$cart = array();
	$cart['id'] = intval($data['id']);

	//if (is_admin() == false) {
	$cart['session_id'] = session_id();
	//}
	$cart['order_completed'] = 'n';

	$cart['one'] = 1;
	$cart['limit'] = 1;
	$checkz = get_cart($cart);

	if ($checkz != false and isarr($checkz)) {
		// d($checkz);
		$cart['qty'] = intval($data['qty']);
		$table = MODULE_DB_TABLE_SHOP;
		mw_var('FORCE_SAVE', $table);

		$cart_s = save_data($table, $cart);
		return ($cart_s);
		//   db_delete_by_id($table, $id = $cart['id'], $field_name = 'id');
	} else {

	}
}

api_expose('remove_cart_item');

function remove_cart_item($data) {

	if (!isset($data['id'])) {
		error('Invalid data');
	}
	if (!session_id() and !headers_sent()) {
		session_start();
	}
	$cart = array();
	$cart['id'] = intval($data['id']);

	if (is_admin() == false) {
		$cart['session_id'] = session_id();
	}
	$cart['order_completed'] = 'n';

	$cart['one'] = 1;
	$cart['limit'] = 1;
	$checkz = get_cart($cart);

	if ($checkz != false and isarr($checkz)) {
		// d($checkz);
		$table = MODULE_DB_TABLE_SHOP;
		db_delete_by_id($table, $id = $cart['id'], $field_name = 'id');
	} else {

	}
}

function get_cart($params) {
	$params2 = array();

	if (is_string($params)) {
		$params = parse_str($params, $params2);
		$params = $params2;
	}
	$table = MODULE_DB_TABLE_SHOP;
	$params['table'] = $table;
	$params['session_id'] = session_id();
	if (isset($params['no_session_id']) and is_admin() == true) {
		unset($params['session_id']);
		//	$params['session_id'] = session_id();
	} else {

	}

	//  d($params);
	return get($params);
}

function payment_options($option_key = false) {

	$option_key_q = '';
	if (is_string($option_key)) {
		$option_key_q = "&limit=1&option_key={$option_key}";

	}

	$providers = get_options('option_group=payments' . $option_key_q);
	$str = 'payment_gw_';
	$l = strlen($str);

	$valid = array();
	foreach ($providers as $value) {
		if ($value['option_value'] == 'y') {
			if (substr($value['option_key'], 0, $l) == $str) {
				$title = substr($value['option_key'], $l);
				$string = preg_replace('/(\w+)([A-Z])/U', '\\1 \\2', $title);
				$value['gw_file'] = $title;
				$value['title'] = $string;
				$valid[] = $value;

			}
		}
	}
	return $valid;
}
