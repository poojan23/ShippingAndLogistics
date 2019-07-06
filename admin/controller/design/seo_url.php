<?php

class ControllerDesignSeoUrl extends PT_Controller {

    private $error = array();

    public function index() {
        $this->load->language('design/seo_url');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('design/seo_url');

        $this->getList();
    }

    public function add() {
        $this->load->language('design/seo_url');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('design/seo_url');

        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
          
            $this->model_design_seo_url->addSeoUrl($this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('design/seo_url', 'user_token=' . $this->session->data['user_token']));
        }

        $this->getForm();
    }

    public function edit() {
        $this->load->language('design/seo_url');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('design/seo_url');

        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $this->model_design_seo_url->editSeoUrl($this->request->get['seo_url_id'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('design/seo_url', 'user_token=' . $this->session->data['user_token']));
        }

        $this->getForm();
    }

    public function delete() {
        $this->load->language('design/seo_url');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('design/seo_url');

        if (isset($this->request->post['selected'])) {
            foreach ($this->request->post['selected'] as $seo_url_id) {
                $this->model_design_seo_url->deleteSeoUrl($seo_url_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('design/seo_url', 'user_token=' . $this->session->data['user_token']));
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
            'href' => $this->url->link('design/seo_url', 'user_token=' . $this->session->data['user_token'])
        );

        $data['add'] = $this->url->link('design/seo_url/add', 'user_token=' . $this->session->data['user_token']);
        $data['delete'] = $this->url->link('design/seo_url/delete', 'user_token=' . $this->session->data['user_token']);

        $data['seo_urls'] = array();

        $results = $this->model_design_seo_url->getSeoUrls();
        
        foreach ($results as $result) {
            $data['seo_urls'][] = array(
                'seo_url_id' => $result['seo_url_id'],
                'query' => $result['query'],
                'keyword' => $result['keyword'],
                'edit' => $this->url->link('design/seo_url/edit', 'user_token=' . $this->session->data['user_token'] . '&seo_url_id=' . $result['seo_url_id']),
                'delete' => $this->url->link('design/seo_url/delete', 'user_token=' . $this->session->data['user_token'] . '&seo_url_id=' . $result['seo_url_id'])
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

        $this->response->setOutput($this->load->view('design/seo_url_list', $data));
    }

    protected function getForm() {
        $this->document->addStyle("view/dist/plugins/iCheck/all.css");
        $this->document->addScript("view/dist/plugins/ckeditor/ckeditor.js");
        $this->document->addScript("view/dist/plugins/ckeditor/adapters/jquery.js");
        $this->document->addScript("view/dist/plugins/iCheck/icheck.min.js");

        $data['text_form'] = !isset($this->request->get['seo_url_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

        if (isset($this->error['warning'])) {
            $data['warning_err'] = $this->error['warning'];
        } else {
            $data['warning_err'] = '';
        }

        if (isset($this->error['query'])) {
            $data['error_query'] = $this->error['query'];
        } else {
            $data['error_query'] = '';
        }

        if (isset($this->error['keyword'])) {
            $data['error_keyword'] = $this->error['keyword'];
        } else {
            $data['error_keyword'] = '';
        }

        if (isset($this->error['push'])) {
            $data['error_push'] = $this->error['push'];
        } else {
            $data['error_push'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('design/seo_url', 'user_token=' . $this->session->data['user_token'])
        );

        if (!isset($this->request->get['seo_url_id'])) {
            $data['action'] = $this->url->link('design/seo_url/add', 'user_token=' . $this->session->data['user_token']);
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_add'),
                'href' => $this->url->link('design/seo_url/add', 'user_token=' . $this->session->data['user_token'])
            );
        } else {
            $data['action'] = $this->url->link('design/seo_url/edit', 'user_token=' . $this->session->data['user_token'] . '&seo_url_id=' . $this->request->get['seo_url_id']);
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_edit'),
                'href' => $this->url->link('design/seo_url/edit', 'user_token=' . $this->session->data['user_token'] . '&seo_url_id=' . $this->request->get['seo_url_id'])
            );
        }

        $data['cancel'] = $this->url->link('design/seo_url', 'user_token=' . $this->session->data['user_token']);

        if (isset($this->request->get['seo_url_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $seo_url_info = $this->model_design_seo_url->getSeoUrl($this->request->get['seo_url_id']);
        }
       
        $data['user_token'] = $this->session->data['user_token'];

        if (isset($this->request->post['query'])) {
            $data['query'] = $this->request->post['query'];
        } elseif (!empty($seo_url_info)) {
            $data['query'] = htmlspecialchars($seo_url_info['query'], ENT_COMPAT, 'UTF-8');
        } else {
            $data['query'] = '';
        }

        if (isset($this->request->post['keyword'])) {
            $data['keyword'] = $this->request->post['keyword'];
        } elseif (!empty($seo_url_info)) {
            $data['keyword'] = $seo_url_info['keyword'];
        } else {
            $data['keyword'] = '';
        }

        if (isset($this->request->post['push'])) {
            $data['push'] = $this->request->post['push'];
        } elseif (!empty($seo_url_info)) {
            $data['push'] = htmlspecialchars($seo_url_info['push'], ENT_COMPAT, 'UTF-8');
        } else {
            $data['push'] = '';
        }

        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();

        if (isset($this->request->post['language_id'])) {
            $data['language_id'] = $this->request->post['language_id'];
        } elseif (!empty($seo_url_info)) {
            $data['language_id'] = $seo_url_info['language_id'];
        } else {
            $data['language_id'] = '';
        }
       
        $data['header'] = $this->load->controller('common/header');
        $data['nav'] = $this->load->controller('common/nav');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('design/seo_url_form', $data));
    }

    protected function validateForm() {
        if (!$this->member->hasPermission('modify', 'design/seo_url')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ($this->request->post['query']) {
            $seo_urls = $this->model_design_seo_url->getSeoUrlsByQuery($this->request->post['query']);

            foreach ($seo_urls as $seo_url) {
                if (($seo_url['language_id'] == $this->request->post['language_id']) && (!isset($this->request->get['seo_url_id']) || (($seo_url['query'] != 'seo_url_id=' . $this->request->get['seo_url_id'])))) {
                    $this->error['query'] = $this->language->get('error_query_exists');

                    break;
                }
            }
        } else {
            $this->error['query'] = $this->language->get('error_query');
        }

        if ($this->request->post['keyword']) {
            if (preg_match('/[^a-zA-Z0-9_\-]+/', $this->request->post['keyword'])) {
                $this->error['keyword'] = $this->language->get('error_keyword');
            }

            $seo_urls = $this->model_design_seo_url->getSeoUrlsByKeyword($this->request->post['keyword']);

            foreach ($seo_urls as $seo_url) {
                if (($seo_url['language_id'] == $this->request->post['language_id']) && (!isset($this->request->get['seo_url_id']) || (($seo_url['query'] != 'seo_url_id=' . $this->request->get['seo_url_id'])))) {
                    $this->error['keyword'] = $this->language->get('error_keyword_exists');

                    break;
                }
            }
        }

        if (!$this->request->post['push']) {
            $this->error['push'] = $this->language->get('error_push');
        }

        return !$this->error;
    }

    protected function validateDelete() {
        if (!$this->member->hasPermission('modify', 'design/seo_url')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

}
