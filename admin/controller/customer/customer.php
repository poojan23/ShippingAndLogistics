<?php

class ControllerCustomerCustomer extends PT_Controller {

    private $error = array();

    public function index() {
        $this->load->language('customer/customer');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('customer/customer');

        $this->getList();
    }

    public function add() {
        $this->load->language('customer/customer');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('customer/customer');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_customer_customer->addCustomer($this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('customer/customer', 'user_token=' . $this->session->data['user_token']));
        }

        $this->getForm();
    }

    public function edit() {
        $this->load->language('customer/customer');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('customer/customer');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_customer_customer->editCustomer($this->request->get['customer_id'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('customer/customer', 'user_token=' . $this->session->data['user_token']));
        }

        $this->getForm();
    }

    public function delete() {
        $this->load->language('customer/customer');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('customer/customer');

        if (isset($this->request->post['selected'])) {
            foreach ($this->request->post['selected'] as $customer_id) {
                $this->model_customer_customer->deleteCustomer($customer_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('customer/customer', 'user_token=' . $this->session->data['user_token']));
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
            'href' => $this->url->link('customer/customer', 'user_token=' . $this->session->data['user_token'])
        );

        $data['add'] = $this->url->link('customer/customer/add', 'user_token=' . $this->session->data['user_token']);
        $data['delete'] = $this->url->link('customer/customer/delete', 'user_token=' . $this->session->data['user_token']);

        $data['customers'] = array();

        $results = $this->model_customer_customer->getCustomers();

        foreach ($results as $result) {
            $data['customers'][] = array(
                'customer_id' => $result['customer_id'],
                'name' => $result['name'],
                'email' => $result['email'],
                'edit' => $this->url->link('customer/customer/edit', 'user_token=' . $this->session->data['user_token'] . '&customer_id=' . $result['customer_id'])
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

        $this->load->model('user/user');

        $user_info = $this->model_user_user->getUser($this->user->getId());

        if ($user_info) {
            $data['user_group'] = $user_info['user_group'];
        }

        $data['header'] = $this->load->controller('common/header');
        $data['nav'] = $this->load->controller('common/nav');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('customer/customer_list', $data));
    }

    protected function getForm() {
        $this->document->addStyle("view/dist/plugins/iCheck/all.css");
        $this->document->addScript("view/dist/plugins/ckeditor/ckeditor.js");
        $this->document->addScript("view/dist/plugins/ckeditor/adapters/jquery.js");
        $this->document->addScript("view/dist/plugins/iCheck/icheck.min.js");

        $data['text_form'] = !isset($this->request->get['customer_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

        if (isset($this->error['warning'])) {
            $data['warning_err'] = $this->error['warning'];
        } else {
            $data['warning_err'] = '';
        }

        if (isset($this->error['email'])) {
            $data['email_err'] = $this->error['email'];
        } else {
            $data['email_err'] = '';
        }

        if (isset($this->error['password'])) {
            $data['password_err'] = $this->error['password'];
        } else {
            $data['password_err'] = '';
        }

        if (isset($this->error['mobile'])) {
            $data['mobile_err'] = $this->error['mobile'];
        } else {
            $data['mobile_err'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('customer/customer', 'user_token=' . $this->session->data['user_token'])
        );

        if (!isset($this->request->get['customer_id'])) {
            $data['action'] = $this->url->link('customer/customer/add', 'user_token=' . $this->session->data['user_token']);
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_add'),
                'href' => $this->url->link('customer/customer/add', 'user_token=' . $this->session->data['user_token'])
            );
        } else {
            $data['action'] = $this->url->link('customer/customer/edit', 'user_token=' . $this->session->data['user_token'] . '&customer_id=' . $this->request->get['customer_id']);
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_edit'),
                'href' => $this->url->link('customer/customer/edit', 'user_token=' . $this->session->data['user_token'])
            );
        }

        $data['cancel'] = $this->url->link('customer/customer', 'user_token=' . $this->session->data['user_token']);

        if (isset($this->request->get['customer_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $customer_info = $this->model_customer_customer->getCustomer($this->request->get['customer_id']);
        }

        if (isset($this->request->post['name'])) {
            $data['name'] = $this->request->post['name'];
        } elseif (!empty($customer_info)) {
            $data['name'] = $customer_info['name'];
        } else {
            $data['name'] = '';
        }

        if (isset($this->request->post['area_id'])) {
            $data['area_id'] = $this->request->post['area_id'];
        } elseif (!empty($customer_info)) {
            $data['area_id'] = $customer_info['area_id'];
        } else {
            $data['area_id'] = '';
        }


        if (isset($this->request->post['customer_id'])) {
            $data['customer_id'] = $this->request->post['customer_id'];
        } elseif (!empty($customer_info)) {
            $data['customer_id'] = $customer_info['customer_id'];
        } else {
            $data['customer_id'] = '';
        }
        
        if (isset($this->request->post['email'])) {
            $data['email'] = $this->request->post['email'];
        } elseif (!empty($customer_info)) {
            $data['email'] = $customer_info['email'];
        } else {
            $data['email'] = '';
        }
        
        if (isset($this->request->post['mobile'])) {
            $data['mobile'] = $this->request->post['mobile'];
        } elseif (!empty($customer_info)) {
            $data['mobile'] = $customer_info['mobile'];
        } else {
            $data['mobile'] = '';
        }
        
        $this->load->model('customer/customer_group');

        $data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();

        if (isset($this->request->post['password'])) {
            $data['password'] = $this->request->post['password'];
        } else {
            $data['password'] = '';
        }

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($customer_info)) {
            $data['status'] = $customer_info['status'];
        } else {
            $data['status'] = true;
        }

        $data['header'] = $this->load->controller('common/header');
        $data['nav'] = $this->load->controller('common/nav');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('customer/customer_form', $data));
    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'customer/customer')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ((utf8_strlen(trim($this->request->post['customer_group_id'])) == 0)) {
            $this->error['name'] = $this->language->get('error_name');
        }


        if ((utf8_strlen(trim($this->request->post['mobile'])) < 1) || (utf8_strlen(trim($this->request->post['mobile'])) > 11)) {
            $this->error['mobile'] = $this->language->get('error_mobile');
        }

        if ((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
            $this->error['email'] = $this->language->get('error_email');
        }


        if ($this->request->post['password'] || (!isset($this->request->get['customer_id']))) {
            if ((utf8_strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) < 4) || (utf8_strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) > 40)) {
                $this->error['password'] = $this->language->get('error_password');
            }
        }

        return !$this->error;
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('delete', 'customer/customer')) {
            $this->error['warning'] = $this->language->get('error_delete');
        }

        foreach ($this->request->post['selected'] as $customer_id) {
            if ($this->user->getId() == $customer_id) {
                $this->error['warning'] = $this->language->get('error_account');
            }
        }

        return !$this->error;
    }

    public function autocomplete() {
        $json = array();

        if (isset($this->request->get['filter_name'])) {
            $this->load->model('customer/customer');

            $results = $this->model_customer_customer->getcustomers();

            foreach ($results as $result) {
                $json[] = array(
                    'customer_id' => $result['customer_id'],
                    'title' => strip_tags(html_entity_decode($result['customer_name'], ENT_QUOTES, 'UTF-8'))
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
