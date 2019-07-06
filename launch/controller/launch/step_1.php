<?php

class ControllerLaunchStep1 extends PT_Controller
{
    public function index()
    {
        $this->load->language('launch/step_1');

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $this->response->redirect($this->url->link('launch/step_2'));
        }

        $this->document->setTitle($this->language->get('heading_title'));

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_step_1'] = $this->language->get('text_step_1');
        $data['text_terms'] = $this->language->get('text_terms');

        $data['button_continue'] = $this->language->get('button_continue');

        $data['action'] = $this->url->link('launch/step_1');

        $data['header'] = $this->load->controller('common/header');
        $data['nav'] = $this->load->controller('common/nav');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('launch/step_1', $data));
    }
}
