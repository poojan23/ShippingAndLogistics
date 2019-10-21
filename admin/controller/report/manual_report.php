<?php

class ControllerReportManualReport extends PT_Controller {

    private $error = array();

    public function index() {
        $this->load->language('report/manual_report');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('report/manual_report');

        $this->getList();
    }

    public function add() {
        $this->load->language('report/manual_report');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('report/manual_report');

        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $this->model_report_manual_report->addManualReport($this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('report/import_report', 'user_token=' . $this->session->data['user_token']));
        }

        $this->getForm();
    }

    public function edit() {
        $this->load->language('report/manual_report');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('report/manual_report');

        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $this->model_report_manual_report->editManualReport($this->request->get['customer_id'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('report/manual_report', 'user_token=' . $this->session->data['user_token']));
        }

        $this->getForm();
    }

    public function delete() {
        $this->load->language('report/manual_report');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('report/manual_report');

        if (isset($this->request->post['selected'])) {
            foreach ($this->request->post['selected'] as $customer_id) {
                $this->model_report_manual_report->deleteManualReport($customer_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('report/manual_report', 'user_token=' . $this->session->data['user_token']));
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
            'href' => $this->url->link('report/manual_report', 'user_token=' . $this->session->data['user_token'])
        );

        $data['add'] = $this->url->link('report/manual_report/add', 'user_token=' . $this->session->data['user_token']);
        $data['delete'] = $this->url->link('report/manual_report/delete', 'user_token=' . $this->session->data['user_token']);

        $data['customers'] = array();

        $results = $this->model_report_manual_report->getManualReports();

        foreach ($results as $result) {
            $data['customers'][] = array(
                'customer_id' => $result['customer_id'],
                'name' => $result['name'],
                'email' => $result['email'],
                'edit' => $this->url->link('report/manual_report/edit', 'user_token=' . $this->session->data['user_token'] . '&customer_id=' . $result['customer_id'])
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

        $this->response->setOutput($this->load->view('report/manual_report_list', $data));
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

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('report/manual_report', 'user_token=' . $this->session->data['user_token'])
        );

        if (!isset($this->request->get['customer_id'])) {
            $data['action'] = $this->url->link('report/manual_report/add', 'user_token=' . $this->session->data['user_token']);
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_add'),
                'href' => $this->url->link('report/manual_report/add', 'user_token=' . $this->session->data['user_token'])
            );
        } else {
            $data['action'] = $this->url->link('report/manual_report/edit', 'user_token=' . $this->session->data['user_token'] . '&customer_id=' . $this->request->get['customer_id']);
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_edit'),
                'href' => $this->url->link('report/manual_report/edit', 'user_token=' . $this->session->data['user_token'])
            );
        }

        $data['cancel'] = $this->url->link('report/manual_report', 'user_token=' . $this->session->data['user_token']);

        if (isset($this->request->get['customer_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $manual_report_info = $this->model_report_manual_report->getManualReport($this->request->get['customer_id']);
        }
                
        if (isset($this->request->post['customer_group_id'])) {
            $data['customer_group_id'] = $this->request->post['customer_group_id'];
        } elseif (!empty($manual_report_info)) {
            $data['customer_group_id'] = $manual_report_info['customer_group_id'];
        } else {
            $data['customer_group_id'] = '';
        }
        
        if (isset($this->request->post['job_no'])) {
            $data['job_no'] = $this->request->post['job_no'];
        } elseif (!empty($manual_report_info)) {
            $data['job_no'] = $manual_report_info['job_no'];
        } else {
            $data['job_no'] = '';
        }
        
        if (isset($this->request->post['igm_no'])) {
            $data['igm_no'] = $this->request->post['igm_no'];
        } elseif (!empty($manual_report_info)) {
            $data['igm_no'] = $manual_report_info['igm_no'];
        } else {
            $data['igm_no'] = '';
        }
        
        if (isset($this->request->post['igm_date'])) {
            $data['igm_date'] = $this->request->post['igm_date'];
        } elseif (!empty($manual_report_info)) {
            $data['igm_date'] = $manual_report_info['igm_date'];
        } else {
            $data['igm_date'] = '';
        }
        
        if (isset($this->request->post['po_no'])) {
            $data['po_no'] = $this->request->post['po_no'];
        } elseif (!empty($manual_report_info)) {
            $data['po_no'] = $manual_report_info['po_no'];
        } else {
            $data['po_no'] = '';
        }
        
        if (isset($this->request->post['shipper'])) {
            $data['shipper'] = $this->request->post['shipper'];
        } elseif (!empty($manual_report_info)) {
            $data['shipper'] = $manual_report_info['shipper'];
        } else {
            $data['shipper'] = '';
        }
        
        if (isset($this->request->post['be_heading'])) {
            $data['be_heading'] = $this->request->post['be_heading'];
        } elseif (!empty($manual_report_info)) {
            $data['be_heading'] = $manual_report_info['be_heading'];
        } else {
            $data['be_heading'] = '';
        }
        
        if (isset($this->request->post['no_of_package'])) {
            $data['no_of_package'] = $this->request->post['no_of_package'];
        } elseif (!empty($manual_report_info)) {
            $data['no_of_package'] = $manual_report_info['no_of_package'];
        } else {
            $data['no_of_package'] = '';
        }
        
        if (isset($this->request->post['net_wt'])) {
            $data['net_wt'] = $this->request->post['net_wt'];
        } elseif (!empty($manual_report_info)) {
            $data['net_wt'] = $manual_report_info['net_wt'];
        } else {
            $data['net_wt'] = '';
        }
        
        if (isset($this->request->post['mode'])) {
            $data['mode'] = $this->request->post['mode'];
        } elseif (!empty($manual_report_info)) {
            $data['mode'] = $manual_report_info['mode'];
        } else {
            $data['mode'] = '';
        }
        
        if (isset($this->request->post['org_eta_date'])) {
            $data['org_eta_date'] = $this->request->post['org_eta_date'];
        } elseif (!empty($manual_report_info)) {
            $data['org_eta_date'] = $manual_report_info['org_eta_date'];
        } else {
            $data['org_eta_date'] = '';
        }
        
        if (isset($this->request->post['shipping_line_date'])) {
            $data['shipping_line_date'] = $this->request->post['shipping_line_date'];
        } elseif (!empty($manual_report_info)) {
            $data['shipping_line_date'] = $manual_report_info['shipping_line_date'];
        } else {
            $data['shipping_line_date'] = '';
        }
        
        if (isset($this->request->post['tentative_eta_date'])) {
            $data['tentative_eta_date'] = $this->request->post['tentative_eta_date'];
        } elseif (!empty($manual_report_info)) {
            $data['tentative_eta_date'] = $manual_report_info['tentative_eta_date'];
        } else {
            $data['tentative_eta_date'] = '';
        }
        
        if (isset($this->request->post['expected_date'])) {
            $data['expected_date'] = $this->request->post['expected_date'];
        } elseif (!empty($manual_report_info)) {
            $data['expected_date'] = $manual_report_info['expected_date'];
        } else {
            $data['expected_date'] = '';
        }
        
        if (isset($this->request->post['invoice_no'])) {
            $data['invoice_no'] = $this->request->post['invoice_no'];
        } elseif (!empty($manual_report_info)) {
            $data['invoice_no'] = $manual_report_info['invoice_no'];
        } else {
            $data['invoice_no'] = '';
        }
        
        if (isset($this->request->post['invoice_date'])) {
            $data['invoice_date'] = $this->request->post['invoice_date'];
        } elseif (!empty($manual_report_info)) {
            $data['invoice_date'] = $manual_report_info['invoice_date'];
        } else {
            $data['invoice_date'] = '';
        }
        
        if (isset($this->request->post['mawb_no'])) {
            $data['mawb_no'] = $this->request->post['mawb_no'];
        } elseif (!empty($manual_report_info)) {
            $data['mawb_no'] = $manual_report_info['mawb_no'];
        } else {
            $data['mawb_no'] = '';
        }
        
        if (isset($this->request->post['mawb_date'])) {
            $data['mawb_date'] = $this->request->post['mawb_date'];
        } elseif (!empty($manual_report_info)) {
            $data['mawb_date'] = $manual_report_info['mawb_date'];
        } else {
            $data['mawb_date'] = '';
        }
        
        if (isset($this->request->post['hawb_no'])) {
            $data['hawb_no'] = $this->request->post['hawb_no'];
        } elseif (!empty($manual_report_info)) {
            $data['hawb_no'] = $manual_report_info['hawb_no'];
        } else {
            $data['hawb_no'] = '';
        }
        
        if (isset($this->request->post['hawb_date'])) {
            $data['hawb_date'] = $this->request->post['hawb_date'];
        } elseif (!empty($manual_report_info)) {
            $data['hawb_date'] = $manual_report_info['hawb_date'];
        } else {
            $data['hawb_date'] = '';
        }
        
        if (isset($this->request->post['be_no'])) {
            $data['be_no'] = $this->request->post['be_no'];
        } elseif (!empty($manual_report_info)) {
            $data['be_no'] = $manual_report_info['be_no'];
        } else {
            $data['be_no'] = '';
        }
        
        if (isset($this->request->post['be_date'])) {
            $data['be_date'] = $this->request->post['be_date'];
        } elseif (!empty($manual_report_info)) {
            $data['be_date'] = $manual_report_info['be_date'];
        } else {
            $data['be_date'] = '';
        }
        
        if (isset($this->request->post['airline'])) {
            $data['airline'] = $this->request->post['airline'];
        } elseif (!empty($manual_report_info)) {
            $data['airline'] = $manual_report_info['airline'];
        } else {
            $data['airline'] = '';
        }
        
        if (isset($this->request->post['n_document_date'])) {
            $data['n_document_date'] = $this->request->post['n_document_date'];
        } elseif (!empty($manual_report_info)) {
            $data['n_document_date'] = $manual_report_info['n_document_date'];
        } else {
            $data['n_document_date'] = '';
        }
        
        if (isset($this->request->post['org_doc_date'])) {
            $data['org_doc_date'] = $this->request->post['org_doc_date'];
        } elseif (!empty($manual_report_info)) {
            $data['org_doc_date'] = $manual_report_info['org_doc_date'];
        } else {
            $data['org_doc_date'] = '';
        }
        
        if (isset($this->request->post['duty_inform_date'])) {
            $data['duty_inform_date'] = $this->request->post['duty_inform_date'];
        } elseif (!empty($manual_report_info)) {
            $data['duty_inform_date'] = $manual_report_info['duty_inform_date'];
        } else {
            $data['duty_inform_date'] = '';
        }
        
        if (isset($this->request->post['duty_received_date'])) {
            $data['duty_received_date'] = $this->request->post['duty_received_date'];
        } elseif (!empty($manual_report_info)) {
            $data['duty_received_date'] = $manual_report_info['duty_received_date'];
        } else {
            $data['duty_received_date'] = '';
        }
        
        if (isset($this->request->post['duty_paid_date'])) {
            $data['duty_paid_date'] = $this->request->post['duty_paid_date'];
        } elseif (!empty($manual_report_info)) {
            $data['duty_paid_date'] = $manual_report_info['duty_paid_date'];
        } else {
            $data['duty_paid_date'] = '';
        }
        
        if (isset($this->request->post['total_duty'])) {
            $data['total_duty'] = $this->request->post['total_duty'];
        } elseif (!empty($manual_report_info)) {
            $data['total_duty'] = $manual_report_info['total_duty'];
        } else {
            $data['total_duty'] = '';
        }
        
        if (isset($this->request->post['container_cleared_date'])) {
            $data['container_cleared_date'] = $this->request->post['container_cleared_date'];
        } elseif (!empty($manual_report_info)) {
            $data['container_cleared_date'] = $manual_report_info['container_cleared_date'];
        } else {
            $data['container_cleared_date'] = '';
        }
        
        if (isset($this->request->post['detention_amt'])) {
            $data['detention_amt'] = $this->request->post['detention_amt'];
        } elseif (!empty($manual_report_info)) {
            $data['detention_amt'] = $manual_report_info['detention_amt'];
        } else {
            $data['detention_amt'] = '';
        }
        
        if (isset($this->request->post['customer_remark'])) {
            $data['customer_remark'] = $this->request->post['customer_remark'];
        } elseif (!empty($manual_report_info)) {
            $data['customer_remark'] = $manual_report_info['customer_remark'];
        } else {
            $data['customer_remark'] = '';
        }
        
        if (isset($this->request->post['delivery_location_remark'])) {
            $data['delivery_location_remark'] = $this->request->post['delivery_location_remark'];
        } elseif (!empty($manual_report_info)) {
            $data['delivery_location_remark'] = $manual_report_info['delivery_location_remark'];
        } else {
            $data['delivery_location_remark'] = '';
        }
        
        if (isset($this->request->post['container_no'])) {
            $data['container_no'] = $this->request->post['container_no'];
        } elseif (!empty($manual_report_info)) {
            $data['container_no'] = $manual_report_info['container_no'];
        } else {
            $data['container_no'] = '';
        }
        
        if (isset($this->request->post['free_period_shipping_date'])) {
            $data['free_period_shipping_date'] = $this->request->post['free_period_shipping_date'];
        } elseif (!empty($manual_report_info)) {
            $data['free_period_shipping_date'] = $manual_report_info['free_period_shipping_date'];
        } else {
            $data['free_period_shipping_date'] = '';
        }
        
        if (isset($this->request->post['expected_free_dt_date'])) {
            $data['expected_free_dt_date'] = $this->request->post['expected_free_dt_date'];
        } elseif (!empty($manual_report_info)) {
            $data['expected_free_dt_date'] = $manual_report_info['expected_free_dt_date'];
        } else {
            $data['expected_free_dt_date'] = '';
        }
        
        if (isset($this->request->post['expected_free_dt_remark'])) {
            $data['expected_free_dt_remark'] = $this->request->post['expected_free_dt_remark'];
        } elseif (!empty($manual_report_info)) {
            $data['expected_free_dt_remark'] = $manual_report_info['expected_free_dt_remark'];
        } else {
            $data['expected_free_dt_remark'] = '';
        }
        
        if (isset($this->request->post['customer_group_id'])) {
            $data['customer_group_id'] = $this->request->post['customer_group_id'];
        } elseif (!empty($manual_report_info)) {
            $data['customer_group_id'] = $manual_report_info['customer_id'];
        } else {
            $data['customer_group_id'] = '';
        }

        
        $this->load->model('customer/customer_group');

        $data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();

        $data['header'] = $this->load->controller('common/header');
        $data['nav'] = $this->load->controller('common/nav');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('report/manual_report_form', $data));
    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'report/manual_report')) {
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
        if (!$this->user->hasPermission('delete', 'report/manual_report')) {
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
            $this->load->model('report/manual_report');

            $results = $this->model_report_manual_report->getcustomers();

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
