<?php

class ControllerCustomerCustomerGroup extends PT_Controller {

    private $error = array();

    public function index() {
        $this->load->language('customer/customer_group');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('customer/customer_group');

        $this->getList();
    }

    public function add() {
        $this->load->language('customer/customer_group');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('customer/customer_group');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
//            print_r($this->request->post);exit;
            $this->model_customer_customer_group->addCustomerGroup($this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('customer/customer_group', 'user_token=' . $this->session->data['user_token']));
        }

        $this->getForm();
    }

    public function edit() {
        $this->load->language('customer/customer_group');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('customer/customer_group');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_customer_customer_group->editCustomerGroup($this->request->get['customer_group_id'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('customer/customer_group', 'user_token=' . $this->session->data['user_token']));
        }

        $this->getForm();
    }

    public function delete() {
        $this->load->language('customer/customer_group');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('customer/customer_group');
       
        if (isset($this->request->post['selected'])) {
            foreach ($this->request->post['selected'] as $customer_group_id) {
                $this->model_customer_customer_group->deleteCustomerGroup($customer_group_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('customer/customer_group', 'user_token=' . $this->session->data['user_token']));
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
            'href' => $this->url->link('customer/customer_group', 'user_token=' . $this->session->data['user_token'])
        );

        $data['add'] = $this->url->link('customer/customer_group/add', 'user_token=' . $this->session->data['user_token']);
        $data['delete'] = $this->url->link('customer/customer_group/delete', 'user_token=' . $this->session->data['user_token']);

        $data['customer_groups'] = array();

        $results = $this->model_customer_customer_group->getCustomerGroups();

        foreach ($results as $result) {
            $data['customer_groups'][] = array(
                'customer_group_id' => $result['customer_group_id'],
                'customer_name' => $result['name'],
                'short_form' => $result['short_form'],
                'sort_order' => $result['sort_order'],
                'edit' => $this->url->link('customer/customer_group/edit', 'user_token=' . $this->session->data['user_token'] . '&customer_group_id=' . $result['customer_group_id']),
                'delete' => $this->url->link('customer/customer_group/delete', 'user_token=' . $this->session->data['user_token'] . '&customer_group_id=' . $result['customer_group_id'])
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

        $this->response->setOutput($this->load->view('customer/customer_group_list', $data));
    }

    protected function getForm() {
        $this->document->addStyle("view/dist/plugins/iCheck/all.css");
        $this->document->addScript("view/dist/plugins/ckeditor/ckeditor.js");
        $this->document->addScript("view/dist/plugins/ckeditor/adapters/jquery.js");
        $this->document->addScript("view/dist/plugins/iCheck/icheck.min.js");

        $data['text_form'] = !isset($this->request->get['customer_group_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

        if (isset($this->error['warning'])) {
            $data['warning_err'] = $this->error['warning'];
        } else {
            $data['warning_err'] = '';
        }

        if (isset($this->error['customer_name'])) {
            $data['customer_name_err'] = $this->error['customer_name'];
        } else {
            $data['customer_name_err'] = array();
        }

        if (isset($this->error['short_form'])) {
            $data['short_form_err'] = $this->error['short_form'];
        } else {
            $data['short_form_err'] = array();
        }

        if (isset($this->error['url'])) {
            $data['url_err'] = $this->error['url'];
        } else {
            $data['url_err'] = array();
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('customer/customer_group', 'user_token=' . $this->session->data['user_token'])
        );

        if (!isset($this->request->get['customer_group_id'])) {
            $data['action'] = $this->url->link('customer/customer_group/add', 'user_token=' . $this->session->data['user_token']);
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_add'),
                'href' => $this->url->link('customer/customer_group/add', 'user_token=' . $this->session->data['user_token'])
            );
        } else {
            $data['action'] = $this->url->link('customer/customer_group/edit', 'user_token=' . $this->session->data['user_token'] . '&customer_group_id=' . $this->request->get['customer_group_id']);
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_edit'),
                'href' => $this->url->link('customer/customer_group/edit', 'user_token=' . $this->session->data['user_token'] . '&customer_group_id=' . $this->request->get['customer_group_id'])
            );
        }

        $data['cancel'] = $this->url->link('customer/customer_group', 'user_token=' . $this->session->data['user_token']);

        if (isset($this->request->get['customer_group_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $customer_group_info = $this->model_customer_customer_group->getCustomerGroup($this->request->get['customer_group_id']);
        }
       
        $data['user_token'] = $this->session->data['user_token'];

        $this->load->model('localisation/language');

        if (isset($this->request->post['customer_name'])) {
            $data['customer_name'] = $this->request->post['customer_name'];
        } elseif (!empty($customer_group_info)) {
            $data['customer_name'] = $customer_group_info['name'];
        } else {
            $data['customer_name'] = '';
        }

        if (isset($this->request->post['short_form'])) {
            $data['short_forms'] = $this->request->post['short_form'];
        } elseif (!empty($customer_group_info)) {
            $data['short_forms'] = $customer_group_info['short_form'];
        } else {
            $data['short_forms'] = '';
        }
        
        if (isset($this->request->post['customer_group_id'])) {
            $data['customer_group_id'] = $this->request->post['customer_group_id'];
        } elseif (!empty($customer_group_info)) {
            $data['customer_group_id'] = $customer_group_info['customer_group_id'];
        } else {
            $data['customer_group_id'] = '';
        }

        if (isset($this->request->post['sort_order'])) {
            $data['sort_order'] = $this->request->post['sort_order'];
        } elseif (!empty($customer_group_info)) {
            $data['sort_order'] = $customer_group_info['sort_order'];
        } else {
            $data['sort_order'] = 0;
        }
         if (isset($this->request->get['customer_group_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $information_seo = $this->model_customer_customer_group->getCustomerGroupSeoUrls($this->request->get['customer_group_id']);
        }
       
        if (isset($this->request->post['short_form'])) {
            $data['short_form'] = $this->request->post['short_form'];
        } elseif (!empty($information_seo)) {
            $data['short_form'] = $information_seo['keyword'];
        } else {
            $data['short_form'] = '';
        }

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($customer_group_info)) {
            $data['status'] = $customer_group_info['status'];
        } else {
            $data['status'] = true;
        }

        $data['header'] = $this->load->controller('common/header');
        $data['nav'] = $this->load->controller('common/nav');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('customer/customer_group_form', $data));
    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'customer/customer_group')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        
        if ((utf8_strlen(trim($this->request->post['customer_name'])) < 1) || (utf8_strlen(trim($this->request->post['customer_name'])) > 32)) {
            $this->error['customer_name'] = $this->language->get('error_customer_name');
        }
        
        if ((utf8_strlen(trim($this->request->post['short_form'])) < 1) || (utf8_strlen(trim($this->request->post['short_form'])) > 10)) {
            $this->error['short_form'] = $this->language->get('error_short_form');
        }

        if ($this->error && !isset($this->error['warning'])) {
            $this->error['warning'] = $this->language->get('error_warning');
        }

        return !$this->error;
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('delete', 'customer/customer_group')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    public function icons() {
        $json = array();

        if (!$json) {
            $pattern = '/\.(fa-(?:\w+(?:-)?)+):before\s+{\s*content:\s*"(.+)";\s+}/';
            $subject = file_get_contents('view/dist/plugins/font-awesome/css/font-awesome.css');

            preg_match_all($pattern, $subject, $matches, PREG_SET_ORDER);

            foreach ($matches as $match) {
                $json[] = array(
                    'icon' => $match[1],
                    'css' => implode(array(str_replace('\\', '&#x', $match[2]), ';'))
                );
            }

            // $json = var_export($json, TRUE);
            // $json = stripslashes($json);
        }

        $sort_order = array();

        foreach ($json as $key => $value) {
            $sort_order[$key] = $value;
        }

        array_multisort($sort_order, SORT_ASC, $json);

        //print_r($json);

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function autocomplete() {
        $json = array();

        if (isset($this->request->get['filter_name'])) {
            $this->load->model('customer/customer_group');

            $filter_data = array(
                'filter_name' => $this->request->get['filter_name'],
                'sort' => 'name',
                'order' => 'ASC',
                'start' => 0,
                'limit' => 5
            );

            $results = $this->model_customer_customer_group->getInformations($filter_data);

            foreach ($results as $result) {
                $json[] = array(
                    'customer_group_id' => $result['customer_group_id'],
                    'title' => strip_tags(html_entity_decode($result['title'], ENT_QUOTES, 'UTF-8'))
                );
            }
        }

        $sort_order = array();

        foreach ($json as $key => $value) {
            $sort_order[$key] = $value;
        }

        array_multisort($sort_order, SORT_ASC, $json);

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

}
