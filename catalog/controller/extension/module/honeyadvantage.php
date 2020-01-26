<?php
class ControllerExtensionModuleHoneyAdvantage extends Controller {
    public function index($setting) {
        $this->load->language('extension/module/honeyadvantage');
        $this->load->model('tool/image');
        $advantages=$setting['advantages'];
        if(isset($advantages)){
            foreach ($advantages as $advantage){
                $data['advantages'][]=array(
                    'title'=>htmlspecialchars_decode($advantage['title']),
                    'image'=>$this->model_tool_image->resize($advantage['image'],400,400),
                    'description'=>htmlspecialchars_decode($advantage['description']),
                );
            }
            return $this->load->view('extension/module/honeyadvantage', $data);
        }
    }
}