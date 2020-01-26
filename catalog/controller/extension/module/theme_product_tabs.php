<?php
class ControllerExtensionModuleThemeProductTabs extends Controller {
	public function index($setting) {
        $setting['width']=300;
        $setting['height']=450;
		static $module = 0;
		
		$this->load->language('extension/module/theme_product_tabs');

		$data['heading_title'] = $this->language->get('heading_title');

		$data['button_cart'] = $this->language->get('button_cart');
		$data['button_wishlist'] = $this->language->get('button_wishlist');
		$data['button_compare'] = $this->language->get('button_compare');
		
		$data['lang_id'] = $this->config->get('config_language_id');
		$lang_id = $this->config->get('config_language_id');
		$data['lazy_load_placeholder'] = 'catalog/view/theme/' . $this->config->get('config_theme') . '/js/lazyload/loading.gif';
		$this->load->model('catalog/product');
		$this->load->model('tool/image');
		// Bestseller
		$data['bestseller_products'] = array();
		$bestseller_results = $this->model_catalog_product->getBestSellerProducts($setting['limit']);
		if ($bestseller_results) {
			foreach ($bestseller_results as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height']);
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
				}

				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}

				if ((float)$result['special']) {
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$special = false;
				}

				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);
				} else {
					$tax = false;
				}
                if($result['weight_class_id']==1){
                    $type='кг';
                }
                if($result['weight_class_id']==2){
                    $type='гр';
                }
				$out_of_stock_badge = $result['quantity'] <= 0 ? $result['stock_status'] : 1;

				$data['bestseller_products'][] = array(
                    'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					'newstart'   => $result['date_added'],
                    'type'=>$type,
					'name'        => $result['name'],
					'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
					'price'       => $price,
					'special'     => $special,
                    'weight'=>(double)$result['weight'],
					'tax'         => $tax,
					'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id']),
					'out_of_stock_quantity'  => $result['quantity'],
                    'out_of_stock_badge'  => $out_of_stock_badge,
					'brand'      => $result['manufacturer'],
				    'brand_url'  => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $result['manufacturer_id']),
					'val1'        => preg_replace("/[^0-9.]/", "", $result['special']),
					'val2'        => preg_replace("/[^0-9.]/", "", $result['price']),
					'startDate1'  => strtotime(mb_substr($result['date_added'], 0, 10)),
					'endDate2'    => strtotime(date("Y-m-d")),
				);
			}
		}
			
			// Featured
			
			$data['featured_products'] = array();

		$products = explode(',', $this->config->get('featured_product'));

		if (empty($setting['limit'])) {
			$setting['limit'] = 4;
		}

		$products = array_slice($setting['product'], 0, (int)$setting['limit']);
		foreach ($products as $product_id) {
			$product_info = $this->model_catalog_product->getProduct($product_id);
			if ($product_info) {
				if ($product_info['image']) {
					$image = $this->model_tool_image->resize($product_info['image'],300, 400);
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', 300, 400);
				}
				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}

				if ((float)$product_info['special']) {
					$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$special = false;
				}

				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price'], $this->session->data['currency']);
				} else {
					$tax = false;
				}
				$out_of_stock_badge = $product_info['quantity'] <= 0 ? $product_info['stock_status'] : 1;
                if($product_info['weight_class_id']==1){
                    $type='кг';
                }
                if($product_info['weight_class_id']==2){
                    $type='гр';
                }

				$data['featured_products'][] = array(
                    'minimum'     => $product_info['minimum'] > 0 ? $product_info['minimum'] : 1,
					'product_id'  => $product_info['product_id'],
                    'weight'=>(double)$product_info['weight'],
					'type'=>$type,
					'thumb'       => $image,
					'newstart'   => $product_info['date_added'],
					'name'        => $product_info['name'],
					'description' => utf8_substr(trim(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
					'price'       => $price,
					'special'     => $special,
					'tax'         => $tax,
					'href'        => $this->url->link('product/product', 'product_id=' . $product_info['product_id']),
					'out_of_stock_quantity'  => $product_info['quantity'],
                    'out_of_stock_badge'  => $out_of_stock_badge,
					'brand'      => $product_info['manufacturer'],
					'brand_url'  => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $product_info['manufacturer_id']),
					'val1'        => preg_replace("/[^0-9.]/", "", $product_info['special']),
					'val2'        => preg_replace("/[^0-9.]/", "", $product_info['price']),
					'startDate1'  => strtotime(mb_substr($product_info['date_added'], 0, 10)),
					'endDate2'    => strtotime(date("Y-m-d")),
				);
			}
		}

			
			// Latest
			
			$data['latest_products'] = array();

		$filter_data = array(
			'sort'  => 'p.date_added',
			'order' => 'DESC',
			'start' => 0,
			'limit' => $setting['limit']
		);

		$latest_results = $this->model_catalog_product->getProducts($filter_data);
		if ($latest_results) {
			foreach ($latest_results as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], 300, 400);
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', 300, 400);
				}

				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}

				if ((float)$result['special']) {
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$special = false;
				}

				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);
				} else {
					$tax = false;
				}

				$out_of_stock_badge = $result['quantity'] <= 0 ? $result['stock_status'] : 1;
                if($result['weight_class_id']==1){
                    $type='кг';
                }
                if($result['weight_class_id']==2){
                    $type='гр';
                }

				$data['latest_products'][] = array(
                    'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
                    'type'=>$type,
                    'weight'=>(double)$result['weight'],
					'newstart'   => $result['date_added'],
					'name'        => $result['name'],
					'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
					'price'       => $price,
					'special'     => $special,
					'tax'         => $tax,
					'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id']),
					'out_of_stock_quantity'  => $result['quantity'],
                    'out_of_stock_badge'  => $out_of_stock_badge,
					'brand'      => $result['manufacturer'],
				    'brand_url'  => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $result['manufacturer_id']),
					'val1'        => preg_replace("/[^0-9.]/", "", $result['special']),
					'val2'        => preg_replace("/[^0-9.]/", "", $result['price']),
					'startDate1'  => strtotime(mb_substr($result['date_added'], 0, 10)),
					'endDate2'    => strtotime(date("Y-m-d")),
				);
			}
		}
			
			// Specials
			
			$data['special_products'] = array();

		$filter_data = array(
			'sort'  => 'pd.name',
			'order' => 'ASC',
			'start' => 0,
			'limit' => $setting['limit']
		);

		$special_results = $this->model_catalog_product->getProductSpecials($filter_data);

		if ($special_results) {
			foreach ($special_results as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], 300, 400);
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', 300, 400);
				}

				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}

				if ((float)$result['special']) {
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$special = false;
				}

				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);
				} else {
					$tax = false;
				}

				if ($this->config->get('config_review_status')) {
					$rating = $result['rating'];
				} else {
					$rating = false;
				}
				
				$out_of_stock_badge = $result['quantity'] <= 0 ? $result['stock_status'] : 1;
                if($result['weight_class_id']==1){
                    $type='кг';
                }
                if($result['weight_class_id']==2){
                    $type='гр';
                }
				$data['special_products'][] = array(
                    'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
                    'weight'=>(double)$result['weight'],
                    'type'=>$type,
					'newstart'   => $result['date_added'],
					'name'        => $result['name'],
					'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
					'price'       => $price,
					'special'     => $special,
					'tax'         => $tax,
					'rating'      => $rating,
					'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id']),
					'out_of_stock_quantity'  => $result['quantity'],
                    'out_of_stock_badge'  => $out_of_stock_badge,
					'brand'      => $result['manufacturer'],
				    'brand_url'  => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $result['manufacturer_id']),
					'val1'        => preg_replace("/[^0-9.]/", "", $result['special']),
					'val2'        => preg_replace("/[^0-9.]/", "", $result['price']),
					'startDate1'  => strtotime(mb_substr($result['date_added'], 0, 10)),
					'endDate2'    => strtotime(date("Y-m-d")),
				);
			}
		}
		    
			// Most Viewed
			
			$data['most_viewed_products'] = array();
		
		$query = $this->db->query("SELECT p.product_id FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' ORDER BY p.viewed DESC LIMIT " . (int)$setting['limit']);
		
		foreach ($query->rows as $result) { 		
			$product_data[$result['product_id']] = $this->model_catalog_product->getProduct($result['product_id']);
		}

		$results = $product_data;

		if ($results) {
			foreach ($results as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], 300, 400);
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', 300, 400);
				}

				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}

				if ((float)$result['special']) {
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$special = false;
				}

				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);
				} else {
					$tax = false;
				}
                if($result['weight_class_id']==1){
                    $type='кг';
                }
                if($result['weight_class_id']==2){
                    $type='гр';
                }

				$out_of_stock_badge = $result['quantity'] <= 0 ? $result['stock_status'] : 1;

				$data['most_viewed_products'][] = array(
                    'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
                    'weight'=>(double)$result['weight'],
                    'type'=>$type,
					'newstart'   => $result['date_added'],
					'name'        => $result['name'],
					'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
					'price'       => $price,
					'special'     => $special,
					'tax'         => $tax,
					'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id']),
					'out_of_stock_quantity'  => $result['quantity'],
                    'out_of_stock_badge'  => $out_of_stock_badge,
					'brand'      => $result['manufacturer'],
				    'brand_url'  => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $result['manufacturer_id']),
					'val1'        => preg_replace("/[^0-9.]/", "", $result['special']),
					'val2'        => preg_replace("/[^0-9.]/", "", $result['price']),
					'startDate1'  => strtotime(mb_substr($result['date_added'], 0, 10)),
					'endDate2'    => strtotime(date("Y-m-d")),
				);
			}
		}
			
            $data['module'] = $module++;
			
			return $this->load->view('extension/module/theme_product_tabs', $data);
		}
	}
	