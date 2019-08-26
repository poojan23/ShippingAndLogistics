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

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_dashboard'),
            'href' => $this->url->link('common/home')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('club/member')
        );

        $data['customer_id'] = $this->customer->getId();
        $data['customer_name'] = $this->customer->getFirstName();
        $data['area_id'] = $this->customer->getAreaId();
        $data['mobile'] = $this->customer->getMobile();
        $data['email'] = $this->customer->getEmail();

        $data['header'] = $this->load->controller('common/header');
        $data['nav'] = $this->load->controller('common/nav');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('customer/dashboard', $data));
    }
}
