<?php

class ControllerInstallStep3 extends PT_Controller
{
    private $error = array();

    public function index()
    {
        $this->load->language('install/step_3');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->response->redirect($this->url->link('install/step_4'));
        }

        $this->document->setTitle($this->language->get('heading_title'));

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_step_3'] = $this->language->get('text_step_3');
        $data['text_authenticate'] = $this->language->get('text_authenticate');

        $data['entry_auth_code'] = $this->language->get('entry_auth_code');
        $data['entry_auth_email'] = $this->language->get('entry_auth_email');

        $data['button_continue'] = $this->language->get('button_continue');
        $data['button_back'] = $this->language->get('button_back');

        if (isset($this->error['warning'])) {
            $data['warning_err'] = $this->error['warning'];
        } else {
            $data['warning_err'] = '';
        }

        if (isset($this->error['auth_code'])) {
            $data['auth_code_err'] = $this->error['auth_code'];
        } else {
            $data['auth_code_err'] = '';
        }

        if (isset($this->error['auth_email'])) {
            $data['auth_email_err'] = $this->error['auth_email'];
        } else {
            $data['auth_email_err'] = '';
        }

        $data['action'] = $this->url->link('install/step_3');

        if (isset($this->request->post['auth_code'])) {
            $data['auth_code'] = $this->request->post['auth_code'];
        } else {
            $data['auth_code'] = '';
        }

        if (isset($this->request->post['auth_email'])) {
            $data['auth_email'] = $this->request->post['auth_email'];
        } else {
            $data['auth_email'] = '';
        }

        $data['header'] = $this->load->controller('common/header');
        $data['nav'] = $this->load->controller('common/nav');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('install/step_3', $data));
    }

    private function validate()
    {
        if (!$this->request->post['auth_code']) {
            $this->error['auth_code'] = $this->language->get('error_auth_code');
        } else {
            if (utf8_strlen(str_replace('-', '', $this->request->post['auth_code'])) != 17) {
                $this->error['auth_code'] = $this->language->get('error_auth_len');
            }
        }

        if ((utf8_strlen($this->request->post['auth_email']) > 96) || !filter_var($this->request->post['auth_email'], FILTER_VALIDATE_EMAIL)) {
            $this->error['auth_email'] = $this->language->get('error_auth_email');
        }

        if ($this->error && !isset($this->error['warning'])) {
            $this->error['warning'] = $this->language->get('error_warning');
        }

        return !$this->error;
    }
}
