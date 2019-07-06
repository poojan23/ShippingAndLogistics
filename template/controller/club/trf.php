<?php

class ControllerClubTrf extends PT_Controller {

    public function index() {
        $this->load->language('club/trf');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('club/trf');

        if (!$this->customer->isLogged()) {
            $this->response->redirect($this->url->link('club/login'));
        }

        $this->getList();
    }

    public function add() {
        $this->load->language('club/trf');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('club/trf');

        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $this->model_club_trf->addTrf($this->request->post);

            $this->response->redirect($this->url->link('club/trf'));
        }

        $this->getForm();
    }

    public function view() {
        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('club/trf')
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
        $data['cancel'] = $this->url->link('club/trf');
        $data['continue'] = $this->url->link('common/home');
        $data['add_trf'] = $this->url->link('club/trf/add');
        $data['dashboard'] = $this->url->link('club/dashboard');
        $data['member'] = $this->url->link('club/member');
        $data['project'] = $this->url->link('club/project');
        $data['trf'] = $this->url->link('club/trf');
        $data['profile'] = $this->url->link('club/profile');
        $data['logout'] = $this->url->link('club/logout');

        $this->load->language('club/trf');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('club/trf');

             if (isset($this->request->get['trf_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
                $trf_info = $this->model_club_trf->getTrf($this->request->get['trf_id']);
        }
       
        $date = $trf_info['date'];
        $date = explode('-', $date);
        $data['year'] = $date[0];
        $data['month']   = $date[1];

        $data['amount_inr'] = $trf_info['amount_inr'];
        $data['exchange_rate'] = $trf_info['exchange_rate'];
        $data['amount_usd'] = $trf_info['amount_usd'];
        $data['points'] = $trf_info['points'];

        $data['header'] = $this->load->controller('common/header');
        $data['nav'] = $this->load->controller('common/nav');
        $data['navpage'] = $this->load->controller('common/navpage');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('club/view_trf', $data));
    }

    protected function getList() {
        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('club/trf')
        );

        $data['cancel'] = $this->url->link('club/dashboard');

        $data['add'] = $this->url->link('club/trf/add');

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


        $data['trfs'] = array();

        $results = $this->model_club_trf->getTrfById($this->customer->getId());

        foreach ($results as $result) {
            $data['trfs'][] = array(
                'trf_id' => $result['trf_id'],
                'date' => $result['date'],
                'amount_usd' => $result['amount_usd'],
                'points' => $result['points'],
                'view' => $this->url->link('club/trf/view', 'trf_id=' . $result['trf_id'])
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
            'href' => $this->url->link('club/trf')
        );


        $data['continue'] = $this->url->link('common/home');
        $data['add_trf'] = $this->url->link('club/trf/add');
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

        $this->response->setOutput($this->load->view('club/trf', $data));
    }

    protected function getForm() {
        $data['text_form'] = !isset($this->request->get['trf_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

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
        if (isset($this->error['amount_inr'])) {
            $data['amount_inr_err'] = $this->error['amount_inr'];
        } else {
            $data['amount_inr_err'] = '';
        }
        if (isset($this->error['exchange_rate'])) {
            $data['exchange_rate_err'] = $this->error['exchange_rate'];
        } else {
            $data['exchange_rate_err'] = '';
        }
        if (isset($this->error['amount_usd'])) {
            $data['amount_usd_err'] = $this->error['amount_usd'];
        } else {
            $data['amount_usd_err'] = '';
        }
        if (isset($this->error['points'])) {
            $data['points_err'] = $this->error['points'];
        } else {
            $data['points_err'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('club/trf')
        );

        if (!isset($this->request->get['trf_id'])) {
            $data['action'] = $this->url->link('club/trf/add');
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_add'),
                'href' => $this->url->link('club/trf/add')
            );
        } else {
            $data['action'] = $this->url->link('club/trf/edit' . '&trf_id=' . $this->request->get['trf_id']);
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_edit'),
                'href' => $this->url->link('club/trf/edit')
            );
        }

        $data['cancel'] = $this->url->link('club/trf');

        if (isset($this->request->get['trf_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $trf_info = $this->model_club_trf->getTrf($this->request->get['trf_id']);
        }

        if (isset($this->request->post['month'])) {
            $data['month'] = $this->request->post['month'];
        } elseif (!empty($trf_info)) {
            $data['month'] = $trf_info['month'];
        } else {
            $data['month'] = '';
        }

        if (isset($this->request->post['year'])) {
            $data['year'] = $this->request->post['year'];
        } elseif (!empty($trf_info)) {
            $data['year'] = $trf_info['year'];
        } else {
            $data['year'] = '';
        }

        if (isset($this->request->post['month'])) {
            $data['month'] = $this->request->post['month'];
        } elseif (!empty($trf_info)) {
            $data['month'] = $trf_info['month'];
        } else {
            $data['month'] = '';
        }

        if (isset($this->request->post['amount_inr'])) {
            $data['amount_inr'] = $this->request->post['amount_inr'];
        } elseif (!empty($trf_info)) {
            $data['amount_inr'] = $trf_info['amount_inr'];
        } else {
            $data['amount_inr'] = '';
        }

        if (isset($this->request->post['exchange_rate'])) {
            $data['exchange_rate'] = $this->request->post['exchange_rate'];
        } elseif (!empty($trf_info)) {
            $data['exchange_rate'] = $trf_info['exchange_rate'];
        } else {
            $data['exchange_rate'] = '';
        }
        if (isset($this->request->post['amount_usd'])) {
            $data['amount_usd'] = $this->request->post['amount_usd'];
        } elseif (!empty($trf_info)) {
            $data['amount_usd'] = $trf_info['amount_usd'];
        } else {
            $data['amount_usd'] = '';
        }

        if (isset($this->request->post['points'])) {
            $data['points'] = $this->request->post['points'];
        } elseif (!empty($trf_info)) {
            $data['points'] = $trf_info['points'];
        } else {
            $data['points'] = '';
        }



        if (!$this->customer->isLogged()) {
            $this->response->redirect($this->url->link('club/login'));
        }

        $data['total_trf'] = $this->model_club_trf->getTotalTrfById($club_id);

        $data['continue'] = $this->url->link('common/home');
        $data['add_trf'] = $this->url->link('club/trf/add');
        $data['dashboard'] = $this->url->link('club/dashboard');
        $data['project'] = $this->url->link('club/project');
        $data['member'] = $this->url->link('club/member');
        $data['trf'] = $this->url->link('club/trf');
        $data['profile'] = $this->url->link('club/profile');
        $data['logout'] = $this->url->link('club/logout');


        $data['header'] = $this->load->controller('common/header');
        $data['nav'] = $this->load->controller('common/nav');
        $data['navpage'] = $this->load->controller('common/navpage');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('club/trf_form', $data));
    }

}
