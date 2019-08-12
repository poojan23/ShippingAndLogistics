<?php

class ControllerClubProfile extends PT_Controller {

    public function index() {
        $this->load->language('club/profile');

        $this->document->setTitle($this->language->get('heading_title'));

        if (!$this->customer->isLogged()) {
            $this->response->redirect($this->url->link('club/login'));
        }


        $this->getForm();
    }
    public function edit()
    {
        $this->load->language('club/profile');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('club/profile');

        $data['club_id'] = $this->customer->getId();

        // if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

            $this->model_club_profile->editProfile($data['club_id'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('club/profile'));
        }
    }

    protected function getForm() {

        $data['club_id'] = $this->customer->getId();

        $data['text_form'] = !isset($data['club_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

        $this->load->model('club/profile');
        // form validtion
        if (isset($this->error['warning'])) {
            $data['warning_err'] = $this->error['warning'];
        } else {
            $data['warning_err'] = '';
        }

        if (isset($this->error['website'])) {
            $data['website_err'] = $this->error['website'];
        } else {
            $data['website_err'] = '';
        }

        if (isset($this->error['mobile'])) {
            $data['mobile_err'] = $this->error['mobile'];
        } else {
            $data['mobile_err'] = '';
        }

        if (isset($this->error['password'])) {
            $data['password_err'] = $this->error['password'];
        } else {
            $data['password_err'] = '';
        }

        if (isset($this->error['confirm'])) {
            $data['confirm_err'] = $this->error['confirm'];
        } else {
            $data['confirm_err'] = '';
        }
       
        //breadcrumbs
        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('club/member')
        );

        $data['action'] = $this->url->link('club/profile/edit');

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_edit'),
            'href' => $this->url->link('club/member/edit')
        );
    

        // Get form data
        $profile_info = $this->model_club_profile->getProfile($data['club_id']);

        if (isset($this->request->post['website'])) {
            $data['website'] = $this->request->post['website'];
        } elseif (!empty($profile_info)) {
            $data['website'] = $profile_info['website'];
        } else {
            $data['website'] = '';
        }

         if (isset($this->request->post['mobile'])) {
            $data['mobile'] = $this->request->post['mobile'];
        } elseif (!empty($profile_info)) {
            $data['mobile'] = $profile_info['mobile'];
        } else {
            $data['mobile'] = '';
        }

         if (isset($this->request->post['password'])) {
            $data['password'] = $this->request->post['password'];
        } elseif (!empty($profile_info)) {
            $data['password'] = $profile_info['password'];
        } else {
            $data['password'] = '';
        }
        
        //login session check
        if (!$this->customer->isLogged()) {
            $this->response->redirect($this->url->link('club/login'));
        }

        # session data
        
        $data['club_id'] = $this->customer->getId();
        $data['club_name'] = $this->customer->getFirstName();
        $data['date'] = $this->customer->getDate();
        $data['mobile'] = $this->customer->getMobile();
        $data['email'] = $this->customer->getEmail();
        $data['image'] = $this->customer->getImage();
        $data['president'] = $this->customer->getPresident();
        $data['assistant_governor'] = $this->customer->getAssistant();
        $data['district_secretary'] = $this->customer->getDistrict();
        
        $this->load->model('tool/image');

        $data['placeholder'] = $this->model_tool_image->resize('no-image.png', 100, 100);

        if (is_file(DIR_IMAGE . html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8'))) {
            $data['thumb'] = $this->model_tool_image->resize(html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8'), 100, 100);
        } else {
            $data['thumb'] = $data['placeholder'];
        }

        # page menu link
        $data['continue'] = $this->url->link('common/home');
        $data['add_member'] = $this->url->link('club/member/add');
        $data['dashboard'] = $this->url->link('club/dashboard');
        $data['project'] = $this->url->link('club/project');
        $data['trf'] = $this->url->link('club/trf');
        $data['member'] = $this->url->link('club/member');
        $data['profile'] = $this->url->link('club/profile');
        $data['logout'] = $this->url->link('club/logout');

        $data['header'] = $this->load->controller('common/header');
        $data['nav'] = $this->load->controller('common/nav');
        $data['navpage'] = $this->load->controller('common/navpage');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('club/profile_form', $data));
    }

    // protected function validateForm()
    // {
    //     if (!$this->user->hasPermission('modify', 'club/profile')) {
    //         $this->error['warning'] = $this->language->get('error_permission');
    //     }
    //     $data['club_id'] = $this->customer->getId();

    //     if ((utf8_strlen(trim($this->request->post['mobile'])) < 1) || (utf8_strlen(trim($this->request->post['mobile'])) > 11)) {
    //         $this->error['mobile'] = $this->language->get('mobile_err');
    //     }

    //     if ($this->request->post['password'] || (!$data['club_id'])) {
    //         if ((utf8_strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) < 4) || (utf8_strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) > 40)) {
    //             $this->error['password'] = $this->language->get('password_err');
    //         }
    //     }
        
    //     if ((utf8_strlen(trim($this->request->post['website'])) < 1) || (utf8_strlen(trim($this->request->post['website'])) > 32)) {
    //         $this->error['website'] = $this->language->get('website_err');
    //     }

    //      if ($this->request->post['password']) {
    //         if ((utf8_strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) < 4) || (utf8_strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) > 40)) {
    //             $this->error['password'] = $this->language->get('password_err');
    //         }

    //         if ($this->request->post['password'] != $this->request->post['confirm']) {
    //             $this->error['confirm'] = $this->language->get('confirm_err');
    //         }
    //     }
    //     return !$this->error;
    // }

}
