<?php
class ControllerExtensionModuleSlideshow extends Controller {
	public function index($setting) {
		static $module = 0;
		$this->load->model('design/banner');
		$this->load->model('tool/image');
        $banners=$setting['banners'];
        $this->load->model('tool/image');
		foreach ($banners as $result) {
			if (is_file(DIR_IMAGE . $result['image'])) {
				$data['banners'][] = array(
					'title' => htmlspecialchars_decode($result['title']),
					'description'  => htmlspecialchars_decode($result['description']),
					'image' => $this->model_tool_image->resize($result['image'],1600,700)
				);
			}
		}

		$data['module'] = $module++;

		return $this->load->view('extension/module/slideshow', $data);
	}
}