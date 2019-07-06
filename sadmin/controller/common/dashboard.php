<?php

class ControllerCommonDashboard extends PT_Controller
{
    public function index()
    {
        $this->load->language('common/dashboard');

        $this->document->setTitle($this->language->get('heading_title'));
        
        $this->load->model('catalog/enquiry');
        
        $data['enquiries'] = array();

        $result_enquiries = $this->model_catalog_enquiry->getTopEnquiries();
        
        foreach ($result_enquiries as $result) {
            $data['enquiries'][] = array(
                'enquiry_id'    => $result['enquiry_id'],
                'name'          => $result['name'],
                'email'         => $result['email'],
                'date_added'    => date("d-m-Y", strtotime($result['date_added']) )
            );
        }
        
        $this->load->model('catalog/service');
        
        $data['services'] = array();

        $result_services = $this->model_catalog_service->getServices();

        foreach ($result_services as $result) {
            $data['services'][] = array(
                'service_id'  => $result['service_id'],
                'name'        => $result['name'],
                'sort_order'  => $result['sort_order']
            );
        }
        
        $data['website']    = $this->config->get('config_website');
        $data['software']   = $this->config->get('config_software');
        $data['client']     = $this->config->get('config_client');
        
        $data['header']     = $this->load->controller('common/header');
        $data['nav']        = $this->load->controller('common/nav');
        $data['footer']     = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('common/dashboard', $data));
    }
}
