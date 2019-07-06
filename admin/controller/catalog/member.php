<?php

class ControllerCatalogMember extends PT_Controller
{
    private $error = array();

    public function index()
    {
        $this->load->language('catalog/member');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/member');

        $this->getList();
    }

    public function edit()
    {
        $this->load->language('catalog/member');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/member');

        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $this->model_catalog_member->editMember($this->request->get['member_id'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');
            
            $result = $this->model_catalog_member->getMember($this->request->get['member_id']);
//            print_r($result);exit;
            $this->response->redirect($this->url->link('catalog/governor_approve', 'user_token=' . $this->session->data['user_token'].'&club_id=' .$result['club_id']));
        }

        $this->getForm();
    }

    protected function getForm()
    {
        $this->document->addStyle("view/dist/plugins/iCheck/all.css");
        $this->document->addScript("view/dist/plugins/ckeditor/ckeditor.js");
        $this->document->addScript("view/dist/plugins/ckeditor/adapters/jquery.js");
        $this->document->addScript("view/dist/plugins/iCheck/icheck.min.js");
        
        $data['text_form'] = !isset($this->request->get['member_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

        if (isset($this->error['warning'])) {
            $data['warning_err'] = $this->error['warning'];
        } else {
            $data['warning_err'] = '';
        }

        if (isset($this->error['month'])) {
            $data['month_err'] = $this->error['month'];
        } else {
            $data['month_err'] = '';
        }
        if (isset($this->error['year'])) {
            $data['year_err'] = $this->error['year'];
        } else {
            $data['year_err'] = '';
        }
        if (isset($this->error['member_induct'])) {
            $data['member_inducted_err'] = $this->error['member_induct'];
        } else {
            $data['member_inducted_err'] = '';
        }

        if (isset($this->error['member_unlist'])) {
            $data['member_unlist_err'] = $this->error['member_unlist'];
        } else {
            $data['member_unlist_err'] = '';
        }
        if (isset($this->error['net_growth'])) {
            $data['net_growth_err'] = $this->error['net_growth'];
        } else {
            $data['net_growth_err'] = '';
        }
        if (isset($this->error['point_accumulate'])) {
            $data['point_accumulate_err'] = $this->error['point_accumulate'];
        } else {
            $data['point_accumulate_err'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text'  => $this->language->get('text_home'),
            'href'  => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
        );

        $data['breadcrumbs'][] = array(
            'text'  => $this->language->get('heading_title'),
            'href'  => $this->url->link('catalog/member', 'user_token=' . $this->session->data['user_token'])
        );

        if (!isset($this->request->get['member_id'])) {
            $data['action'] = $this->url->link('catalog/member/add', 'user_token=' . $this->session->data['user_token']);
            $data['breadcrumbs'][] = array(
                'text'  => $this->language->get('text_add'),
                'href'  => $this->url->link('catalog/member/add', 'user_token=' . $this->session->data['user_token'])
            );
        } else {
            $data['action'] = $this->url->link('catalog/member/edit', 'user_token=' . $this->session->data['user_token'] . '&member_id=' . $this->request->get['member_id']);
            $data['breadcrumbs'][] = array(
                'text'  => $this->language->get('text_edit'),
                'href'  => $this->url->link('catalog/member/edit', 'user_token=' . $this->session->data['user_token'])
            );
        }
        $result = $this->model_catalog_member->getMember($this->request->get['member_id']);
        $data['cancel'] = $this->url->link('catalog/governor_approve', 'user_token=' . $this->session->data['user_token'].'&club_id=' .$result['club_id']);

        if (isset($this->request->get['member_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $member_info = $this->model_catalog_member->getMember($this->request->get['member_id']);
        }
        $date = $member_info['date'];
    
        $date = explode('-', $date);

        if (isset($this->request->post['month'])) {
            $data['month'] = $this->request->post['month'];
        } elseif (!empty($member_info)) {
            $data['month'] = $date[1];
        } else {
            $data['month'] = '';
        }

        if (isset($this->request->post['year'])) {
            $data['year'] = $this->request->post['year'];
        } elseif (!empty($member_info)) {
            $data['year'] = $date[0];
        } else {
            $data['year'] = '';
        }
        
        if (isset($this->request->post['member_induct'])) {
            $data['member_induct'] = $this->request->post['member_induct'];
        } elseif (!empty($member_info)) {
            $data['member_induct'] = $member_info['induction'];
        } else {
            $data['member_induct'] = '';
        }
        
        if (isset($this->request->post['review'])) {
            $data['review'] = $this->request->post['review'];
        } elseif (!empty($member_info)) {
            $data['review'] = $member_info['review'];
        } else {
            $data['review'] = '';
        }
        
        if (isset($this->request->post['member_unlist'])) {
            $data['member_unlist'] = $this->request->post['member_unlist'];
        } elseif (!empty($member_info)) {
            $data['member_unlist'] = $member_info['unlist'];
        } else {
            $data['member_unlist'] = '';
        }
        
        if (isset($this->request->post['net_growth'])) {
            $data['net_growth'] = $this->request->post['net_growth'];
        } elseif (!empty($member_info)) {
            $data['net_growth'] = $member_info['net'];
        } else {
            $data['net_growth'] = '';
        }
        
        if (isset($this->request->post['point_accumulate'])) {
            $data['point_accumulate'] = $this->request->post['point_accumulate'];
        } elseif (!empty($member_info)) {
            $data['point_accumulate'] = $member_info['net'];
        } else {
            $data['point_accumulate'] = '';
        }
        
        if (isset($this->request->post['notes'])) {
            $data['notes'] = $this->request->post['notes'];
        } elseif (!empty($member_info)) {
            $data['notes'] = $member_info['notes'];
        } else {
            $data['notes'] = '';
        }
        
        $data['header'] = $this->load->controller('common/header');
        $data['nav'] = $this->load->controller('common/nav');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('catalog/member_form', $data));
    }

    protected function validateForm()
    {
        if (!$this->user->hasPermission('modify', 'catalog/member')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ((utf8_strlen(trim($this->request->post['content'])) < 1) || (utf8_strlen(trim($this->request->post['content'])) > 255)) {
            $this->error['content'] = $this->language->get('error_content');
        }

        if ((utf8_strlen(trim($this->request->post['value'])) < 1) || (utf8_strlen(trim($this->request->post['value'])) > 255)) {
            $this->error['value'] = $this->language->get('error_value');
        }

        return !$this->error;
    }

    protected function validateDelete()
    {
        if (!$this->user->hasPermission('delete', 'catalog/member')) {
            $this->error['warning'] = $this->language->get('error_delete');
        }

        foreach ($this->request->post['selected'] as $member_id) {
            if ($this->user->getId() == $member_id) {
                $this->error['warning'] = $this->language->get('error_account');
            }
        }

        return !$this->error;
    }
}
