<?php

class ControllerActivityActivity extends PT_Controller {

    private $error = array();

    public function index() {
        $this->load->language('activity/activity');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('activity/activity');

        $this->getList();
    }

    public function add() {
        $this->load->language('activity/activity');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('activity/activity');

        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $this->model_activity_activity->addActivity($this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('activity/activity', 'user_token=' . $this->session->data['user_token'], true));
        }

        $this->getForm();
    }

    public function edit() {
        $this->load->language('activity/activity');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('activity/activity');

        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $this->model_activity_activity->editActivity($this->request->get['activity_id'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('activity/activity', 'user_token=' . $this->session->data['user_token'], true));
        }

        $this->getForm();
    }

    public function delete() {
        $this->load->language('activity/activity');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('activity/activity');

        if (isset($this->request->post['selected'])) {
            foreach ($this->request->post['selected'] as $activity_id) {
                $this->model_activity_activity->deleteActivity($activity_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('activity/activity', 'user_token=' . $this->session->data['user_token'], true));
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
            'href' => $this->url->link('activity/activity', 'user_token=' . $this->session->data['user_token'])
        );

        $data['add'] = $this->url->link('activity/activity/add', 'user_token=' . $this->session->data['user_token']);
        $data['delete'] = $this->url->link('activity/activity/delete', 'user_token=' . $this->session->data['user_token']);

        $data['activities'] = array();

        $results = $this->model_activity_activity->getActivities();

        foreach ($results as $result) {
            $data['activities'][] = array(
                'activity_id' => $result['activity_id'],
                'name' => $result['name'],
                'edit' => $this->url->link('activity/activity/edit', 'user_token=' . $this->session->data['user_token'] . '&activity_id=' . $result['activity_id']),
                'delete' => $this->url->link('activity/activity/delete', 'user_token=' . $this->session->data['user_token'] . '&activity_id=' . $result['activity_id'])
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

        $this->response->setOutput($this->load->view('activity/activity_list', $data));
    }

    protected function getForm() {
        $this->document->addStyle("view/dist/plugins/iCheck/all.css");
        $this->document->addScript("view/dist/plugins/ckeditor/ckeditor.js");
        $this->document->addScript("view/dist/plugins/ckeditor/adapters/jquery.js");
        $this->document->addScript("view/dist/plugins/iCheck/icheck.min.js");

        $data['text_form'] = !isset($this->request->get['activity_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

        if (isset($this->error['warning'])) {
            $data['warning_err'] = $this->error['warning'];
        } else {
            $data['warning_err'] = '';
        }

        if (isset($this->error['activity_fee'])) {
            $data['error_activity_fee'] = $this->error['activity_fee'];
        } else {
            $data['error_activity_fee'] = array();
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
            'href' => $this->url->link('activity/activity', 'user_token=' . $this->session->data['user_token'])
        );

        if (!isset($this->request->get['activity_id'])) {
            $data['action'] = $this->url->link('activity/activity/add', 'user_token=' . $this->session->data['user_token']);
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_add'),
                'href' => $this->url->link('activity/activity/add', 'user_token=' . $this->session->data['user_token'])
            );
        } else {
            $data['action'] = $this->url->link('activity/activity/edit', 'user_token=' . $this->session->data['user_token'] . '&activity_id=' . $this->request->get['activity_id']);
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_edit'),
                'href' => $this->url->link('activity/activity/edit', 'user_token=' . $this->session->data['user_token'] . '&activity_id=' . $this->request->get['activity_id'])
            );
        }

        $data['cancel'] = $this->url->link('activity/activity', 'user_token=' . $this->session->data['user_token']);

        if (isset($this->request->get['activity_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $activity_info = $this->model_activity_activity->getActivity($this->request->get['activity_id']);
        }
//        print_r($activity_info);exit;
        $data['user_token'] = $this->session->data['user_token'];

        if (isset($this->request->post['name'])) {
            $data['name'] = $this->request->post['name'];
        } elseif (!empty($activity_info)) {
            $data['name'] = $activity_info['name'];
        } else {
            $data['name'] = '';
        }

        if (isset($this->request->post['member_group_id'])) {
            $data['member_group_id'] = $this->request->post['member_group_id'];
        } elseif (!empty($activity_info)) {
            $data['member_group_id'] = $activity_info['member_group_id'];
        } else {
            $data['member_group_id'] = '';
        }

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($activity_info)) {
            $data['status'] = $activity_info['status'];
        } else {
            $data['status'] = true;
        }

        if (isset($this->request->post['activity_group_id'])) {
            $data['activity_group_id'] = $this->request->post['activity_group_id'];
        } elseif (!empty($activity_info)) {
            $data['activity_group_id'] = $activity_info['activity_group_id'];
        } else {
            $data['activity_group_id'] = '';
        }

//        if (isset($this->request->post['isActive'])) {
//            $data['isActive'] = $this->request->post['isActive'];
//        } elseif (!empty($activity_info)) {
//            $data['isActive'] = $activity_info['isActive'];
//        } else {
//            $data['isActive'] = '';
//        }


        $this->load->model('activity/activity_group');

        $data['activity_groups'] = $this->model_activity_activity_group->getActivityGroups();

        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();

        $this->load->model('tool/image');

        if (isset($this->request->post['activity_fees'])) {
            $activity_fees = $this->request->post['activity_fees'];
        } elseif (isset($this->request->get['activity_id'])) {
            $activity_fees = $this->model_activity_activity->getActivityFees($this->request->get['activity_id']);
        } else {
            $activity_fees = array();
        }

        $data['activity_fees'] = array();

        foreach ($activity_fees as $activity_fee) {

            $data['activity_fees'][] = array(
                'period' => $activity_fee['period'],
                'fees' => $activity_fee['fees'],
                'sort_order' => $activity_fee['sort_order']
            );
        }

        if (isset($this->request->post['time'])) {
            $times = $this->request->post['time'];
        } elseif (isset($this->request->get['activity_id'])) {
            $times = $this->model_activity_activity->getActivityTime($this->request->get['activity_id']);
        } else {
            $times = array();
        }
       
        $data['times'] = array();

        foreach ($times as $time) {

            $data['times'][] = array(
                'gender' => $time['gender'],
                'period' => $time['period'],
                'time' => $time['time'],
                'sort_order' => $time['sort_order']
            );
        }
        
        if (isset($this->request->post['coaching'])) {
            $coachings = $this->request->post['coaching'];
        } elseif (isset($this->request->get['activity_id'])) {
            $coachings = $this->model_activity_activity->getActivityCoaching($this->request->get['activity_id']);
        } else {
            $coachings = array();
        }

        $data['coachings'] = array();

        foreach ($coachings as $coaching) {

            $data['coachings'][] = array(
                'coach_name'    => $coaching['coach_name'],
                'gender'        => $coaching['gender'],
                'days'          => $coaching['days'],
                'time'          => $coaching['time'],
                'sort_order'    => $coaching['sort_order']
            );
        }

        $data['placeholder'] = $this->model_tool_image->resize('no-image.png', 100, 100);

        $data['header'] = $this->load->controller('common/header');
        $data['nav'] = $this->load->controller('common/nav');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('activity/activity_form', $data));
    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'activity/activity')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
            $this->error['name'] = $this->language->get('error_name');
        }

        if (isset($this->request->post['activity_fee'])) {
            foreach ($this->request->post['activity_fee'] as $language_id => $value) {
                foreach ($value as $activity_fee_id => $activity_fee) {
                    if ((utf8_strlen($activity_fee['title']) < 2) || (utf8_strlen($activity_fee['title']) > 64)) {
                        $this->error['activity_fee'][$language_id][$activity_fee_id] = $this->language->get('error_title');
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
        if (!$this->user->hasPermission('delete', 'activity/activity')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    public function autocomplete() {
        $json = array();

        if (isset($this->request->get['filter_name'])) {
            $this->load->model('activity/activity');

            $filter_data = [
                'filter_name' => $this->request->get['filter_name'],
                'sort' => 'name',
                'order' => 'ASC',
                'start' => 0,
                'limit' => 5
            ];

            $results = $this->model_activity_activity->getActivitys($filter_data);

            foreach ($results as $result) {
                $json[] = [
                    'activity_id' => $result['activity_id'],
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
