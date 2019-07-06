<?php

class ControllerLaunchStep4 extends PT_Controller
{
    public function index()
    {
        $this->load->language('launch/step_4');

        $this->document->setTitle($this->language->get('heading_title'));

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_step_4'] = $this->language->get('text_step_4');
        $data['text_catalog'] = $this->language->get('text_catalog');
        $data['text_admin'] = $this->language->get('text_admin');
        $data['text_popaya'] = $this->language->get('text_popaya');
        $data['text_facebook'] = $this->language->get('text_facebook');
        $data['text_facebook_description'] = $this->language->get('text_facebook_description');
        $data['text_facebook_visit'] = $this->language->get('text_facebook_visit');
        $data['text_linkedin'] = $this->language->get('text_linkedin');
        $data['text_linkedin_description'] = $this->language->get('text_linkedin_description');
        $data['text_linkedin_visit'] = $this->language->get('text_linkedin_visit');

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        $data['header'] = $this->load->controller('common/header');
        $data['nav'] = $this->load->controller('common/nav');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('launch/step_4', $data));
    }
}
