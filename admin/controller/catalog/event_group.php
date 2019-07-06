<?php

class ControllerCatalogEventGroup extends PT_Controller {

    private $error = array();

    public function index() {
        $this->load->language('catalog/event_group');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/event_group');

        $this->getList();
    }

    public function add() {
        $this->load->language('catalog/event_group');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/event_group');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_catalog_event_group->addEventGroup($this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('catalog/event_group', 'user_token=' . $this->session->data['user_token']));
        }

        $this->getForm();
    }

    public function edit() {
        $this->load->language('catalog/event_group');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/event_group');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            
            $this->model_catalog_event_group->editEventGroup($this->request->get['event_group_id'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('catalog/event_group', 'user_token=' . $this->session->data['user_token']));
        }

        $this->getForm();
    }

    public function delete() {
        $this->load->language('catalog/event_group');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/event_group');
       
        if (isset($this->request->post['selected'])) {
            foreach ($this->request->post['selected'] as $event_group_id) {
                $this->model_catalog_event_group->deleteEventGroup($event_group_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('catalog/event_group', 'user_token=' . $this->session->data['user_token']));
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
            'href' => $this->url->link('catalog/event_group', 'user_token=' . $this->session->data['user_token'])
        );

        $data['add'] = $this->url->link('catalog/event_group/add', 'user_token=' . $this->session->data['user_token']);
        $data['delete'] = $this->url->link('catalog/event_group/delete', 'user_token=' . $this->session->data['user_token']);

        $data['event_groups'] = array();

        $results = $this->model_catalog_event_group->getEventGroups();

        foreach ($results as $result) {
            $data['event_groups'][] = array(
                'event_group_id' => $result['event_group_id'],
                'name' => $result['name'],
                'sort_order' => $result['sort_order'],
                'edit' => $this->url->link('catalog/event_group/edit', 'user_token=' . $this->session->data['user_token'] . '&event_group_id=' . $result['event_group_id']),
                'delete' => $this->url->link('catalog/event_group/delete', 'user_token=' . $this->session->data['user_token'] . '&event_group_id=' . $result['event_group_id'])
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

        $this->response->setOutput($this->load->view('catalog/event_group_list', $data));
    }

    protected function getForm() {
        $this->document->addStyle("view/dist/plugins/iCheck/all.css");
        $this->document->addScript("view/dist/plugins/ckeditor/ckeditor.js");
        $this->document->addScript("view/dist/plugins/ckeditor/adapters/jquery.js");
        $this->document->addScript("view/dist/plugins/iCheck/icheck.min.js");

        $data['text_form'] = !isset($this->request->get['event_group_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

        if (isset($this->error['warning'])) {
            $data['warning_err'] = $this->error['warning'];
        } else {
            $data['warning_err'] = '';
        }

        if (isset($this->error['name'])) {
            $data['name_err'] = $this->error['name'];
        } else {
            $data['name_err'] = array();
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
            'href' => $this->url->link('catalog/event_group', 'user_token=' . $this->session->data['user_token'])
        );

        if (!isset($this->request->get['event_group_id'])) {
            $data['action'] = $this->url->link('catalog/event_group/add', 'user_token=' . $this->session->data['user_token']);
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_add'),
                'href' => $this->url->link('catalog/event_group/add', 'user_token=' . $this->session->data['user_token'])
            );
        } else {
            $data['action'] = $this->url->link('catalog/event_group/edit', 'user_token=' . $this->session->data['user_token'] . '&event_group_id=' . $this->request->get['event_group_id']);
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_edit'),
                'href' => $this->url->link('catalog/event_group/edit', 'user_token=' . $this->session->data['user_token'] . '&event_group_id=' . $this->request->get['event_group_id'])
            );
        }

        $data['cancel'] = $this->url->link('catalog/event_group', 'user_token=' . $this->session->data['user_token']);

        if (isset($this->request->get['event_group_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $event_group_info = $this->model_catalog_event_group->getEventGroup($this->request->get['event_group_id']);
        }
      
        $data['user_token'] = $this->session->data['user_token'];

        $this->load->model('localisation/language');

        if (isset($this->request->post['name'])) {
            $data['name'] = $this->request->post['name'];
        } elseif (!empty($event_group_info)) {
            $data['name'] = $event_group_info['name'];
        } else {
            $data['name'] = '';
        }

        if (isset($this->request->post['event_group_id'])) {
            $data['event_group_id'] = $this->request->post['event_group_id'];
        } elseif (!empty($event_group_info)) {
            $data['event_group_id'] = $event_group_info['event_group_id'];
        } else {
            $data['event_group_id'] = '';
        }

        if (isset($this->request->post['sort_order'])) {
            $data['sort_order'] = $this->request->post['sort_order'];
        } elseif (!empty($event_group_info)) {
            $data['sort_order'] = $event_group_info['sort_order'];
        } else {
            $data['sort_order'] = 0;
        }
         if (isset($this->request->get['event_group_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $event_seo = $this->model_catalog_event_group->getEventGroupSeoUrls($this->request->get['event_group_id']);
        }
       
        if (isset($this->request->post['event_seo_url'])) {
            $data['event_seo_url'] = $this->request->post['event_seo_url'];
        } elseif (!empty($event_seo)) {
            $data['event_seo_url'] = $event_seo['keyword'];
        } else {
            $data['event_seo_url'] = '';
        }

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($event_group_info)) {
            $data['status'] = $event_group_info['status'];
        } else {
            $data['status'] = true;
        }

        $data['header'] = $this->load->controller('common/header');
        $data['nav'] = $this->load->controller('common/nav');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('catalog/event_group_form', $data));
    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'catalog/event_group')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        
        if ((utf8_strlen(trim($this->request->post['name'])) < 1) || (utf8_strlen(trim($this->request->post['name'])) > 32)) {
            $this->error['name'] = $this->language->get('error_name');
        }

        if ($this->error && !isset($this->error['warning'])) {
            $this->error['warning'] = $this->language->get('error_warning');
        }

        return !$this->error;
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('delete', 'catalog/event_group')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
    
    public function autocomplete() {
        $json = array();

        if (isset($this->request->get['filter_name'])) {
            $this->load->model('catalog/event_group');

            $filter_data = array(
                'filter_name' => $this->request->get['filter_name'],
                'sort' => 'name',
                'order' => 'ASC',
                'start' => 0,
                'limit' => 5
            );

            $results = $this->model_catalog_event_group->getEvents($filter_data);

            foreach ($results as $result) {
                $json[] = array(
                    'event_group_id' => $result['event_group_id'],
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
