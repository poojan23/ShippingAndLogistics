<?php

class ControllerCustomerLogout extends PT_Controller
{
    public function index()
    {
        $this->customer->logout();

        unset($this->session->data['customer_id']);

        $this->response->redirect($this->url->link('customer/login'));
    }
}
