<?php

class ControllerCustomerDashboard extends PT_Controller
{
    public function index()
    {
        $this->load->language('customer/dashboard');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('customer/dashboard');

        // login session
        if (!$this->customer->isLogged()) {
            $this->response->redirect($this->url->link('customer/login'));
        }      
        
        

        $data['customer_id'] = $this->customer->getId();
        $data['customer_name'] = $this->customer->getFirstName();
        $data['area_id'] = $this->customer->getAreaId();
        $data['mobile'] = $this->customer->getMobile();
        $data['email'] = $this->customer->getEmail();
        // print_r($data);

        $this->response->setOutput($this->load->view('customer/dashboard', $data));
    }
}
