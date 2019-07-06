<?php

class ControllerCommonDashboard extends PT_Controller
{
    public function index()
    {
        $this->load->language('common/dashboard');

        $this->document->setTitle($this->language->get('heading_title'));
        
        $data['header']     = $this->load->controller('common/header');
        $data['nav']        = $this->load->controller('common/nav');
        $data['footer']     = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('common/dashboard', $data));
    }
}
