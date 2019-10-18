<?php

class ControllerReportImportReport extends PT_Controller {

    private $error = array();

    public function index() {
        $this->load->language('report/import_report');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('report/import_report');

        $this->getForm();
    }

    public function add() {
        $this->load->language('report/import_report');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('report/import_report');

        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            //$this->model_report_import_report->addImportReport($this->request->post);
            
            $this->model_report_import_report->truncateTable(DB_PREFIX . "temp_dsr");

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('report/import_report', 'user_token=' . $this->session->data['user_token']));
        }

        $this->getForm();
    }

    public function edit() {
        $this->load->language('report/import_report');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('report/import_report');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
           
            $this->model_report_import_report->editImportReport($this->request->get['customer_id'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('report/import_report', 'user_token=' . $this->session->data['user_token']));
        }

        $this->getForm();
    }

    public function delete() {
        $this->load->language('report/import_report');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('report/import_report');

//        if (isset($this->request->post['selected'])) {
//            foreach ($this->request->post['selected'] as $customer_id) {
        $this->model_report_import_report->deleteImportReport();
//            }

        $this->session->data['success'] = $this->language->get('text_success');

        $this->response->redirect($this->url->link('report/import_report', 'user_token=' . $this->session->data['user_token']));
//        }

        $this->getList();
    }

    protected function getList1() {
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
            'href' => $this->url->link('report/import_report', 'user_token=' . $this->session->data['user_token'])
        );

        $data['add'] = $this->url->link('report/import_report/add', 'user_token=' . $this->session->data['user_token']);
        $data['delete'] = $this->url->link('report/import_report/delete', 'user_token=' . $this->session->data['user_token']);
        $data['dsrs'] = array();

        $results = $this->model_report_import_report->getImportReports();

        foreach ($results as $result) {
            $data['dsrs'][] = array(
                'igm_date' => $result['igm_date'],
                'job_no' => $result['job_no'],
                'igm_no' => $result['igm_no'],
                'approve' => $this->url->link('report/import_report/add', 'user_token=' . $this->session->data['user_token']),
                'reject' => $this->url->link('report/import_report/delete', 'user_token=' . $this->session->data['user_token'])
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

        $this->response->setOutput($this->load->view('report/import_report_form', $data));
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
            'href' => $this->url->link('report/import_report', 'user_token=' . $this->session->data['user_token'])
        );

        $data['add'] = $this->url->link('report/import_report/add', 'user_token=' . $this->session->data['user_token']);
        $data['delete'] = $this->url->link('report/import_report/delete', 'user_token=' . $this->session->data['user_token']);
        $data['dsrs'] = array();

        $results = $this->model_report_import_report->getImportReports();

        foreach ($results as $result) {
            $data['dsrs'][] = array(
                'igm_date' => $result['igm_date'],
                'job_no' => $result['job_no'],
                'igm_no' => $result['igm_no'],
                'approve' => $this->url->link('report/import_report/add', 'user_token=' . $this->session->data['user_token']),
                'reject' => $this->url->link('report/import_report/delete', 'user_token=' . $this->session->data['user_token'])
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

        $this->response->setOutput($this->load->view('report/import_report_list', $data));
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
            'href' => $this->url->link('report/import_report', 'user_token=' . $this->session->data['user_token'])
        );

        if (!isset($this->request->get['customer_id'])) {
            $data['action'] = $this->url->link('report/import_report/uploadcsv', 'user_token=' . $this->session->data['user_token']);
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_add'),
                'href' => $this->url->link('report/import_report/add', 'user_token=' . $this->session->data['user_token'])
            );
        } else {
            $data['action'] = $this->url->link('report/import_report/edit', 'user_token=' . $this->session->data['user_token'] . '&customer_id=' . $this->request->get['customer_id']);
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_edit'),
                'href' => $this->url->link('report/import_report/edit', 'user_token=' . $this->session->data['user_token'])
            );
        }

        $data['cancel'] = $this->url->link('report/import_report', 'user_token=' . $this->session->data['user_token']);
        $data['import'] = $this->url->link('report/import_report/upload', 'user_token=' . $this->session->data['user_token']);

        if (isset($this->request->get['customer_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $import_report = $this->model_report_import_report->getImportReport($this->request->get['customer_id']);
        }

        if (isset($this->request->post['dsr'])) {
            $data['dsr'] = $this->request->post['dsr'];
        } elseif (!empty($import_report)) {
            $data['dsr'] = $import_report['dsr'];
        } else {
            $data['dsr'] = '';
        }

        if (isset($this->request->post['customer_id'])) {
            $data['customer_id'] = $this->request->post['customer_id'];
        } elseif (!empty($import_report)) {
            $data['customer_id'] = $import_report['customer_id'];
        } else {
            $data['customer_id'] = '';
        }

        $data['add'] = $this->url->link('report/import_report/add', 'user_token=' . $this->session->data['user_token']);
        $data['delete'] = $this->url->link('report/import_report/delete', 'user_token=' . $this->session->data['user_token']);
        $data['dsrs'] = array();

        $results = $this->model_report_import_report->getImportReports();

        foreach ($results as $result) {
            $data['dsrs'][] = array(
                'customer_id' => $result['customer_id'],
                'job_no' => $result['job_no'],
                'igm_no' => $result['igm_no'],
                'igm_date' => $result['igm_date'],
                'po_no' => $result['po_no'],
                'shipper' => $result['shipper'],
                'be_heading' => $result['be_heading'],
                'no_of_package' => $result['no_of_package'],
                'net_wt' => $result['net_wt'],
                'mode' => $result['mode'],
                'org_eta_date' => $result['org_eta_date'],
                'shipping_line_date' => $result['shipping_line_date'],
                'tentative_eta_date' => $result['tentative_eta_date'],
                'expected_date' => $result['expected_date'],
                'invoice_no' => $result['invoice_no'],
                'invoice_date' => $result['invoice_date'],
                'mawb_no' => $result['mawb_no'],
                'mawb_date' => $result['mawb_date'],
                'hawb_no' => $result['hawb_no'],
                'hawb_date' => $result['hawb_date'],
                'be_no' => $result['be_no'],
                'be_date' => $result['be_date'],
                'airline' => $result['airline'],
                'n_document_date' => $result['n_document_date'],
                'org_doc_date' => $result['org_doc_date'],
                'duty_inform_date' => $result['duty_inform_date'],
                'duty_received_date' => $result['duty_received_date'],
                'duty_paid_date' => $result['duty_paid_date'],
                'total_duty' => $result['total_duty'],
                'container_cleared_date' => $result['container_cleared_date'],
                'detention_amt' => $result['detention_amt'],
                'customer_remark' => $result['customer_remark'],
                'delivery_location_remark' => $result['delivery_location_remark'],
                'container_no' => $result['container_no'],
                'free_period_shipping_date' => $result['free_period_shipping_date'],
                'expected_free_dt_date' => $result['expected_free_dt_date'],
                'expected_free_dt_remark' => $result['expected_free_dt_remark'],
                'approve' => $this->url->link('report/import_report/add', 'user_token=' . $this->session->data['user_token']),
                'reject' => $this->url->link('report/import_report/delete', 'user_token=' . $this->session->data['user_token'])
            );
        }

        $this->load->model('customer/customer');

        $data['customers'] = $this->model_customer_customer->getCustomers();
        $data['header'] = $this->load->controller('common/header');
        $data['nav'] = $this->load->controller('common/nav');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('report/import_report_form', $data));
    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'report/import_report')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ((utf8_strlen(trim($this->request->post['customer_id'])) == 0)) {
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
        if (!$this->user->hasPermission('delete', 'report/import_report')) {
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
            $this->load->model('report/import_report');

            $results = $this->model_report_import_report->getcustomers();

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

    public function upload() {
        $this->load->language('report/import_report');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('customer/customer');

        $data['customers'] = $this->model_customer_customer->getCustomers();

        $this->load->model('report/import_report');
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validateUploadForm())) {
            if ((isset($this->request->files['upload'])) && (is_uploaded_file($this->request->files['upload']['tmp_name']))) {
                $file = $this->request->files['upload']['tmp_name'];
//				$incremental = ($this->request->post['incremental']) ? true : false;
                if ($this->model_report_import_report->upload($file, $this->request->post['customer_id']) == true) {
                    $this->session->data['success'] = $this->language->get('text_success');
                    $this->response->redirect($this->url->link('report/import_report', 'user_token=' . $this->session->data['user_token']));
                } else {
                    $this->error['warning'] = $this->language->get('error_upload1');
                }
            }
        }

        $this->getList1();
    }

    protected function validateUploadForm() {
        if (!isset($this->request->files['upload']['name'])) {
            if (isset($this->error['warning'])) {
                $this->error['warning'] .= "<br /\n" . $this->language->get('error_upload_name');
            } else {
                $this->error['warning'] = $this->language->get('error_upload_name');
            }
        } else {
            $ext = strtolower(pathinfo($this->request->files['upload']['name'], PATHINFO_EXTENSION));
            if (($ext != 'xls') && ($ext != 'xlsx') && ($ext != 'ods')) {
                if (isset($this->error['warning'])) {
                    $this->error['warning'] .= "<br /\n" . $this->language->get('error_upload_ext');
                } else {
                    $this->error['warning'] = $this->language->get('error_upload_ext');
                }
            }
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    public function uploadcsv() {

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');

        $data['entry_status'] = $this->language->get('entry_status');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_module'),
            'href' => $this->url->link('extension/module', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('module/importexport', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['action'] = $this->url->link('module/importexport/mapToCsv', 'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $this->url->link('extension/module', 'user_token=' . $this->session->data['user_token'], true);


        $data['products'] = array();

        $tmp_name = $_FILES["uploadcsvfile"]["tmp_name"];

        $name = basename($_FILES["uploadcsvfile"]["name"]);
        $file_ext = pathinfo($name, PATHINFO_EXTENSION);
        $randfilename = "upload" . "." . $file_ext;
        
        
        move_uploaded_file($tmp_name, $randfilename);

        $csv = array_map('str_getcsv', file(DIR_APPLICATION . 'upload.csv'));
        $array = $fields = array();
        $i = 0;
        $handle = @fopen(DIR_APPLICATION . "upload.csv", "r");
        if ($handle) {
            while (($row = fgetcsv($handle, 4096)) !== false) {
                if (empty($fields)) {
                    $fields = $row;
                    continue;
                }

                $row = array_filter(array_map('trim', $row));

                foreach ($row as $k => $value) {
                    $array[$i][$fields[$k]] = $value;
                }
                $i++;
            }
            if (!feof($handle)) {
                echo "Error: unexpected fgets() fail\n";
            }
            fclose($handle);
        }
//        print_r($fields[1]);exit;
        if (isset($this->request->post['customer_id'])) {
            $data['customer_id'] = $this->request->post['customer_id'];
        } else {
            $data['customer_id'] = 0;
        }
        
        for ($f = 0; $f < count($array); $f++) {
          
//            echo "<pre>";
////            print_r($array[$f][$fields[1]]);exit;
//            echo "INSERT INTO " . DB_PREFIX . "temp_dsr SET customer_id = '" . (int) $data['customer_id'] . "', job_no = '" . (!empty($array[$f][$fields[0]]) ? $this->db->escape($array[$f][$fields[0]]) : '') . "', igm_no ='" . (!empty($array[$f][$fields[1]]) ? $this->db->escape($array[$f][$fields[1]]) : '') . "', igm_date ='" . (!empty($array[$f][$fields[2]]) ? $this->db->escape($array[$f][$fields[2]]) : '') . "', po_no ='" . (!empty($array[$f][$fields[3]]) ? $this->db->escape($array[$f][$fields[3]]) : '') . "', shipper ='" . (!empty($array[$f][$fields[4]]) ? $this->db->escape($array[$f][$fields[4]]) : '') . "', be_heading ='" . (!empty($array[$f][$fields[5]]) ? $this->db->escape($array[$f][$fields[5]]) : '') . "', no_of_package ='" . (!empty($array[$f][$fields[6]]) ? $this->db->escape($array[$f][$fields[6]]) : '') . "',  net_wt ='" . (!empty($array[$f][$fields[7]]) ? $this->db->escape($array[$f][$fields[7]]) : '') . "', mode ='" . (!empty($array[$f][$fields[8]]) ? $this->db->escape($array[$f][$fields[8]]) : '') . "', org_eta_date ='" . (!empty($array[$f][$fields[9]]) ? $this->db->escape($array[$f][$fields[9]]) : '') . "',"
//                    . " shipping_line_date ='" . (!empty($array[$f][$fields[10]]) ? $this->db->escape($array[$f][$fields[10]]) : '') . "', tentative_eta_date ='" . (!empty($array[$f][$fields[11]]) ? $this->db->escape($array[$f][$fields[11]]) : '') . "', expected_date ='" . (!empty($array[$f][$fields[12]]) ? $this->db->escape($array[$f][$fields[12]]) : '') . "', invoice_no ='" . (!empty($array[$f][$fields[13]]) ? $this->db->escape($array[$f][$fields[13]]) : '') . "', invoice_date ='" . (!empty($array[$f][$fields[14]]) ? $this->db->escape($array[$f][$fields[14]]) : '') . "',"
//                    . " mawb_no ='" . (!empty($array[$f][$fields[15]]) ? $this->db->escape($array[$f][$fields[15]]) : '') . "', mawb_date ='" . (!empty($array[$f][$fields[16]]) ? $this->db->escape($array[$f][$fields[16]]) : '') . "', hawb_no ='" . (!empty($array[$f][$fields[17]]) ? $this->db->escape($array[$f][$fields[17]]) : '') . "', hawb_date ='" . (!empty($array[$f][$fields[18]]) ? $this->db->escape($array[$f][$fields[18]]) : '') . "', be_no ='" . (!empty($array[$f][$fields[19]]) ? $this->db->escape($array[$f][$fields[19]]) : '') . "', be_date ='" . (!empty($array[$f][$fields[20]]) ? $this->db->escape($array[$f][$fields[20]]) : '') . "',"
//                    . " airline ='" . (!empty($array[$f][$fields[21]]) ? $this->db->escape($array[$f][$fields[21]]) : '') . "', n_document_date ='" . (!empty($array[$f][$fields[22]]) ? $this->db->escape($array[$f][$fields[22]]) : '') . "', org_doc_date ='" . (!empty($array[$f][$fields[23]]) ? $this->db->escape($array[$f][$fields[23]]) : '') . "', duty_inform_date ='" . (!empty($array[$f][$fields[24]]) ? $this->db->escape($array[$f][$fields[24]]) : '') . "', duty_received_date ='" . (!empty($array[$f][$fields[25]]) ? $this->db->escape($array[$f][$fields[25]]) : '') . "', duty_paid_date ='" . (!empty($array[$f][$fields[26]]) ? $this->db->escape($array[$f][$fields[26]]) : '') . "',"
//                    . " total_duty ='" . (!empty($array[$f][$fields[27]]) ? $this->db->escape($array[$f][$fields[27]]) : '') . "', container_cleared_date ='" . (!empty($array[$f][$fields[28]]) ? $this->db->escape($array[$f][$fields[28]]) : '') . "', detention_amt ='" . (!empty($array[$f][$fields[29]]) ? $this->db->escape($array[$f][$fields[29]]) : '') . "', customer_remark ='" .(!empty($array[$f][$fields[30]]) ? $this->db->escape($array[$f][$fields[30]]) : '') . "', delivery_location_remark ='" . (!empty($array[$f][$fields[31]]) ? $this->db->escape($array[$f][$fields[31]]) : '') . "', "
//                    . "container_no ='" . (!empty($array[$f][$fields[32]]) ? $this->db->escape($array[$f][$fields[32]]) : '') . "', free_period_shipping_date ='" . (!empty($array[$f][$fields[33]]) ? $this->db->escape($array[$f][$fields[33]]) : '') . "', expected_free_dt_date ='" . (!empty($array[$f][$fields[34]]) ? $this->db->escape($array[$f][$fields[34]]) : '') . "', expected_free_dt_remark ='" . (!empty($array[$f][$fields[35]]) ? $this->db->escape($array[$f][$fields[35]]) : '') . "'";

                    $query = $this->db->query("INSERT INTO " . DB_PREFIX . "temp_dsr SET customer_id = '" . (int) $data['customer_id'] . "', job_no = '" . (!empty($array[$f][$fields[0]]) ? $this->db->escape($array[$f][$fields[0]]) : '') . "', igm_no ='" . (!empty($array[$f][$fields[1]]) ? $this->db->escape($array[$f][$fields[1]]) : '') . "', igm_date ='" . (!empty($array[$f][$fields[2]]) ? $this->db->escape($array[$f][$fields[2]]) : '') . "', po_no ='" . (!empty($array[$f][$fields[3]]) ? $this->db->escape($array[$f][$fields[3]]) : '') . "', shipper ='" . (!empty($array[$f][$fields[4]]) ? $this->db->escape($array[$f][$fields[4]]) : '') . "', be_heading ='" . (!empty($array[$f][$fields[5]]) ? $this->db->escape($array[$f][$fields[5]]) : '') . "', no_of_package ='" . (!empty($array[$f][$fields[6]]) ? $this->db->escape($array[$f][$fields[6]]) : '') . "',  net_wt ='" . (!empty($array[$f][$fields[7]]) ? $this->db->escape($array[$f][$fields[7]]) : '') . "', mode ='" . (!empty($array[$f][$fields[8]]) ? $this->db->escape($array[$f][$fields[8]]) : '') . "', org_eta_date ='" . (!empty($array[$f][$fields[9]]) ? $this->db->escape($array[$f][$fields[9]]) : '') . "',"
                    . " shipping_line_date ='" . (!empty($array[$f][$fields[10]]) ? $this->db->escape($array[$f][$fields[10]]) : '') . "', tentative_eta_date ='" . (!empty($array[$f][$fields[11]]) ? $this->db->escape($array[$f][$fields[11]]) : '') . "', expected_date ='" . (!empty($array[$f][$fields[12]]) ? $this->db->escape($array[$f][$fields[12]]) : '') . "', invoice_no ='" . (!empty($array[$f][$fields[13]]) ? $this->db->escape($array[$f][$fields[13]]) : '') . "', invoice_date ='" . (!empty($array[$f][$fields[14]]) ? $this->db->escape($array[$f][$fields[14]]) : '') . "',"
                    . " mawb_no ='" . (!empty($array[$f][$fields[15]]) ? $this->db->escape($array[$f][$fields[15]]) : '') . "', mawb_date ='" . (!empty($array[$f][$fields[16]]) ? $this->db->escape($array[$f][$fields[16]]) : '') . "', hawb_no ='" . (!empty($array[$f][$fields[17]]) ? $this->db->escape($array[$f][$fields[17]]) : '') . "', hawb_date ='" . (!empty($array[$f][$fields[18]]) ? $this->db->escape($array[$f][$fields[18]]) : '') . "', be_no ='" . (!empty($array[$f][$fields[19]]) ? $this->db->escape($array[$f][$fields[19]]) : '') . "', be_date ='" . (!empty($array[$f][$fields[20]]) ? $this->db->escape($array[$f][$fields[20]]) : '') . "',"
                    . " airline ='" . (!empty($array[$f][$fields[21]]) ? $this->db->escape($array[$f][$fields[21]]) : '') . "', n_document_date ='" . (!empty($array[$f][$fields[22]]) ? $this->db->escape($array[$f][$fields[22]]) : '') . "', org_doc_date ='" . (!empty($array[$f][$fields[23]]) ? $this->db->escape($array[$f][$fields[23]]) : '') . "', duty_inform_date ='" . (!empty($array[$f][$fields[24]]) ? $this->db->escape($array[$f][$fields[24]]) : '') . "', duty_received_date ='" . (!empty($array[$f][$fields[25]]) ? $this->db->escape($array[$f][$fields[25]]) : '') . "', duty_paid_date ='" . (!empty($array[$f][$fields[26]]) ? $this->db->escape($array[$f][$fields[26]]) : '') . "',"
                    . " total_duty ='" . (!empty($array[$f][$fields[27]]) ? $this->db->escape($array[$f][$fields[27]]) : '') . "', container_cleared_date ='" . (!empty($array[$f][$fields[28]]) ? $this->db->escape($array[$f][$fields[28]]) : '') . "', detention_amt ='" . (!empty($array[$f][$fields[29]]) ? $this->db->escape($array[$f][$fields[29]]) : '') . "', customer_remark ='" .(!empty($array[$f][$fields[30]]) ? $this->db->escape($array[$f][$fields[30]]) : '') . "', delivery_location_remark ='" . (!empty($array[$f][$fields[31]]) ? $this->db->escape($array[$f][$fields[31]]) : '') . "', "
                    . "container_no ='" . (!empty($array[$f][$fields[32]]) ? $this->db->escape($array[$f][$fields[32]]) : '') . "', free_period_shipping_date ='" . (!empty($array[$f][$fields[33]]) ? $this->db->escape($array[$f][$fields[33]]) : '') . "', expected_free_dt_date ='" . (!empty($array[$f][$fields[34]]) ? $this->db->escape($array[$f][$fields[34]]) : '') . "', expected_free_dt_remark ='" . (!empty($array[$f][$fields[35]]) ? $this->db->escape($array[$f][$fields[35]]) : '') . "'");

//            $query = $this->db->query("INSERT INTO " . DB_PREFIX . "temp_dsr SET customer_id = '" . (int) $data['customer_id'] . "', job_no = '" . $this->db->escape($array[$f]['job_no']) . "', igm_no ='" . $this->db->escape($array[$f]['igm_no']) . "', igm_date ='" . $this->db->escape($array[$f]['igm_date']) . "', po_no ='" . $this->db->escape($array[$f]['po_no']) . "', shipper ='" . $this->db->escape($array[$f]['shipper']) . "', be_heading ='" . $this->db->escape($array[$f]['be_heading']) . "', no_of_package ='" . $this->db->escape($array[$f]['no_of_package']) . "', unit ='" . $this->db->escape($array[$f]['unit']) . "', net_wt ='" . $this->db->escape($array[$f]['net_wt']) . "', mode ='" . $this->db->escape($array[$f]['mode']) . "', org_eta_date ='" . $this->db->escape($array[$f]['org_eta_date']) . "', shipping_line_date ='" . $this->db->escape($array[$f]['shipping_line_date']) . "', tentative_eta_date ='" . $this->db->escape($array[$f]['tentative_eta_date']) . "', expected_date ='" . $this->db->escape($array[$f]['expected_date']) . "', invoice_no ='" . $this->db->escape($array[$f]['invoice_no']) . "', invoice_date ='" . $this->db->escape($array[$f]['invoice_date']) . "', mawb_no ='" . $this->db->escape($array[$f]['mawb_no']) . "', mawb_date ='" . $this->db->escape($array[$f]['mawb_date']) . "', be_no ='" . $this->db->escape($array[$f]['be_no']) . "', be_date ='" . $this->db->escape($array[$f]['be_date']) . "', hawb_no ='" . $this->db->escape($array[$f]['hawb_no']) . "', hawb_date ='" . $this->db->escape($array[$f]['hawb_date']) . "', airline ='" . $this->db->escape($array[$f]['airline']) . "', n_document_date ='" . $this->db->escape($array[$f]['n_document_date']) . "', org_doc_date ='" . $this->db->escape($array[$f]['org_doc_date']) . "', duty_inform_date ='" . $this->db->escape($array[$f]['duty_inform_date']) . "', duty_received_date ='" . $this->db->escape($array[$f]['duty_received_date']) . "', duty_paid_date ='" . $this->db->escape($array[$f]['duty_paid_date']) . "', total_duty ='" . $this->db->escape($array[$f]['total_duty']) . "', container_cleared_date ='" . $this->db->escape($array[$f]['container_cleared_date']) . "', detention_amt ='" . $this->db->escape($array[$f]['detention_amt']) . "', customer_remark ='" . $this->db->escape($array[$f]['customer_remark']) . "', delivery_location_remark ='" . $this->db->escape($array[$f]['delivery_location_remark']) . "', container_no ='" . $this->db->escape($array[$f]['container_no']) . "', free_period_shipping_date ='" . $this->db->escape($array[$f]['free_period_shipping_date']) . "', expected_free_dt_date ='" . $this->db->escape($array[$f]['expected_free_dt_date']) . "', expected_free_dt_remark ='" . $this->db->escape($array[$f]['expected_free_dt_remark']) . "'");
        }
//        exit;
        $data['add'] = $this->url->link('report/import_report/add', 'user_token=' . $this->session->data['user_token']);
        $data['delete'] = $this->url->link('report/import_report/delete', 'user_token=' . $this->session->data['user_token']);
        $data['dsrs'] = array();

        $this->load->model('report/import_report');

        $results = $this->model_report_import_report->getImportReports();

        foreach ($results as $result) {
            $data['dsrs'][] = array(
                'igm_date' => $result['igm_date'],
                'job_no' => $result['job_no'],
                'igm_no' => $result['igm_no'],
                'approve' => $this->url->link('report/import_report/add', 'user_token=' . $this->session->data['user_token']),
                'reject' => $this->url->link('report/import_report/delete', 'user_token=' . $this->session->data['user_token'])
            );
        }

        $data['header'] = $this->load->controller('common/header');
        $data['nav'] = $this->load->controller('common/nav');
        $data['footer'] = $this->load->controller('common/footer');

//        $this->response->setOutput($this->load->view('report/import_report_form', $data));

	$this->response->redirect($this->url->link('report/import_report', 'user_token=' . $this->session->data['user_token'], true));
    }

}
