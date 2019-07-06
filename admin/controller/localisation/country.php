<?php

class ControllerLocalisationCountry extends PT_Controller {

    private $error = array();

    public function index() {
        $this->load->language('localisation/country');

        $this->load->model('localisation/country');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->getList();
    }

//    public function add()
//    {
//        $this->load->language('common/testimonial');
//
//        $this->document->setTitle($this->language->get('heading_title'));
//
//        $this->load->model('catalog/testimonial');
//
//        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
////            print_r($this->request->post);exit;
//            $this->model_catalog_testimonial->addTestimonial($this->request->post);
//
//            $this->session->data['success'] = $this->language->get('text_success');
//
//            $this->response->redirect($this->url->link('common/testimonial', 'user_token=' . $this->session->data['user_token']));
//        }
//
//        $this->getForm();
//    }
//
//    public function edit()
//    {
//        $this->load->language('common/testimonial');
//
//        $this->document->setTitle($this->language->get('heading_title'));
//
//        $this->load->model('catalog/testimonial');
//
//        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
//            $this->model_catalog_testimonial->editTestimonial($this->request->get['testimonial_id'], $this->request->post);
//
//            $this->session->data['success'] = $this->language->get('text_success');
//
//            $this->response->redirect($this->url->link('common/testimonial', 'user_token=' . $this->session->data['user_token']));
//        }
//
//        $this->getForm();
//    }
//
//    public function delete()
//    {
//        $this->load->language('common/testimonial');
//
//        $this->document->setTitle($this->language->get('heading_title'));
//
//        $this->load->model('catalog/testimonial');
//
//        if (isset($this->request->post['selected'])) {
//            foreach ($this->request->post['selected'] as $testimonial_id) {
//                $this->model_catalog_testimonial->deleteTestimonial($testimonial_id);
//            }
//
//            $this->session->data['success'] = $this->language->get('text_success');
//
//            $this->response->redirect($this->url->link('common/testimonial', 'user_token=' . $this->session->data['user_token']));
//        }
//
//        $this->getList();
//    }

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
            'href' => $this->url->link('localisation/country', 'user_token=' . $this->session->data['user_token'])
        );

//        $data['add'] = $this->url->link('common/testimonial/add', 'user_token=' . $this->session->data['user_token']);
//        $data['delete'] = $this->url->link('common/testimonial/delete', 'user_token=' . $this->session->data['user_token']);

        $data['countries'] = array();

        $results = $this->model_localisation_country->getCountries();

        foreach ($results as $result) {
            $data['countries'][] = array(
                'country_id' => $result['country_id'],
                'name' => $result['name'],
                'iso_code_2' => $result['iso_code_2'],
                'iso_code_3' => $result['iso_code_3']
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

        $this->response->setOutput($this->load->view('localisation/country_list', $data));
    }

//
//    protected function getForm()
//    {
//        $this->document->addStyle("view/dist/plugins/iCheck/all.css");
//        $this->document->addScript("view/dist/plugins/ckeditor/ckeditor.js");
//        $this->document->addScript("view/dist/plugins/ckeditor/adapters/jquery.js");
//        $this->document->addScript("view/dist/plugins/iCheck/icheck.min.js");
//        
//        $data['text_form'] = !isset($this->request->get['testimonial_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
//
//        if (isset($this->error['warning'])) {
//            $data['warning_err'] = $this->error['warning'];
//        } else {
//            $data['warning_err'] = '';
//        }
//
//        if (isset($this->error['designation'])) {
//            $data['designation_err'] = $this->error['designation'];
//        } else {
//            $data['designation_err'] = '';
//        }
//
//        if (isset($this->error['name'])) {
//            $data['name_err'] = $this->error['name'];
//        } else {
//            $data['name_err'] = '';
//        }
//
//        $data['breadcrumbs'] = array();
//
//        $data['breadcrumbs'][] = array(
//            'text'  => $this->language->get('text_home'),
//            'href'  => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
//        );
//
//        $data['breadcrumbs'][] = array(
//            'text'  => $this->language->get('heading_title'),
//            'href'  => $this->url->link('common/testimonial', 'user_token=' . $this->session->data['user_token'])
//        );
//
//        if (!isset($this->request->get['testimonial_id'])) {
//            $data['action'] = $this->url->link('common/testimonial/add', 'user_token=' . $this->session->data['user_token']);
//            $data['breadcrumbs'][] = array(
//                'text'  => $this->language->get('text_add'),
//                'href'  => $this->url->link('common/testimonial/add', 'user_token=' . $this->session->data['user_token'])
//            );
//        } else {
//            $data['action'] = $this->url->link('common/testimonial/edit', 'user_token=' . $this->session->data['user_token'] . '&testimonial_id=' . $this->request->get['testimonial_id']);
//            $data['breadcrumbs'][] = array(
//                'text'  => $this->language->get('text_edit'),
//                'href'  => $this->url->link('common/testimonial/edit', 'user_token=' . $this->session->data['user_token'])
//            );
//        }
//
//        $data['cancel'] = $this->url->link('common/testimonial', 'user_token=' . $this->session->data['user_token']);
//
//        if (isset($this->request->get['testimonial_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
//            $testimonial_info = $this->model_catalog_testimonial->getTestimonial($this->request->get['testimonial_id']);
//        }
//        
//        if (isset($this->request->post['name'])) {
//            $data['name'] = $this->request->post['name'];
//        } elseif (!empty($testimonial_info)) {
//            $data['name'] = $testimonial_info['name'];
//        } else {
//            $data['name'] = '';
//        }
//        
//        if (isset($this->request->post['designation'])) {
//            $data['designation'] = $this->request->post['designation'];
//        } elseif (!empty($testimonial_info)) {
//            $data['designation'] = $testimonial_info['designation'];
//        } else {
//            $data['designation'] = '';
//        }
//        
//        if (isset($this->request->post['description'])) {
//            $data['description'] = $this->request->post['description'];
//        } elseif (!empty($testimonial_info)) {
//            $data['description'] = $testimonial_info['description'];
//        } else {
//            $data['description'] = '';
//        }
//
//        if (isset($this->request->post['sort_order'])) {
//            $data['sort_order'] = $this->request->post['sort_order'];
//        } elseif (!empty($testimonial_info)) {
//            $data['sort_order'] = $testimonial_info['sort_order'];
//        } else {
//            $data['sort_order'] = 0;
//        }
//  
//
//        $this->load->model('tool/image');
//             
//        if (isset($this->request->post['image'])) {
//            $data['image'] = $this->request->post['image'];
//        } elseif (!empty($testimonial_info)) {
//            $data['image'] = $testimonial_info['image'];
//        } else {
//            $data['image'] = '';
//        }
//        
//        $data['placeholder'] = $this->model_tool_image->resize('no-image.png', 100, 100);
//
//        if (is_file(DIR_IMAGE . html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8'))) {
//            $data['thumb'] = $this->model_tool_image->resize(html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8'), 100, 100);
//        } else {
//            $data['thumb'] = $data['placeholder'];
//        }
//
//        if (isset($this->request->post['status'])) {
//            $data['status'] = $this->request->post['status'];
//        } elseif (!empty($testimonial_info)) {
//            $data['status'] = $testimonial_info['status'];
//        } else {
//            $data['status'] = 0;
//        }
//
//        $data['header'] = $this->load->controller('common/header');
//        $data['nav'] = $this->load->controller('common/nav');
//        $data['footer'] = $this->load->controller('common/footer');
//
//        $this->response->setOutput($this->load->view('common/testimonial_form', $data));
//    }
//
//    protected function validateForm()
//    {
//        if (!$this->user->hasPermission('modify', 'common/testimonial')) {
//            $this->error['warning'] = $this->language->get('error_permission');
//        }
//
//        if ((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
//            $this->error['email'] = $this->language->get('error_email');
//        }
//
//        $testimonial_info = $this->model_catalog_testimonial->getTestimonialByEmail($this->request->post['email']);
//
//        if (!isset($this->request->get['testimonial_id'])) {
//            if ($testimonial_info) {
//                $this->error['warning'] = $this->language->get('error_exists_email');
//            }
//        } else {
//            if ($testimonial_info && ($this->request->get['testimonial_id'] != $testimonial_info['testimonial_id'])) {
//                $this->error['warning'] = $this->language->get('error_exists_email');
//            }
//        }
//
//        if ((utf8_strlen(trim($this->request->post['name'])) < 1) || (utf8_strlen(trim($this->request->post['name'])) > 32)) {
//            $this->error['name'] = $this->language->get('error_name');
//        }
//
//        if ($this->request->post['password'] || (!isset($this->request->get['testimonial_id']))) {
//            if ((utf8_strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) < 4) || (utf8_strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) > 40)) {
//                $this->error['password'] = $this->language->get('error_password');
//            }
//
//            if ($this->request->post['password'] != $this->request->post['confirm']) {
//                $this->error['confirm'] = $this->language->get('error_confirm');
//            }
//        }
//
//        return !$this->error;
//    }
//
//    protected function validateDelete()
//    {
//        if (!$this->user->hasPermission('delete', 'common/testimonial')) {
//            $this->error['warning'] = $this->language->get('error_delete');
//        }
//
//        foreach ($this->request->post['selected'] as $testimonial_id) {
//            if ($this->user->getId() == $testimonial_id) {
//                $this->error['warning'] = $this->language->get('error_account');
//            }
//        }
//
//        return !$this->error;
//    }
    public function country() {
        $json = array();

        $this->load->model('localisation/country');

        $country_info = $this->model_localisation_country->getCountry($this->request->get['country_id']);

        if ($country_info) {
            $this->load->model('localisation/zone');

            $json = array(
                'country_id' => $country_info['country_id'],
                'name' => $country_info['name'],
                'iso_code_2' => $country_info['iso_code_2'],
                'iso_code_3' => $country_info['iso_code_3'],
                'address_format' => $country_info['address_format'],
                'postcode_required' => $country_info['postcode_required'],
                'zone' => $this->model_localisation_zone->getZonesByCountryId($this->request->get['country_id']),
                'status' => $country_info['status']
            );
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

}
