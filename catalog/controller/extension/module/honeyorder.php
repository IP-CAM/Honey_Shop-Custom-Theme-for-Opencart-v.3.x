<?php
class ControllerExtensionModuleHoneyOrder extends Controller {
    public function index($setting) {
        $this->load->language('extension/module/honeyorder');
        $this->load->model('tool/image');
        $orders=$setting['orders'];
        if(isset($orders)){
            foreach ($orders as $order){
                $data['orders'][]=array(
                    'title'=>$order['title'],
                    'icon'=>$this->model_tool_image->resize($order['icon'],300,300),
                );
            }
            return $this->load->view('extension/module/honeyorder', $data);
        }
    }
}