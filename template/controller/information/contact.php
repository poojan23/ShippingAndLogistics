<?php

class ControllerInformationContact extends PT_Controller
{
    public function index()
    {
        $this->load->language('information/contact');

        $this->document->setTitle($this->language->get('heading_title'));
        
         $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text'  => $this->language->get('text_home'),
            'href'  => $this->url->link('common/home')
        );

        $data['breadcrumbs'][] = array(
            'text'  => $this->language->get('heading_title'),
            'href'  => $this->url->link('information/contact')
        );
        
        $this->load->model('tool/image');

        # Projects
        $this->load->model('design/banner');

        $data['projects'] = array();

        $results = $this->model_design_banner->getBanner(3, 0, 8);

        foreach ($results as $result) {
            if (is_file(DIR_IMAGE . html_entity_decode($result['image'], ENT_QUOTES, 'UTF-8'))) {
                $data['projects'][] = array(
                    'title' => $result['title'],
                    'image' => $this->model_tool_image->resize(html_entity_decode($result['image'], ENT_QUOTES, 'UTF-8'), 750, 1335)
                );
            }
        }

        # Team
        $this->load->model('catalog/team');

        $data['teams'] = array();

        $results = $this->model_catalog_team->getTeams(0, 6);

        foreach ($results as $result) {
            if ($result['image']) {
                $thumb = $this->model_tool_image->resize($result['image'], 400, 500);
            } else {
                $thumb = $this->model_tool_image->resize('default-image.png', 400, 500);
            }

            $data['teams'][] = array(
                'name'          => $result['name'],
                'designation'   => $result['designation'],
                'thumb'         => $thumb
            );
        }

        # Watch (See Our Work Showcase)

        # Testimonials
        $this->load->model('catalog/testimonial');

        $data['testimonials'] = array();

        $results = $this->model_catalog_testimonial->getTestimonials(0, 6);

        foreach ($results as $result) {
            if ($result['image']) {
                $thumb = $this->model_tool_image->resize($result['image'], 150, 150);
            } else {
                $thumb = $this->model_tool_image->resize('default-image.png', 150, 150);
            }

            $data['testimonials'][] = array(
                'name'          => $result['name'],
                'description'   => trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))),
                'designation'   => $result['designation'],
                'thumb'         => $thumb
            );
        }

        # Facts
        $this->load->model('tool/online');

        $data['website_icon'] = $this->config->get('config_website_icon');
        $data['website'] = $this->config->get('config_website');

        $data['software_icon'] = $this->config->get('config_software_icon');
        $data['software'] = $this->config->get('config_software');

        $data['client_icon'] = $this->config->get('config_client_icon');
        $data['client'] = $this->config->get('config_client');

//        $data['visitor_icon'] = $this->config->get('config_visitor_icon');
//        $data['visitor'] = ($this->model_tool_online->getTotalOnlines() > 9999) ? '9999' : $this->model_tool_online->getTotalOnlines();

        # Blog

        # Contact
        $data['address'] = nl2br($this->config->get('config_address'));
        $data['telephone'] = $this->config->get('config_telephone');
        $data['email'] = $this->config->get('config_email');
        $data['open'] = preg_replace("/^(.*)<br.*\/?>/m", '<p>$1</p><p>', nl2br($this->config->get('config_open')));

        $data['header'] = $this->load->controller('common/header');
        $data['nav'] = $this->load->controller('common/nav');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('information/contact', $data));
    }

    public function send()
    {
        $this->load->language('common/home');

        $json = array();

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 32)) {
                $json['error']['name'] = $this->language->get('error_name');
            }

            if (!filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
                $json['error']['email'] = $this->language->get('error_email');
            }

            if ((utf8_strlen($this->request->post['message']) < 10) || (utf8_strlen($this->request->post['message']) > 3000)) {
                $json['error']['message'] = $this->language->get('error_message');
            }

            if (!$json) {
                $json['success'] = $this->language->get('text_success');

                if (filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
                    $mail = new Mail($this->config->get('config_mail_engine'));
                    $mail->parameter = $this->config->get('config_mail_parameter');
                    $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
                    $mail->smtp_username = $this->config->get('config_mail_smtp_username');
                    $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
                    $mail->smtp_port = $this->config->get('config_mail_smtp_port');
                    $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

                    $mail->setTo($this->config->get('config_email'));
                    $mail->setFrom($this->request->post['email']);
                    $mail->setReplyTo($this->request->post['email']);
                    $mail->setSender(html_entity_decode($this->request->post['name'], ENT_QUOTES, 'UTF-8'));
                    $mail->setSubject(html_entity_decode(sprintf($this->language->get('email_subject'), $this->request->post['name']), ENT_QUOTES, 'UTF-8'));
                    $mail->setText($this->request->post['message']);
                    $mail->send();
                }
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}
