<?php

class ControllerTrackingTracking extends PT_Controller
{
    public function index()
    {
        $this->load->language('tracking/tracking');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('tracking/tracking');

        // login session
        if (!$this->customer->isLogged()) {
            $this->response->redirect($this->url->link('customer/login'));
        }      

        $data['customer_id'] = $this->customer->getId();
        $data['customer_name'] = $this->customer->getFirstName();
        $data['area_id'] = $this->customer->getAreaId();
        $data['mobile'] = $this->customer->getMobile();
        $data['email'] = $this->customer->getEmail();

        $data['header'] = $this->load->controller('common/header');
        $data['nav'] = $this->load->controller('common/nav');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('tracking/tracking', $data));
    }
}
