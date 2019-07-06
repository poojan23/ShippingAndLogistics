<?php

class ControllerLaunchStep2 extends PT_Controller
{
    private $error = array();

    public function index()
    {
        $this->load->language('launch/step_2');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->response->redirect($this->url->link('launch/step_3'));
        }

        $this->document->setTitle($this->language->get('heading_title'));

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_step_2'] = $this->language->get('text_step_2');
        $data['text_app'] = $this->language->get('text_app');

        $data['entry_app'] = $this->language->get('entry_app');
        $data['entry_email'] = $this->language->get('entry_email');

        $data['button_continue'] = $this->language->get('button_continue');
        $data['button_back'] = $this->language->get('button_back');

        if (isset($this->error['warning'])) {
            $data['warning_err'] = $this->error['warning'];
        } else {
            $data['warning_err'] = '';
        }

        if (isset($this->error['app_key'])) {
            $data['app_key_err'] = $this->error['app_key'];
        } else {
            $data['app_key_err'] = '';
        }

        if (isset($this->error['email'])) {
            $data['email_err'] = $this->error['email'];
        } else {
            $data['email_err'] = '';
        }

        $data['action'] = $this->url->link('launch/step_2');

        if (isset($this->request->post['app_key'])) {
            $data['app_key'] = $this->request->post['app_key'];
        } else {
            $data['app_key'] = '';
        }

        if (isset($this->request->post['email'])) {
            $data['email'] = $this->request->post['email'];
        } else {
            $data['email'] = '';
        }

        $data['back'] = $this->url->link('launch/step_1');

        $data['header'] = $this->load->controller('common/header');
        $data['nav'] = $this->load->controller('common/nav');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('launch/step_2', $data));
    }

    private function validate()
    {
        if (!$this->request->post['app_key']) {
            $this->error['app_key'] = $this->language->get('error_app_key');
        } else {
            if (utf8_strlen(str_replace('-', '', $this->request->post['app_key'])) != 17) {
                $this->error['app_key'] = $this->language->get('error_app_key_len');
            }
        }

        if (utf8_strlen($this->request->post['email']) > 96 || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
            $this->error['email'] = $this->language->get('error_email');
        }

        return !$this->error;
    }
}
