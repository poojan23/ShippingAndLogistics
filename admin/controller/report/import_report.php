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
            $this->model_report_import_report->addImportReport($this->request->post);

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
            $data['action'] = $this->url->link('report/import_report/add', 'user_token=' . $this->session->data['user_token']);
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

//        if (isset($this->request->post['job_no'])) {
//            $data['job_no'] = $this->request->post['job_no'];
//        } elseif (!empty($import_report)) {
//            $data['job_no'] = $import_report['job_no'];
//        } else {
//            $data['job_no'] = '';
//        }
//
//        if (isset($this->request->post['igm_no'])) {
//            $data['igm_no'] = $this->request->post['igm_no'];
//        } elseif (!empty($import_report)) {
//            $data['igm_no'] = $import_report['igm_no'];
//        } else {
//            $data['igm_no'] = '';
//        }
//
//
//        if (isset($this->request->post['igm_date'])) {
//            $data['igm_date'] = $this->request->post['igm_date'];
//        } elseif (!empty($import_report)) {
//            $data['igm_date'] = $import_report['igm_date'];
//        } else {
//            $data['igm_date'] = '';
//        }
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
        if (isset($this->request->post['customer_group_id'])) {
            $data['customer_group_id'] = $this->request->post['customer_group_id'];
        } elseif (!empty($import_report)) {
            $data['customer_group_id'] = $import_report['customer_group_id'];
        } else {
            $data['customer_group_id'] = '';
        }
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
        $this->load->model('customer/customer_group');

        $data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();
        $data['header'] = $this->load->controller('common/header');
        $data['nav'] = $this->load->controller('common/nav');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('report/import_report_form', $data));
    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'report/import_report')) {
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
        $this->load->model('customer/customer_group');

        $data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();
        
        $this->load->model('report/import_report');
      if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validateUploadForm())) {
            if ((isset($this->request->files['upload'])) && (is_uploaded_file($this->request->files['upload']['tmp_name']))) {
                $file = $this->request->files['upload']['tmp_name'];
//				$incremental = ($this->request->post['incremental']) ? true : false;
                if ($this->model_report_import_report->upload($file,$this->request->post['customer_group_id']) == true) {
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
				$this->error['warning'] .= "<br /\n" . $this->language->get( 'error_upload_name' );
			} else {
				$this->error['warning'] = $this->language->get( 'error_upload_name' );
			}
		} else {
			$ext = strtolower(pathinfo($this->request->files['upload']['name'], PATHINFO_EXTENSION));
			if (($ext != 'xls') && ($ext != 'xlsx') && ($ext != 'ods')) {
				if (isset($this->error['warning'])) {
					$this->error['warning'] .= "<br /\n" . $this->language->get( 'error_upload_ext' );
				} else {
					$this->error['warning'] = $this->language->get( 'error_upload_ext' );
				}
			}
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
}
