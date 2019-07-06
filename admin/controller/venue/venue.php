<?php

class ControllerVenueVenue extends PT_Controller {

    private $error = array();

    public function index() {
        $this->load->language('venue/venue');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('venue/venue');

        $this->getList();
    }

    public function add() {
        $this->load->language('venue/venue');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('venue/venue');

        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $this->model_venue_venue->addVenue($this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('venue/venue', 'user_token=' . $this->session->data['user_token'], true));
        }

        $this->getForm();
    }

    public function edit() {
        $this->load->language('venue/venue');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('venue/venue');

        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $this->model_venue_venue->editVenue($this->request->get['venue_id'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('venue/venue', 'user_token=' . $this->session->data['user_token'], true));
        }

        $this->getForm();
    }

    public function delete() {
        $this->load->language('venue/venue');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('venue/venue');

        if (isset($this->request->post['selected'])) {
            foreach ($this->request->post['selected'] as $venue_id) {
                $this->model_venue_venue->deleteVenue($venue_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('venue/venue', 'user_token=' . $this->session->data['user_token'], true));
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
            'href' => $this->url->link('venue/venue', 'user_token=' . $this->session->data['user_token'])
        );

        $data['add'] = $this->url->link('venue/venue/add', 'user_token=' . $this->session->data['user_token']);
        $data['delete'] = $this->url->link('venue/venue/delete', 'user_token=' . $this->session->data['user_token']);

        $data['sports'] = array();

        $results = $this->model_venue_venue->getVenues();

        foreach ($results as $result) {
            $data['sports'][] = array(
                'venue_id' => $result['venue_id'],
                'name' => $result['name'],
                'edit' => $this->url->link('venue/venue/edit', 'user_token=' . $this->session->data['user_token'] . '&venue_id=' . $result['venue_id']),
                'delete' => $this->url->link('venue/venue/delete', 'user_token=' . $this->session->data['user_token'] . '&venue_id=' . $result['venue_id'])
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

        $this->response->setOutput($this->load->view('venue/venue_list', $data));
    }

    protected function getForm() {
        $this->document->addStyle("view/dist/plugins/iCheck/all.css");
        $this->document->addScript("view/dist/plugins/ckeditor/ckeditor.js");
        $this->document->addScript("view/dist/plugins/ckeditor/adapters/jquery.js");
        $this->document->addScript("view/dist/plugins/iCheck/icheck.min.js");

        $data['text_form'] = !isset($this->request->get['venue_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

        if (isset($this->error['warning'])) {
            $data['warning_err'] = $this->error['warning'];
        } else {
            $data['warning_err'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('venue/venue', 'user_token=' . $this->session->data['user_token'])
        );

        if (!isset($this->request->get['venue_id'])) {
            $data['action'] = $this->url->link('venue/venue/add', 'user_token=' . $this->session->data['user_token']);
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_add'),
                'href' => $this->url->link('venue/venue/add', 'user_token=' . $this->session->data['user_token'])
            );
        } else {
            $data['action'] = $this->url->link('venue/venue/edit', 'user_token=' . $this->session->data['user_token'] . '&venue_id=' . $this->request->get['venue_id']);
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_edit'),
                'href' => $this->url->link('venue/venue/edit', 'user_token=' . $this->session->data['user_token'] . '&venue_id=' . $this->request->get['venue_id'])
            );
        }

        $data['cancel'] = $this->url->link('venue/venue', 'user_token=' . $this->session->data['user_token']);

        if (isset($this->request->get['venue_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $venue_info = $this->model_venue_venue->getVenue($this->request->get['venue_id']);
        }
        
        $data['user_token'] = $this->session->data['user_token'];

        if (isset($this->request->post['name'])) {
            $data['name'] = $this->request->post['name'];
        } elseif (!empty($venue_info)) {
            $data['name'] = $venue_info['name'];
        } else {
            $data['name'] = '';
        }
        
        if (isset($this->request->post['member_group_id'])) {
            $data['member_group_id'] = $this->request->post['member_group_id'];
        } elseif (!empty($venue_info)) {
            $data['member_group_id'] = $venue_info['member_group_id'];
        } else {
            $data['member_group_id'] = '';
        }

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($venue_info)) {
            $data['status'] = $venue_info['status'];
        } else {
            $data['status'] = true;
        }

        if (isset($this->request->post['venue_group_id'])) {
            $data['venue_group_id'] = $this->request->post['venue_group_id'];
        } elseif (!empty($venue_info)) {
            $data['venue_group_id'] = $venue_info['venue_group_id'];
        } else {
            $data['venue_group_id'] = '';
        }

        $this->load->model('venue/venue_group');

        $data['venue_groups'] = $this->model_venue_venue_group->getVenueGroups();
        
        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();

        $this->load->model('tool/image');

        if (isset($this->request->post['venue_fees'])) {
            $venue_fees = $this->request->post['venue_fees'];
        } elseif (isset($this->request->get['venue_id'])) {
            $venue_fees = $this->model_venue_venue->getVenueFees($this->request->get['venue_id']);
        } else {
            $venue_fees = array();
        }

        $data['venue_fees'] = array();

        foreach ($venue_fees as $venue_fee) {

            $data['venue_fees'][] = array(
                'time'              => $venue_fee['time'],
                'hire_charge'       => $venue_fee['hire_charge'],
                'cleaning_charge'   => $venue_fee['cleaning_charge'],
                'electric_point'    => $venue_fee['electric_point'],
                'extra_hour'        => $venue_fee['extra_hour'],
                'tv'                => $venue_fee['tv'],
                'deposite'          => $venue_fee['deposite'],
                'sort_order'        => $venue_fee['sort_order']
            );
        }

        if (isset($this->request->post['tax'])) {
            $taxs = $this->request->post['tax'];
        } elseif (isset($this->request->get['venue_id'])) {
            $taxs = $this->model_venue_venue->getVenueTax($this->request->get['venue_id']);
        } else {
            $taxs = array();
        }

        $data['taxs'] = array();

        foreach ($taxs as $tax) {

            $data['taxs'][] = array(
                'venue' => $tax['venue'],
                'royalty' => $tax['royalty'],
                'gst' => $tax['gst'],
                'total' => $tax['total'],
                'sort_order' => $tax['sort_order']
            );
        }
        if (isset($this->request->post['info'])) {
            $infos = $this->request->post['info'];
        } elseif (isset($this->request->get['venue_id'])) {
            $infos = $this->model_venue_venue->getVenueInfo($this->request->get['venue_id']);
        } else {
            $infos = array();
        }

        $data['infos'] = array();

        foreach ($infos as $info) {

            $data['infos'][] = array(
                'title' => $info['title'],
                'description' => $info['description']
            );
        }


        $data['placeholder'] = $this->model_tool_image->resize('no-image.png', 100, 100);

        $data['header'] = $this->load->controller('common/header');
        $data['nav'] = $this->load->controller('common/nav');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('venue/venue_form', $data));
    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'venue/venue')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
            $this->error['name'] = $this->language->get('error_name');
        }

        if (isset($this->request->post['venue_fee'])) {
            foreach ($this->request->post['venue_fee'] as $language_id => $value) {
                foreach ($value as $venue_fee_id => $venue_fee) {
                    if ((utf8_strlen($venue_fee['title']) < 2) || (utf8_strlen($venue_fee['title']) > 64)) {
                        $this->error['venue_fee'][$language_id][$venue_fee_id] = $this->language->get('error_title');
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
        if (!$this->user->hasPermission('delete', 'venue/venue')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    public function autocomplete() {
        $json = array();

        if (isset($this->request->get['filter_name'])) {
            $this->load->model('venue/venue');

            $filter_data = [
                'filter_name' => $this->request->get['filter_name'],
                'sort' => 'name',
                'order' => 'ASC',
                'start' => 0,
                'limit' => 5
            ];

            $results = $this->model_venue_venue->getVenues($filter_data);

            foreach ($results as $result) {
                $json[] = [
                    'venue_id' => $result['venue_id'],
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
