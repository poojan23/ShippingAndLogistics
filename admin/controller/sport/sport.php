<?php

class ControllerSportSport extends PT_Controller {

    private $error = array();

    public function index() {
        $this->load->language('sport/sport');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('sport/sport');

        $this->getList();
    }

    public function add() {
        $this->load->language('sport/sport');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('sport/sport');

        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
//            print_r($this->request->post);exit;
            $this->model_sport_sport->addSport($this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('sport/sport', 'user_token=' . $this->session->data['user_token'], true));
        }

        $this->getForm();
    }

    public function edit() {
        $this->load->language('sport/sport');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('sport/sport');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_sport_sport->editSport($this->request->get['sport_id'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('sport/sport', 'user_token=' . $this->session->data['user_token'], true));
        }

        $this->getForm();
    }

    public function delete() {
        $this->load->language('sport/sport');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('sport/sport');

        if (isset($this->request->post['selected'])) {
            foreach ($this->request->post['selected'] as $sport_id) {
                $this->model_sport_sport->deleteSport($sport_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('sport/sport', 'user_token=' . $this->session->data['user_token'], true));
        }

        $this->getList();
    }

    protected function getList() {
        $this->document->addStyle("view/dist/plugins/DataTables/DataTables-1.10.18/css/dataTables.bootstrap4.min.css");
        $this->document->addStyle("view/dist/plugins/DataTables/Buttons-1.5.6/css/buttons.bootstrap4.min.css");
        $this->document->addStyle("view/dist/plugins/DataTables/FixedHeader-3.1.4/css/fixedHeader.bootstrap4.min.css");
        $this->document->addStyle("view/dist/plugins/DataTables/Responsive-2.2.2/css/responsive.bootstrap4.min.css");
        $this->document->addScript("view/dist/plugins/DataTables/DataTables-1.10.18/js/jquery.dataTables.min.js");
        $this->document->addScript("view/dist/plugins/DataTables/DataTables-1.10.18/js/dataTables.bootstrap4.min.js");
        $this->document->addScript("view/dist/plugins/DataTables/Buttons-1.5.6/js/dataTables.buttons.min.js");
        $this->document->addScript("view/dist/plugins/DataTables/Buttons-1.5.6/js/buttons.bootstrap4.min.js");
        $this->document->addScript("view/dist/plugins/DataTables/JSZip-2.5.0/jszip.min.js");
        $this->document->addScript("view/dist/plugins/DataTables/pdfmake-0.1.36/pdfmake.min.js");
        $this->document->addScript("view/dist/plugins/DataTables/pdfmake-0.1.36/vfs_fonts.js");
        $this->document->addScript("view/dist/plugins/DataTables/Buttons-1.5.6/js/buttons.html5.min.js");
        $this->document->addScript("view/dist/plugins/DataTables/Buttons-1.5.6/js/buttons.print.min.js");
        $this->document->addScript("view/dist/plugins/DataTables/Buttons-1.5.6/js/buttons.colVis.min.js");
        $this->document->addScript("view/dist/plugins/DataTables/FixedHeader-3.1.4/js/dataTables.fixedHeader.min.js");
        $this->document->addScript("view/dist/plugins/DataTables/FixedHeader-3.1.4/js/fixedHeader.bootstrap4.min.js");
        $this->document->addScript("view/dist/plugins/DataTables/Responsive-2.2.2/js/dataTables.responsive.min.js");
        $this->document->addScript("view/dist/plugins/DataTables/Responsive-2.2.2/js/responsive.bootstrap4.min.js");

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('sport/sport', 'user_token=' . $this->session->data['user_token'])
        );

        $data['add'] = $this->url->link('sport/sport/add', 'user_token=' . $this->session->data['user_token']);
        $data['delete'] = $this->url->link('sport/sport/delete', 'user_token=' . $this->session->data['user_token']);

        $data['sports'] = array();

        $results = $this->model_sport_sport->getSports();

        foreach ($results as $result) {
            $data['sports'][] = array(
                'sport_id' => $result['sport_id'],
                'name' => $result['name'],
                'edit' => $this->url->link('sport/sport/edit', 'user_token=' . $this->session->data['user_token'] . '&sport_id=' . $result['sport_id']),
                'delete' => $this->url->link('sport/sport/delete', 'user_token=' . $this->session->data['user_token'] . '&sport_id=' . $result['sport_id'])
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

        $data['header'] = $this->load->controller('common/header');
        $data['nav'] = $this->load->controller('common/nav');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('sport/sport_list', $data));
    }

    protected function getForm() {
        $this->document->addStyle("view/dist/plugins/iCheck/all.css");
        $this->document->addScript("view/dist/plugins/ckeditor/ckeditor.js");
        $this->document->addScript("view/dist/plugins/ckeditor/adapters/jquery.js");
        $this->document->addScript("view/dist/plugins/iCheck/icheck.min.js");

        $data['text_form'] = !isset($this->request->get['sport_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

        if (isset($this->error['warning'])) {
            $data['warning_err'] = $this->error['warning'];
        } else {
            $data['warning_err'] = '';
        }

        if (isset($this->error['sport_fee'])) {
            $data['error_sport_fee'] = $this->error['sport_fee'];
        } else {
            $data['error_sport_fee'] = array();
        }

        if (isset($this->error['keyword'])) {
            $data['keyword_err'] = $this->error['keyword'];
        } else {
            $data['keyword_err'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('sport/sport', 'user_token=' . $this->session->data['user_token'])
        );

        if (!isset($this->request->get['sport_id'])) {
            $data['action'] = $this->url->link('sport/sport/add', 'user_token=' . $this->session->data['user_token']);
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_add'),
                'href' => $this->url->link('sport/sport/add', 'user_token=' . $this->session->data['user_token'])
            );
        } else {
            $data['action'] = $this->url->link('sport/sport/edit', 'user_token=' . $this->session->data['user_token'] . '&sport_id=' . $this->request->get['sport_id']);
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_edit'),
                'href' => $this->url->link('sport/sport/edit', 'user_token=' . $this->session->data['user_token'] . '&sport_id=' . $this->request->get['sport_id'])
            );
        }

        $data['cancel'] = $this->url->link('sport/sport', 'user_token=' . $this->session->data['user_token']);

        if (isset($this->request->get['sport_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $sport_info = $this->model_sport_sport->getSport($this->request->get['sport_id']);
        }
//        print_r($sport_info);exit;
        $data['user_token'] = $this->session->data['user_token'];

        if (isset($this->request->post['name'])) {
            $data['name'] = $this->request->post['name'];
        } elseif (!empty($sport_info)) {
            $data['name'] = $sport_info['name'];
        } else {
            $data['name'] = '';
        }

        if (isset($this->request->post['member_group_id'])) {
            $data['member_group_id'] = $this->request->post['member_group_id'];
        } elseif (!empty($sport_info)) {
            $data['member_group_id'] = $sport_info['member_group_id'];
        } else {
            $data['member_group_id'] = '';
        }

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($sport_info)) {
            $data['status'] = $sport_info['status'];
        } else {
            $data['status'] = true;
        }

        if (isset($this->request->post['sport_group_id'])) {
            $data['sport_group_id'] = $this->request->post['sport_group_id'];
        } elseif (!empty($sport_info)) {
            $data['sport_group_id'] = $sport_info['sport_group_id'];
        } else {
            $data['sport_group_id'] = '';
        }

//        if (isset($this->request->post['isActive'])) {
//            $data['isActive'] = $this->request->post['isActive'];
//        } elseif (!empty($sport_info)) {
//            $data['isActive'] = $sport_info['isActive'];
//        } else {
//            $data['isActive'] = '';
//        }


        $this->load->model('sport/sport_group');

        $data['sport_groups'] = $this->model_sport_sport_group->getSportGroups();

        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();

        $this->load->model('tool/image');

        if (isset($this->request->post['sport_fees'])) {
            $sport_fees = $this->request->post['sport_fees'];
        } elseif (isset($this->request->get['sport_id'])) {
            $sport_fees = $this->model_sport_sport->getSportFees($this->request->get['sport_id']);
        } else {
            $sport_fees = array();
        }

        $data['sport_fees'] = array();

        foreach ($sport_fees as $sport_fee) {

            $data['sport_fees'][] = array(
                'period' => $sport_fee['period'],
                'time' => $sport_fee['time'],
                'fees' => $sport_fee['fees'],
                'sort_order' => $sport_fee['sort_order']
            );
        }

        if (isset($this->request->post['coaching'])) {
            $coachings = $this->request->post['coaching'];
        } elseif (isset($this->request->get['sport_id'])) {
            $coachings = $this->model_sport_sport->getSportCoaching($this->request->get['sport_id']);
        } else {
            $coachings = array();
        }

        $data['coachings'] = array();

        foreach ($coachings as $coaching) {

            $data['coachings'][] = array(
                'coach_name' => $coaching['coach_name'],
                'coaching_type' => $coaching['coaching_type'],
                'days' => $coaching['days'],
                'time' => $coaching['time'],
                'fees' => $coaching['fees'],
                'sort_order' => $coaching['sort_order']
            );
        }


        $data['placeholder'] = $this->model_tool_image->resize('no-image.png', 100, 100);

        $data['header'] = $this->load->controller('common/header');
        $data['nav'] = $this->load->controller('common/nav');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('sport/sport_form', $data));
    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'sport/sport')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
            $this->error['name'] = $this->language->get('error_name');
        }

        if (isset($this->request->post['sport_fee'])) {
            foreach ($this->request->post['sport_fee'] as $language_id => $value) {
                foreach ($value as $sport_fee_id => $sport_fee) {
                    if ((utf8_strlen($sport_fee['title']) < 2) || (utf8_strlen($sport_fee['title']) > 64)) {
                        $this->error['sport_fee'][$language_id][$sport_fee_id] = $this->language->get('error_title');
                    }
                }
            }
        }

        if ($this->error && !isset($this->error['warning'])) {
            $this->error['warning'] = $this->language->get('error_warning');
        }

        return !$this->error;
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('delete', 'sport/sport')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    public function autocomplete() {
        $json = array();

        if (isset($this->request->get['filter_name'])) {
            $this->load->model('sport/sport');

            $filter_data = [
                'filter_name' => $this->request->get['filter_name'],
                'sort' => 'name',
                'order' => 'ASC',
                'start' => 0,
                'limit' => 5
            ];

            $results = $this->model_sport_sport->getSports($filter_data);

            foreach ($results as $result) {
                $json[] = [
                    'sport_id' => $result['sport_id'],
                    'name' => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
                ];
            }

            $sort_order = array();

            foreach ($json as $key => $value) {
                $sort_order[$key] = $value['name'];
            }

            array_multisort($sort_order, SORT_ASC, $json);

            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
        }
    }

}
