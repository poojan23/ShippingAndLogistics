<?php

class ControllerClubMember extends PT_Controller {

    public function index() {
        $this->load->language('club/member');

        $this->document->setTitle($this->language->get('heading_title'));

        if (!$this->customer->isLogged()) {
            $this->response->redirect($this->url->link('club/login'));
        }

        $this->getList();
    }

    public function add() {
        $this->load->language('club/member');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('club/member');

        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $this->model_club_member->addMember($this->request->post);

            $this->response->redirect($this->url->link('club/member'));
        }

        $this->getForm();
    }

    public function view() {

         
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

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('club/member')
        );

        $data['cancel'] = $this->url->link('club/member');


        $data['continue'] = $this->url->link('common/home');
        $data['add_member'] = $this->url->link('club/member/add');
        $data['dashboard'] = $this->url->link('club/dashboard');
        $data['project'] = $this->url->link('club/project');
        $data['trf'] = $this->url->link('club/trf');
        $data['member'] = $this->url->link('club/member');
        $data['profile'] = $this->url->link('club/profile');
        $data['logout'] = $this->url->link('club/logout');

        $this->load->language('club/member');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('club/member');

        if (isset($this->request->get['member_id'])) {
            $member_info = $this->model_club_member->getMember($this->request->get['member_id']);
        }
 
//        $data['month'] = $member_info['month'];
//        $data['year'] = $member_info['year'];
        $date = $member_info['date'];
    
        $date = explode('-', $date);

        $data['year'] = $date[0];
        $data['month']   = $date[1];

        $data['member_induct'] = $member_info['induction'];
        $data['member_unlist'] = $member_info['unlist'];
        $data['net_growth'] = $member_info['net'];
        $data['point_accumulate'] = $member_info['points'];

        $data['header'] = $this->load->controller('common/header');
        $data['nav'] = $this->load->controller('common/nav');
        $data['navpage'] = $this->load->controller('common/navpage');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('club/view_member', $data));
    }

    protected function getList() {
        $this->load->model('club/member');



        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('club/member')
        );

        
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

        $data['cancel'] = $this->url->link('club/dashboard');

        $data['add'] = $this->url->link('club/member/add');

        
        $data['members'] = array();

        $club_id = $this->customer->getId();

        $results = $this->model_club_member->getMemberById($club_id);
        // print_r($results);exit;
        foreach ($results as $result) {
            $data['members'][] = array(
                'member_id' => $result['member_id'],
                'date' => $result['date'],
                'induction' => $result['induction'],
                'unlist' => $result['unlist'],
                'net' => $result['net'],
                'points' => $result['points'],
                'view' => $this->url->link('club/member/view', 'member_id=' . $result['member_id'])
            );
        }

        if (isset($this->error['warning'])) {
            $data['warning_err'] = $this->error['warning'];
        } else {
            $data['warning_err'] = '';
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        if (isset($this->request->post['selected'])) {
            $data['selected'] = (array) $this->request->post['selected'];
        } else {
            $data['selected'] = array();
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('club/member')
        );



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

        $this->response->setOutput($this->load->view('club/member', $data));
    }

    protected function getForm() {
        $data['text_form'] = !isset($this->request->get['member_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

        $club_id = $this->customer->getId();

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

        if (isset($this->error['warning'])) {
            $data['warning_err'] = $this->error['warning'];
        } else {
            $data['warning_err'] = '';
        }

        if (isset($this->error['month'])) {
            $data['month_err'] = $this->error['month'];
        } else {
            $data['month_err'] = '';
        }
        if (isset($this->error['year'])) {
            $data['year_err'] = $this->error['year'];
        } else {
            $data['year_err'] = '';
        }
        if (isset($this->error['member_induct'])) {
            $data['member_induct_err'] = $this->error['member_induct'];
        } else {
            $data['member_induct_err'] = '';
        }

        if (isset($this->error['member_unlist'])) {
            $data['member_unlist_err'] = $this->error['member_unlist'];
        } else {
            $data['member_unlist_err'] = '';
        }
        if (isset($this->error['net_growth'])) {
            $data['net_growth_err'] = $this->error['net_growth'];
        } else {
            $data['net_growth_err'] = '';
        }
        if (isset($this->error['point_accumulate'])) {
            $data['point_accumulate_err'] = $this->error['point_accumulate'];
        } else {
            $data['point_accumulate_err'] = '';
        }
        if (isset($this->error['club_id'])) {
            $data['club_id_err'] = $this->error['club_id'];
        } else {
            $data['club_id_err'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('club/member')
        );

        if (!isset($this->request->get['member_id'])) {
            $data['action'] = $this->url->link('club/member/add');
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_add'),
                'href' => $this->url->link('club/member/add')
            );
        } else {
            $data['action'] = $this->url->link('club/member/edit' . '&member_id=' . $this->request->get['member_id']);
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_edit'),
                'href' => $this->url->link('club/member/edit')
            );
        }

        $data['cancel'] = $this->url->link('club/member');

        if (isset($this->request->get['member_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $member_info = $this->model_club_member->getMember($this->request->get['member_id']);
        }

        if (isset($this->request->post['month'])) {
            $data['month'] = $this->request->post['month'];
        } elseif (!empty($member_info)) {
            $data['month'] = $member_info['month'];
        } else {
            $data['month'] = '';
        }

        if (isset($this->request->post['year'])) {
            $data['year'] = $this->request->post['year'];
        } elseif (!empty($member_info)) {
            $data['year'] = $member_info['year'];
        } else {
            $data['year'] = '';
        }

        if (isset($this->request->post['member_induct'])) {
            $data['member_induct'] = $this->request->post['member_induct'];
        } elseif (!empty($member_info)) {
            $data['member_induct'] = $member_info['member_induct'];
        } else {
            $data['member_induct'] = '';
        }

        if (isset($this->request->post['member_unlist'])) {
            $data['member_unlist'] = $this->request->post['member_unlist'];
        } elseif (!empty($member_info)) {
            $data['member_unlist'] = $member_info['member_unlist'];
        } else {
            $data['member_unlist'] = '';
        }
        if (isset($this->request->post['net_growth'])) {
            $data['net_growth'] = $this->request->post['net_growth'];
        } elseif (!empty($member_info)) {
            $data['net_growth'] = $member_info['net_growth'];
        } else {
            $data['net_growth'] = '';
        }
        if (isset($this->request->post['point_accumulate'])) {
            $data['point_accumulate'] = $this->request->post['point_accumulate'];
        } elseif (!empty($member_info)) {
            $data['point_accumulate'] = $member_info['point_accumulate'];
        } else {
            $data['point_accumulate'] = '';
        }


        if (!$this->customer->isLogged()) {
            $this->response->redirect($this->url->link('club/login'));
        }

        $data['total_members'] = $this->model_club_member->getTotalMemberById($club_id);

        // print_r($data['total_members']);

        // print_r($data['total_members']); exit;

       

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

        $this->response->setOutput($this->load->view('club/member_form', $data));
    }

}
