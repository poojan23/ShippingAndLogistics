<?php

class ControllerCustomerForgotten extends PT_Controller {

    private $error = array();

    public function index() {
        if ($this->customer->isLogged()) {
            $this->response->redirect($this->url->link('customer/dashboard', '', true));
        }

        if (!$this->config->get('config_password')) {
            $this->response->redirect($this->url->link('customer/login', '', true));
        }

        $this->load->language('customer/forgotten');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('customer/login');
         $this->document->addScript('https://maps.googleapis.com/maps/api/js');
        $this->document->addScript('template/view/dist/js/gmaps.js');

        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $message = 'It seems like You have forgotten your password. <a href="http://localhost/ShippingAndLogistics/index.php?url=customer/forgotten?&email=' . $this->request->post['email'] . '" target="blank">Click here</a>, to reset your password.';
            $emailid= 'info@popaya.in';
            $mail = new Mail($this->config->get('config_mail_engine'));
            $mail->parameter = $this->config->get('config_mail_parameter');
            $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
            $mail->smtp_username = $this->config->get('config_mail_smtp_username');
            $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
            $mail->smtp_port = $this->config->get('config_mail_smtp_port');
            $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

            $mail->setTo($this->config->get('config_email'));
            $mail->setFrom($this->config->get('config_email'));
            $mail->setReplyTo($emailid);
            $mail->setSender(html_entity_decode($this->request->post['email'], ENT_QUOTES, 'UTF-8'));
            $mail->setSubject(html_entity_decode(sprintf($this->request->post['email']), ENT_QUOTES, 'UTF-8'));
            $mail->setText($message);
            $mail->send();

//            $this->response->redirect($this->url->link('information/careers/success'));

            
            
//            $this->model_customer_login->editCode($this->request->post['email']);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('customer/login', '', true));
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('customer/dashboard', '', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('customer/forgotten', '', true)
        );

        $data['action'] = $this->url->link('customer/forgotten', '', true);

        $data['cancel'] = $this->url->link('customer/login', '', true);

        if (isset($this->request->post['email'])) {
            $data['email'] = $this->request->post['email'];
        } else {
            $data['email'] = '';
        }

        $data['header'] = $this->load->controller('common/header');
        $data['nav'] = $this->load->controller('common/nav');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('customer/forgotten', $data));
    }

    protected function validate() {
        if (!isset($this->request->post['email'])) {
            $this->error['warning'] = $this->language->get('error_email');
        } elseif (!$this->model_customer_login->getTotalCustomersByEmail($this->request->post['email'])) {
            $this->error['warning'] = $this->language->get('error_email');
        }

        return !$this->error;
    }

}
