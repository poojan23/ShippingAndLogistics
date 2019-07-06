<?php

class ControllerCatalogEvent extends PT_Controller {

    private $error = array();

    public function index() {
        $this->load->language('catalog/event');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/event');

        $this->getList();
    }

    public function add() {
        $this->load->language('catalog/event');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/event');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

            $this->model_catalog_event->addEvent($this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('catalog/event', 'user_token=' . $this->session->data['user_token']));
        }

        $this->getForm();
    }

    public function edit() {
        $this->load->language('catalog/event');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/event');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_catalog_event->editEvent($this->request->get['event_id'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('catalog/event', 'user_token=' . $this->session->data['user_token']));
        }

        $this->getForm();
    }

    public function delete() {
        $this->load->language('catalog/event');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/event');

        if (isset($this->request->post['selected'])) {
            foreach ($this->request->post['selected'] as $event_id) {
                $this->model_catalog_event->deleteEvent($event_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('catalog/event', 'user_token=' . $this->session->data['user_token']));
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
            'href' => $this->url->link('catalog/event', 'user_token=' . $this->session->data['user_token'])
        );

        $data['add'] = $this->url->link('catalog/event/add', 'user_token=' . $this->session->data['user_token']);
        $data['delete'] = $this->url->link('catalog/event/delete', 'user_token=' . $this->session->data['user_token']);

        $data['events'] = array();

        $results = $this->model_catalog_event->getEvents();
//        print_r($results);exit;
        foreach ($results as $result) {
            $data['events'][] = array(
                'event_id' => $result['event_id'],
                'name' => $result['name'],
                'sort_order' => $result['sort_order'],
                'edit' => $this->url->link('catalog/event/edit', 'user_token=' . $this->session->data['user_token'] . '&event_id=' . $result['event_id']),
                'delete' => $this->url->link('catalog/event/delete', 'user_token=' . $this->session->data['user_token'] . '&event_id=' . $result['event_id'])
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

        $this->response->setOutput($this->load->view('catalog/event_list', $data));
    }

    protected function getForm() {
        $this->document->addStyle("view/dist/plugins/iCheck/all.css");
        $this->document->addScript("view/dist/plugins/ckeditor/ckeditor.js");
        $this->document->addScript("view/dist/plugins/ckeditor/adapters/jquery.js");
        $this->document->addScript("view/dist/plugins/iCheck/icheck.min.js");

        $data['text_form'] = !isset($this->request->get['event_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

        if (isset($this->error['warning'])) {
            $data['warning_err'] = $this->error['warning'];
        } else {
            $data['warning_err'] = '';
        }

        if (isset($this->error['event_name'])) {
            $data['name_err'] = $this->error['event_name'];
        } else {
            $data['name_err'] = array();
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
            'href' => $this->url->link('catalog/event', 'user_token=' . $this->session->data['user_token'])
        );

        if (!isset($this->request->get['event_id'])) {
            $data['action'] = $this->url->link('catalog/event/add', 'user_token=' . $this->session->data['user_token']);
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_add'),
                'href' => $this->url->link('catalog/event/add', 'user_token=' . $this->session->data['user_token'])
            );
        } else {
            $data['action'] = $this->url->link('catalog/event/edit', 'user_token=' . $this->session->data['user_token'] . '&event_id=' . $this->request->get['event_id']);
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_edit'),
                'href' => $this->url->link('catalog/event/edit', 'user_token=' . $this->session->data['user_token'] . '&event_id=' . $this->request->get['event_id'])
            );
        }

        $data['cancel'] = $this->url->link('catalog/event', 'user_token=' . $this->session->data['user_token']);

        if (isset($this->request->get['event_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $event_info = $this->model_catalog_event->getEvent($this->request->get['event_id']);
        }

        $data['user_token'] = $this->session->data['user_token'];

        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();

        if (isset($this->request->post['event_group_id'])) {
            $data['event_group_id'] = $this->request->post['event_group_id'];
        } elseif (!empty($event_info)) {
            $data['event_group_id'] = $event_info['event_group_id'];
        } else {
            $data['event_group_id'] = '';
        }

        $this->load->model('catalog/event_group');

        $data['event_groups'] = $this->model_catalog_event_group->getEventGroups();

        if (isset($this->request->post['event_name'])) {
            $data['event_name'] = $this->request->post['event_name'];
        } elseif (!empty($event_info)) {
            $data['event_name'] = $event_info['name'];
        } else {
            $data['event_name'] = '';
        }

        if (isset($this->request->post['description'])) {
            $data['description'] = $this->request->post['description'];
        } elseif (!empty($event_info)) {
            $data['description'] = $event_info['description'];
        } else {
            $data['description'] = '';
        }

        if (isset($this->request->post['image'])) {
            $data['image'] = $this->request->post['image'];
        } elseif (!empty($event_info)) {
            $data['image'] = $event_info['image'];
        } else {
            $data['image'] = '';
        }

        $this->load->model('tool/image');

        $data['placeholder'] = $this->model_tool_image->resize('no-image.png', 100, 100);

        if (is_file(DIR_IMAGE . html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8'))) {
            $data['thumb'] = $this->model_tool_image->resize(html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8'), 100, 100);
        } else {
            $data['thumb'] = $data['placeholder'];
        }

        // Images
        if (isset($this->request->post['event_image'])) {
            $event_images = $this->request->post['event_image'];
        } elseif (!empty($event_info)) {
            $event_images = $this->model_catalog_event->getEventImages($this->request->get['event_id']);
        } else {
            $event_images = array();
        }

        $data['event_images'] = array();

        foreach ($event_images as $event_image) {
            if (is_file(DIR_IMAGE . html_entity_decode($event_image['image'], ENT_QUOTES, 'UTF-8'))) {
                $image = $event_image['image'];
                $thumb = $event_image['image'];
            } else {
                $image = '';
                $thumb = 'no_image.png';
            }

            $data['event_images'][] = array(
                'image' => $image,
                'thumb' => $this->model_tool_image->resize(html_entity_decode($thumb, ENT_QUOTES, 'UTF-8'), 100, 100),
                'sort_order' => $event_image['sort_order']
            );
        }

        if (isset($this->request->post['sort_order'])) {
            $data['sort_order'] = $this->request->post['sort_order'];
        } elseif (!empty($event_info)) {
            $data['sort_order'] = $event_info['sort_order'];
        } else {
            $data['sort_order'] = 0;
        }

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($event_info)) {
            $data['status'] = $event_info['status'];
        } else {
            $data['status'] = true;
        }

        if (isset($this->request->post['event_seo_url'])) {
            $data['event_seo_url'] = $this->request->post['event_seo_url'];
        } elseif (!empty($event_info)) {
            $data['event_seo_url'] = $this->model_catalog_event->getEventSeoUrls($this->request->get['event_id']);
        } else {
            $data['event_seo_url'] = array();
        }

        $data['header'] = $this->load->controller('common/header');
        $data['nav'] = $this->load->controller('common/nav');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('catalog/event_form', $data));
    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'catalog/event')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ((utf8_strlen($this->request->post['event_name']) < 3) || (utf8_strlen($this->request->post['event_name']) > 128)) {
            $this->error['event_name'] = $this->language->get('error_event_name');
        }
        
        if ($this->error && !isset($this->error['warning'])) {
            $this->error['warning'] = $this->language->get('error_warning');
        }

        return !$this->error;
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('delete', 'catalog/event')) {
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
            $this->load->model('catalog/event');

            $results = $this->model_catalog_event->getEvents();

            foreach ($results as $result) {
                $json[] = array(
                    'event_id' => $result['event_id'],
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
