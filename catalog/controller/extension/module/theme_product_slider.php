<?php
class ControllerExtensionModuleThemeProductSlider extends Controller {
	public function index($setting) {
		static $module = 0;
		
		$this->load->language('extension/module/theme_product_slider');
		
		$this->document->addScript('catalog/view/theme/oxy/js/jquery.eislideshow.js');
		$this->document->addStyle('catalog/view/theme/oxy/stylesheet/elastic_slideshow.css');

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_tax'] = $this->language->get('text_tax');

		$data['button_cart'] = $this->language->get('button_cart');
		$data['button_wishlist'] = $this->language->get('button_wishlist');
		
		$data['lang_id'] = $this->config->get('config_language_id');
		$lang_id = $this->config->get('config_language_id');
		
		$data['t1o_text_shop_now'] = $this->config->get('t1o_text_shop_now');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		$data['products'] = array();

		$products = explode(',', $this->config->get('theme_product_slider_product'));

		if (empty($setting['limit'])) {
			$setting['limit'] = 4;
		}

		$products = array_slice($setting['product'], 0, (int)$setting['limit']);

		foreach ($products as $product_id) {
			$product_info = $this->model_catalog_product->getProduct($product_id);

			if ($product_info) {
				if ($product_info['image']) {
					$image = $this->model_tool_image->resize($product_info['image']);
				} else {
					$image = $this->model_tool_image->resize('placeholder.png');
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
				$data['products'][] = array(
					'product_id'  => $product_info['product_id'],
					'thumb'       => $image,
					'name'        => $product_info['name'],
					'description' => utf8_substr(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8')), 0, 300) . '..',
					'price'       => $price,
					'special'     => $special,
					'href'        => $this->url->link('product/product', 'product_id=' . $product_info['product_id'])
				);
			}
		}
		
		$data['module'] = $module++;
		
		if ($data['products']) {
			return $this->load->view('extension/module/theme_product_slider', $data);
		}
	}
}
