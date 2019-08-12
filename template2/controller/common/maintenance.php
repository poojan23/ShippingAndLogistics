<?php

class ControllerCommonMaintenance extends PT_Controller
{
    public function index()
    {
        $this->load->language('common/maintenance');

        $this->document->setTitle($this->language->get('heading_title'));

        if ($this->request->server['SERVER_PROTOCOL'] == 'HTTP/1.1') {
            $this->response->addHeader('HTTP/1.1 503 Service Unavailable');
        } else {
            $this->response->addHeader('HTTP/1.0 503 Service Unavailable');
        }

        $this->response->addHeader('Retry-After: 3600');

        $data['title'] = $this->language->get('text_title');
        $data['maintenance'] = $this->language->get('text_maintenance');
        $data['message'] = $this->language->get('text_message');

        $data['home'] = $this->url->link('common/home');

        $data['header'] = $this->load->controller('common/header');
        $data['nav'] = $this->load->controller('common/nav');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('common/maintenance', $data));
    }
}
