<?php

class ControllerDesignTranslation extends PT_Controller {

    private $error = array();

    public function index() {
        $this->load->language('design/translation');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('design/translation');

        $this->getList();
    }

    public function add() {
        $this->load->language('design/translation');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('design/translation');

        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

            $this->model_design_translation->addTranslation($this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('design/translation', 'user_token=' . $this->session->data['user_token'], true));
        }

        $this->getForm();
    }

    public function edit() {
        $this->load->language('design/translation');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('design/translation');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

            $this->model_design_translation->editTranslation($this->request->get['translation_id'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('design/translation', 'user_token=' . $this->session->data['user_token'], true));
        }

        $this->getForm();
    }

    public function delete() {
        $this->load->language('design/translation');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('design/translation');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $translation_id) {
                $this->model_design_translation->deleteTranslation($translation_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('design/translation', 'user_token=' . $this->session->data['user_token'], true));
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
            'href' => $this->url->link('design/translation', 'user_token=' . $this->session->data['user_token'])
        );

        $this->load->model('localisation/language');

        $data['add'] = $this->url->link('design/translation/add', 'user_token=' . $this->session->data['user_token']);
        $data['delete'] = $this->url->link('design/translation/delete', 'user_token=' . $this->session->data['user_token']);

        $data['translations'] = array();

        $results = $this->model_design_translation->getTranslations();
        
        foreach ($results as $result) {
            $data['translations'][] = array(
                'translation_id' => $result['translation_id'],
                'store' => ($result['store_id'] ? $result['store'] : $this->language->get('text_default')),
                'route' => $result['route'],
                'language' => $result['name'],
                'key' => $result['key'],
                'value' => $result['value'],
                'edit' => $this->url->link('design/translation/edit', 'user_token=' . $this->session->data['user_token'] . '&translation_id=' . $result['translation_id']),
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
        
        $this->response->setOutput($this->load->view('design/translation_list', $data));
    }

    protected function getForm() {
        $this->document->addStyle("view/dist/plugins/iCheck/all.css");
        $this->document->addScript("view/dist/plugins/ckeditor/ckeditor.js");
        $this->document->addScript("view/dist/plugins/ckeditor/adapters/jquery.js");
        $this->document->addScript("view/dist/plugins/iCheck/icheck.min.js");

        $data['text_form'] = !isset($this->request->get['translation_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

        if (isset($this->error['warning'])) {
            $data['warning_err'] = $this->error['warning'];
        } else {
            $data['warning_err'] = '';
        }

        if (isset($this->error['key'])) {
            $data['error_key'] = $this->error['key'];
        } else {
            $data['error_key'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('design/translation', 'user_token=' . $this->session->data['user_token'])
        );

        if (!isset($this->request->get['translation_id'])) {
            $data['action'] = $this->url->link('design/translation/add', 'user_token=' . $this->session->data['user_token']);
        } else {
            $data['action'] = $this->url->link('design/translation/edit', 'user_token=' . $this->session->data['user_token'] . '&translation_id=' . $this->request->get['translation_id']);
        }

        $data['cancel'] = $this->url->link('design/translation', 'user_token=' . $this->session->data['user_token']);

        $data['user_token'] = $this->session->data['user_token'];

        if (isset($this->request->get['translation_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $translation_info = $this->model_design_translation->getTranslation($this->request->get['translation_id']);
        }

        $this->load->model('setting/store');

        $data['stores'] = $this->model_setting_store->getStores();

        if (isset($this->request->post['store_id'])) {
            $data['store_id'] = $this->request->post['store_id'];
        } elseif (!empty($translation_info)) {
            $data['store_id'] = $translation_info['store_id'];
        } else {
            $data['store_id'] = '';
        }

        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();

        if (isset($this->request->post['language_id'])) {
            $data['language_id'] = $this->request->post['language_id'];
        } elseif (!empty($translation_info)) {
            $data['language_id'] = $translation_info['language_id'];
        } else {
            $data['language_id'] = '';
        }

        if (isset($this->request->post['route'])) {
            $data['route'] = $this->request->post['route'];
        } elseif (!empty($translation_info)) {
            $data['route'] = $translation_info['route'];
        } else {
            $data['route'] = '';
        }

        if (isset($this->request->post['key'])) {
            $data['key'] = $this->request->post['key'];
        } elseif (!empty($translation_info)) {
            $data['key'] = $translation_info['key'];
        } else {
            $data['key'] = '';
        }

        if (isset($this->request->post['value'])) {
            $data['value'] = $this->request->post['value'];
        } elseif (!empty($translation_info)) {
            $data['value'] = $translation_info['value'];
        } else {
            $data['value'] = '';
        }

        $data['header'] = $this->load->controller('common/header');
        $data['nav'] = $this->load->controller('common/nav');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('design/translation_form', $data));
    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'design/translation')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ((utf8_strlen($this->request->post['key']) < 3) || (utf8_strlen($this->request->post['key']) > 64)) {
            $this->error['key'] = $this->language->get('error_key');
        }

        return !$this->error;
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'design/translation')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    public function path() {
        $this->load->language('design/translation');

        $json = array();

        if (isset($this->request->get['language_id'])) {
            $language_id = $this->request->get['language_id'];
        } else {
            $language_id = 0;
        }

        $this->load->model('localisation/language');

        $language_info = $this->model_localisation_language->getLanguage($language_id);

        if (!empty($language_info)) {
            $path = glob(DIR_CATALOG . 'language/' . $language_info['code'] . '/*');

            while (count($path) != 0) {
                $next = array_shift($path);

                foreach ((array) glob($next) as $file) {
                    if (is_dir($file)) {
                        $path[] = $file . '/*';
                    }

                    if (substr($file, -4) == '.php') {
                        $json[] = substr(substr($file, strlen(DIR_CATALOG . 'language/' . $language_info['code'] . '/')), 0, -4);
                    }
                }
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function translation() {
        $this->load->language('design/translation');

        $json = array();

        if (isset($this->request->get['store_id'])) {
            $store_id = $this->request->get['store_id'];
        } else {
            $store_id = 0;
        }

        if (isset($this->request->get['language_id'])) {
            $language_id = $this->request->get['language_id'];
        } else {
            $language_id = 0;
        }

        if (isset($this->request->get['path'])) {
            $route = $this->request->get['path'];
        } else {
            $route = '';
        }

        $this->load->model('localisation/language');

        $language_info = $this->model_localisation_language->getLanguage($language_id);
       
        $directory = DIR_CATALOG . 'language/';

        if ($language_info && is_file($directory . $language_info['code'] . '/' . $route . '.php') && substr(str_replace('\\', '/', realpath($directory . $language_info['code'] . '/' . $route . '.php')), 0, strlen($directory)) == str_replace('\\', '/', $directory)) {
            $_ = array();

            include($directory . $language_info['code'] . '/' . $route . '.php');

            foreach ($_ as $key => $value) {
                $json[] = array(
                    'key' => $key,
                    'value' => $value
                );
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

}
