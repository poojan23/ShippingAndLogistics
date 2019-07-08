<?php

class ControllerCatalogArea extends PT_Controller {

    private $error = array();

    public function index() {
        $this->load->language('catalog/area');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/area');

        $this->getList();
    }

    public function add() {
        $this->load->language('catalog/area');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/area');

        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $this->model_catalog_area->addArea($this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('catalog/area', 'user_token=' . $this->session->data['user_token'], true));
        }

        $this->getForm();
    }

    public function edit() {
        $this->load->language('catalog/area');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/area');

        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
//            print_r($this->request->post);exit;
            $this->model_catalog_area->editArea($this->request->get['area_id'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('catalog/area', 'user_token=' . $this->session->data['user_token'], true));
        }

        $this->getForm();
    }

    public function delete() {
        $this->load->language('catalog/area');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/area');

        if (isset($this->request->post['selected'])) {
            foreach ($this->request->post['selected'] as $area_id) {
                $this->model_catalog_area->deleteArea($area_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('catalog/area', 'user_token=' . $this->session->data['user_token'], true));
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
            'href' => $this->url->link('catalog/area', 'user_token=' . $this->session->data['user_token'])
        );

        $data['add'] = $this->url->link('catalog/area/add', 'user_token=' . $this->session->data['user_token']);
        $data['delete'] = $this->url->link('catalog/area/delete', 'user_token=' . $this->session->data['user_token']);

        $data['areas'] = array();

        $results = $this->model_catalog_area->getAreas();
        foreach ($results as $result) {
            $data['areas'][] = array(
                'area_id' => $result['area_id'],
                'short_form' => $result['short_form'],
                'edit' => $this->url->link('catalog/area/edit', 'user_token=' . $this->session->data['user_token'] . '&area_id=' . $result['area_id']),
                'delete' => $this->url->link('catalog/area/delete', 'user_token=' . $this->session->data['user_token'] . '&area_id=' . $result['area_id'])
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

        $this->response->setOutput($this->load->view('catalog/area_list', $data));
    }

    protected function getForm() {
        $this->document->addStyle("view/dist/plugins/iCheck/all.css");
        $this->document->addScript("view/dist/plugins/ckeditor/ckeditor.js");
        $this->document->addScript("view/dist/plugins/ckeditor/adapters/jquery.js");
        $this->document->addScript("view/dist/plugins/iCheck/icheck.min.js");

        $data['text_form'] = !isset($this->request->get['area_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

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
            'href' => $this->url->link('catalog/area', 'user_token=' . $this->session->data['user_token'])
        );

        if (!isset($this->request->get['area_id'])) {
            $data['action'] = $this->url->link('catalog/area/add', 'user_token=' . $this->session->data['user_token']);
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_add'),
                'href' => $this->url->link('catalog/area/add', 'user_token=' . $this->session->data['user_token'])
            );
        } else {
            $data['action'] = $this->url->link('catalog/area/edit', 'user_token=' . $this->session->data['user_token'] . '&area_id=' . $this->request->get['area_id']);
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_edit'),
                'href' => $this->url->link('catalog/area/edit', 'user_token=' . $this->session->data['user_token'] . '&area_id=' . $this->request->get['area_id'])
            );
        }

        $data['cancel'] = $this->url->link('catalog/area', 'user_token=' . $this->session->data['user_token']);

        if (isset($this->request->get['area_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $area_info = $this->model_catalog_area->getArea($this->request->get['area_id']);
        }

        $data['user_token'] = $this->session->data['user_token'];

        if (isset($this->request->post['area'])) {
            $data['area'] = $this->request->post['area'];
        } elseif (!empty($area_info)) {
            $data['area'] = $area_info['area'];
        } else {
            $data['area'] = '';
        }

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($area_info)) {
            $data['status'] = $area_info['status'];
        } else {
            $data['status'] = true;
        }

        if (isset($this->request->post['customer_group_id'])) {
            $data['customer_group_id'] = $this->request->post['customer_group_id'];
        } elseif (!empty($area_info)) {
            $data['customer_group_id'] = $area_info['customer_group_id'];
        } else {
            $data['customer_group_id'] = '';
        }


        $this->load->model('customer/customer_group');

        $data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();

        if (isset($this->request->post['areas'])) {
            $areas = $this->request->post['areas'];
        } elseif (isset($this->request->get['area_id'])) {
            $areas = $this->model_catalog_area->getAreaGroup($this->request->get['area_id']);
        } else {
            $areas = array();
        }

        $data['areas'] = array();

        foreach ($areas as $area) {

            $data['areas'][] = array(
                'area' => $area['area'],
                'sort_order' => $area['sort_order']
            );
        }

        $data['header'] = $this->load->controller('common/header');
        $data['nav'] = $this->load->controller('common/nav');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('catalog/area_form', $data));
    }

    public function getAreasByCustomerId() {
       $json = array();

        if (isset($this->request->get['customer_group_id'])) {
            $customer_group_id = $this->request->get['customer_group_id'];
        } else {
            $customer_group_id = 0;
        }

        $this->load->model('catalog/area');

        $results = $this->model_catalog_area->getAreasByCustomerId($customer_group_id);

        foreach ($results as $result) {
            if ($result['area_id']) {
                $area_id = $result['area_id'];
            } else {
                $area_id = 0;
            }

            $results = $this->model_catalog_area->getAreasByAreaId($area_id);
          
            foreach ($results as $result) {
                $json[] = array('area' => $result['area'],
                                'area_group_id' => $result['area_group_id']);
            }
        }
       
        $this->response->addHeader('Content-Type : application/json');
        $this->response->setOutput(json_encode($json));
    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'catalog/area')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ((utf8_strlen($this->request->post['area']) < 3) || (utf8_strlen($this->request->post['area']) > 64)) {
            $this->error['area'] = $this->language->get('error_area');
        }

        if (isset($this->request->post['area_fee'])) {
            foreach ($this->request->post['area_fee'] as $language_id => $value) {
                foreach ($value as $area_fee_id => $area_fee) {
                    if ((utf8_strlen($area_fee['title']) < 2) || (utf8_strlen($area_fee['title']) > 64)) {
                        $this->error['area_fee'][$language_id][$area_fee_id] = $this->language->get('error_title');
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
        if (!$this->user->hasPermission('delete', 'catalog/area')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    public function autocomplete() {
        $json = array();

        if (isset($this->request->get['filter_area'])) {
            $this->load->model('catalog/area');

            $filter_data = [
                'filter_area' => $this->request->get['filter_area'],
                'sort' => 'area',
                'order' => 'ASC',
                'start' => 0,
                'limit' => 5
            ];

            $results = $this->model_catalog_area->getAreas($filter_data);

            foreach ($results as $result) {
                $json[] = [
                    'area_id' => $result['area_id'],
                    'area' => strip_tags(html_entity_decode($result['area'], ENT_QUOTES, 'UTF-8'))
                ];
            }

            $sort_order = array();

            foreach ($json as $key => $value) {
                $sort_order[$key] = $value['area'];
            }

            array_multisort($sort_order, SORT_ASC, $json);

            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
        }
    }

}
