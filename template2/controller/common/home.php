<?php

class ControllerCommonHome extends PT_Controller
{
    public function index()
    {
        
        $this->load->language('common/home');

        $data['header'] = $this->load->controller('common/header');
        $data['nav'] = $this->load->controller('common/nav');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('common/home', $data));
    }

}
