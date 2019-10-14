<?php

class ControllerModuleImportexport extends PT_Controller {

    private $error = array();

    public function index() {
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
        $this->load->language('module/importexport');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $this->model_setting_setting->editSetting('importexport', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('extension/module', 'user_token=' . $this->session->data['user_token'], true));
        }

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

        $data['action'] = $this->url->link('module/importexport/uploadcsv', 'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $this->url->link('extension/module', 'user_token=' . $this->session->data['user_token'], true);

        if (isset($this->request->post['importexport_status'])) {
            $data['importexport_status'] = $this->request->post['importexport_status'];
        } else {
            $data['importexport_status'] = $this->config->get('importexport_status');
        }

        //$csv = array_map('str_getcsv', file(DIR_APPLICATION . '1.csv'));
//                $array = $fields = array(); $i = 0;
//                $handle = @fopen(DIR_APPLICATION . "2.csv", "r");
//                if ($handle) {
//                    while (($row = fgetcsv($handle, 4096)) !== false) {
//                        if (empty($fields)) {
//                            $fields = $row;
//                            continue;
//                        }
//                        
//                        $row = array_filter(array_map('trim', $row));
//                        
//                        foreach ($row as $k => $value) {
//                            $array[$i][$fields[$k]] = $value;
//                        }
//                        $i++;
//                    }
//                    if (!feof($handle)) {
//                        echo "Error: unexpected fgets() fail\n";
//                    }
//                    fclose($handle);
//                }
//                
//                for($f = 0; $f < count($array); $f++) {
//                    echo '<pre>'; print_r($array[$f]);
//                    $query = $this->db->query("INSERT INTO " . DB_PREFIX . "temp_dsr SET customer_id = '1', job_no = '" . $this->db->escape($array[$f]['job_no']) . "', igm_no ='" . $this->db->escape($array[$f]['igm_no']) . "', igm_date ='" . $this->db->escape($array[$f]['igm_date']) . "', po_no ='" . $this->db->escape($array[$f]['po_no']) . "', shipper ='" . $this->db->escape($array[$f]['shipper']) . "', be_heading ='" . $this->db->escape($array[$f]['be_heading']) . "', no_of_package ='" . $this->db->escape($array[$f]['no_of_package']) . "', unit ='" . $this->db->escape($array[$f]['unit']) . "', net_wt ='" . $this->db->escape($array[$f]['net_wt']) . "', mode ='" . $this->db->escape($array[$f]['mode']) . "', org_eta_date ='" . $this->db->escape($array[$f]['org_eta_date']) . "', shipping_line_date ='" . $this->db->escape($array[$f]['shipping_line_date']) . "', tentative_eta_date ='" . $this->db->escape($array[$f]['tentative_eta_date']) . "', expected_date ='" . $this->db->escape($array[$f]['expected_date']) . "', invoice_no ='" . $this->db->escape($array[$f]['invoice_no']) . "', invoice_date ='" . $this->db->escape($array[$f]['invoice_date']) . "', mawb_no ='" . $this->db->escape($array[$f]['mawb_no']) . "', mawb_date ='" . $this->db->escape($array[$f]['mawb_date']) . "', be_no ='" . $this->db->escape($array[$f]['be_no']) . "', be_date ='" . $this->db->escape($array[$f]['be_date']) . "', hawb_no ='" . $this->db->escape($array[$f]['hawb_no']) . "', hawb_date ='" . $this->db->escape($array[$f]['hawb_date']) . "', airline ='" . $this->db->escape($array[$f]['airline']) . "', n_document_date ='" . $this->db->escape($array[$f]['n_document_date']) . "', org_doc_date ='" . $this->db->escape($array[$f]['org_doc_date']) . "', duty_inform_date ='" . $this->db->escape($array[$f]['duty_inform_date']) . "', duty_received_date ='" . $this->db->escape($array[$f]['duty_received_date']) . "', duty_paid_date ='" . $this->db->escape($array[$f]['duty_paid_date']) . "', total_duty ='" . $this->db->escape($array[$f]['total_duty']) . "', container_cleared_date ='" . $this->db->escape($array[$f]['container_cleared_date']) . "', detention_amt ='" . $this->db->escape($array[$f]['detention_amt']) . "', customer_remark ='" . $this->db->escape($array[$f]['customer_remark']) . "', delivery_location_remark ='" . $this->db->escape($array[$f]['delivery_location_remark']) . "', container_no ='" . $this->db->escape($array[$f]['container_no']) . "', free_period_shipping_date ='" . $this->db->escape($array[$f]['free_period_shipping_date']) . "', expected_free_dt_date ='" . $this->db->escape($array[$f]['expected_free_dt_date']) . "', expected_free_dt_remark ='" . $this->db->escape($array[$f]['expected_free_dt_remark']) . "'");
//                }
//                
//                echo '<pre>';
//                //print_r(($array));
//                exit;

        $data['header'] = $this->load->controller('common/header');
        $data['nav'] = $this->load->controller('common/nav');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('module/importexport', $data));
    }

    public function uploadcsv() {
//              $this->document->addStyle("view/dist/plugins/DataTables/DataTables-1.10.18/css/dataTables.bootstrap4.min.css");
//        $this->document->addStyle("view/dist/plugins/DataTables/Buttons-1.5.6/css/buttons.bootstrap4.min.css");
//        $this->document->addStyle("view/dist/plugins/DataTables/FixedHeader-3.1.4/css/fixedHeader.bootstrap4.min.css");
//        $this->document->addStyle("view/dist/plugins/DataTables/Responsive-2.2.2/css/responsive.bootstrap4.min.css");
//        $this->document->addScript("view/dist/plugins/DataTables/DataTables-1.10.18/js/jquery.dataTables.min.js");
//        $this->document->addScript("view/dist/plugins/DataTables/DataTables-1.10.18/js/dataTables.bootstrap4.min.js");
//        $this->document->addScript("view/dist/plugins/DataTables/Buttons-1.5.6/js/dataTables.buttons.min.js");
//        $this->document->addScript("view/dist/plugins/DataTables/Buttons-1.5.6/js/buttons.bootstrap4.min.js");
//        $this->document->addScript("view/dist/plugins/DataTables/JSZip-2.5.0/jszip.min.js");
//        $this->document->addScript("view/dist/plugins/DataTables/pdfmake-0.1.36/pdfmake.min.js");
//        $this->document->addScript("view/dist/plugins/DataTables/pdfmake-0.1.36/vfs_fonts.js");
//        $this->document->addScript("view/dist/plugins/DataTables/Buttons-1.5.6/js/buttons.html5.min.js");
//        $this->document->addScript("view/dist/plugins/DataTables/Buttons-1.5.6/js/buttons.print.min.js");
//        $this->document->addScript("view/dist/plugins/DataTables/Buttons-1.5.6/js/buttons.colVis.min.js");
//        $this->document->addScript("view/dist/plugins/DataTables/FixedHeader-3.1.4/js/dataTables.fixedHeader.min.js");
//        $this->document->addScript("view/dist/plugins/DataTables/FixedHeader-3.1.4/js/fixedHeader.bootstrap4.min.js");
//        $this->document->addScript("view/dist/plugins/DataTables/Responsive-2.2.2/js/dataTables.responsive.min.js");
//        $this->document->addScript("view/dist/plugins/DataTables/Responsive-2.2.2/js/responsive.bootstrap4.min.js");
//		$this->load->language('module/importexport');
//
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
        $file_ext = strtolower(end(explode('.', $_FILES['uploadcsvfile']['name'])));
        $randfilename = "upload" . "." . $file_ext;
//                                $extension = end(explode(".", $_FILES["uploadcsvfile"]["name"]));

        move_uploaded_file($tmp_name, $randfilename);

//        $handle = fopen($randfilename, "r");
//
//
//        $header = null;
//        while ($row = fgetcsv($handle)) {
//
//            if ($header === null) {
//
//                $header = $row;
//
//                continue;
//            }
//            $data['headercsv'] = $header;
//
//            $data['products'][] = array_combine($header, $row);
//        }
//
//        $data['uploadedFileName'] = $randfilename;
//
//        $data['table_structure'] = array();
//
//        $productTable = $this->getProductTable();
//
////			  $productDescriptionTable=	$this->getProductDescriptionTable();
////				$data['table_structure'] = array_merge($productTable,$productDescriptionTable);
//        $data['table_structure'] = $productTable;
//
//
//
//        /* foreach ($data['products'] as $key => $value) {
//          $this->addCsvProducts($value);
//          } */
//
//        fclose($handle);





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
        
          if (isset($this->request->post['customer_id'])) {
            $data['customer_id'] = $this->request->post['customer_id'];
            } else {
                $data['customer_id'] = 0;
            }

        for ($f = 0; $f < count($array); $f++) {

            $query = $this->db->query("INSERT INTO " . DB_PREFIX . "temp_dsr SET customer_id = '" . (int) $data['customer_id'] . "'', job_no = '" . $this->db->escape($array[$f]['job_no']) . "', igm_no ='" . $this->db->escape($array[$f]['igm_no']) . "', igm_date ='" . $this->db->escape($array[$f]['igm_date']) . "', po_no ='" . $this->db->escape($array[$f]['po_no']) . "', shipper ='" . $this->db->escape($array[$f]['shipper']) . "', be_heading ='" . $this->db->escape($array[$f]['be_heading']) . "', no_of_package ='" . $this->db->escape($array[$f]['no_of_package']) . "', unit ='" . $this->db->escape($array[$f]['unit']) . "', net_wt ='" . $this->db->escape($array[$f]['net_wt']) . "', mode ='" . $this->db->escape($array[$f]['mode']) . "', org_eta_date ='" . $this->db->escape($array[$f]['org_eta_date']) . "', shipping_line_date ='" . $this->db->escape($array[$f]['shipping_line_date']) . "', tentative_eta_date ='" . $this->db->escape($array[$f]['tentative_eta_date']) . "', expected_date ='" . $this->db->escape($array[$f]['expected_date']) . "', invoice_no ='" . $this->db->escape($array[$f]['invoice_no']) . "', invoice_date ='" . $this->db->escape($array[$f]['invoice_date']) . "', mawb_no ='" . $this->db->escape($array[$f]['mawb_no']) . "', mawb_date ='" . $this->db->escape($array[$f]['mawb_date']) . "', be_no ='" . $this->db->escape($array[$f]['be_no']) . "', be_date ='" . $this->db->escape($array[$f]['be_date']) . "', hawb_no ='" . $this->db->escape($array[$f]['hawb_no']) . "', hawb_date ='" . $this->db->escape($array[$f]['hawb_date']) . "', airline ='" . $this->db->escape($array[$f]['airline']) . "', n_document_date ='" . $this->db->escape($array[$f]['n_document_date']) . "', org_doc_date ='" . $this->db->escape($array[$f]['org_doc_date']) . "', duty_inform_date ='" . $this->db->escape($array[$f]['duty_inform_date']) . "', duty_received_date ='" . $this->db->escape($array[$f]['duty_received_date']) . "', duty_paid_date ='" . $this->db->escape($array[$f]['duty_paid_date']) . "', total_duty ='" . $this->db->escape($array[$f]['total_duty']) . "', container_cleared_date ='" . $this->db->escape($array[$f]['container_cleared_date']) . "', detention_amt ='" . $this->db->escape($array[$f]['detention_amt']) . "', customer_remark ='" . $this->db->escape($array[$f]['customer_remark']) . "', delivery_location_remark ='" . $this->db->escape($array[$f]['delivery_location_remark']) . "', container_no ='" . $this->db->escape($array[$f]['container_no']) . "', free_period_shipping_date ='" . $this->db->escape($array[$f]['free_period_shipping_date']) . "', expected_free_dt_date ='" . $this->db->escape($array[$f]['expected_free_dt_date']) . "', expected_free_dt_remark ='" . $this->db->escape($array[$f]['expected_free_dt_remark']) . "'");
        }
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

        $this->response->setOutput($this->load->view('report/import_report_list', $data));

//	$this->response->redirect($this->url->link('module/importexport', 'user_token=' . $this->session->data['user_token'], true));
    }

//    public function getProductTable() {
//        $table_structure = $this->db->query("DESCRIBE " . DB_PREFIX . "temp_dsr");
//
//        if ($table_structure->num_rows) {
//
//            return $table_structure->rows;
//        }
//    }

//	public function getProductDescriptionTable(){
//		$table_structure_des=$this->db->query("DESCRIBE ". DB_PREFIX ."product_description");
//
//			if($table_structure_des->num_rows){
//
//			  return $table_structure_des->rows;
//
//			}
//	}

//    public function mapToCsv() {
//
//        $readable_dir = DIR_IMAGE . "temp/";
//
//        //  if(isset($this->request->post['maptocsv'])){
//
//        $fieldsname = $this->request->post['mapto'];
//
//
//
//        $uploadedFileName = $this->request->post['uploadedFileName'];
//
//        $handle = fopen($readable_dir . $uploadedFileName, "r");
//
//
//        $header = null;
//
//        while ($row = fgetcsv($handle)) {
//
//            if ($header === null) {
//
//                $header = $row;
//
//                continue;
//            }
//            $data['headercsv'] = $header;
//
//            $data['products'][] = array_combine($fieldsname, $row);
//        }
////var_dump($data['products']);
////exit;
//        //	}
//
//        foreach ($data['products'] as $key => $value) {
//            $this->addCsvProducts($value);
//        }
//        $this->response->setOutput($this->load->view('report/import_report_list', $data));
//    }
//
//    public function addCsvProducts($data) {
//
////		if(isset($data['model'])){
////			$data['model']=$data['model'];
////		}else{
////			$data['model']="";
////		}
////		if(isset($data['sku'])){
////			$data['sku']=$data['sku'];
////		}else{
////			$data['sku']="";
////		}
////		if(isset($data['upc'])){
////			$data['upc']=$data['upc'];
////		}else{
////			$data['upc']="";
////		}
////		if(isset($data['ean'])){
////			$data['ean']=$data['ean'];
////		}else{
////			$data['ean']="";
////		}
////		if(isset($data['jan'])){
////			$data['jan']=$data['jan'];
////		}else{
////			$data['jan']="";
////		}
////		if(isset($data['isbn'])){
////			$data['isbn']=$data['isbn'];
////		}else{
////			$data['isbn']="";
////		}
////		if(isset($data['mpn'])){
////			$data['mpn']=$data['mpn'];
////		}else{
////			$data['mpn']="";
////		}
////		if(isset($data['location'])){
////			$data['location']=$data['location'];
////		}else{
////			$data['location']="";
////		}
////		if(isset($data['quantity'])){
////			$data['quantity']=$data['quantity'];
////		}else{
////			$data['quantity']=0;
////		}
////		if(isset($data['minimum'])){
////			$data['minimum']=$data['minimum'];
////		}else{
////			$data['minimum']="";
////		}
////		if(isset($data['subtract'])){
////			$data['subtract']=$data['subtract'];
////		}else{
////			$data['subtract']="";
////		}
////		if(isset($data['stock_status_id'])){
////			$data['stock_status_id']=$data['stock_status_id'];
////		}else{
////			$data['stock_status_id']="";
////		}
////		if(isset($data['date_available'])){
////			$data['date_available']=$data['date_available'];
////		}else{
////			$data['date_available']=date('Y-m-d');
////		}
////		if(isset($data['manufacturer_id'])){
////			$data['manufacturer_id']=$data['manufacturer_id'];
////		}else{
////			$data['manufacturer_id']=0;
////		}
////		if(isset($data['shipping'])){
////			$data['shipping']=$data['shipping'];
////		}else{
////			$data['shipping']=0;
////		}
////		if(isset($data['price'])){
////			$data['price']=$data['price'];
////		}else{
////			$data['price']=0;
////		}
////		if(isset($data['points'])){
////			$data['points']=$data['points'];
////		}else{
////			$data['points']=0;
////		}
////		if(isset($data['weight'])){
////			$data['weight']=$data['weight'];
////			$datap['weight_class_id']=2;
////		}else{
////			$data['weight']=0;
////			$data['weight_class_id']=0;
////		}
////		if(isset($data['weight'])){
////			$data['weight_class_id']=2;
////		}else{
////			$data['weight_class_id']=0;
////		}
////		if(isset($data['length'])){
////			$data['length']=$data['length'];
////		}else{
////			$data['length']=0;
////		}
////		if(isset($data['width'])){
////			$data['width']=$data['width'];
////		}else{
////			$data['width']=0;
////		}
////		if(isset($data['height'])){
////			$data['height']=$data['height'];
////		}else{
////			$data['height']=0;
////		}
////		if(isset($data['length_class_id'])){
////			$data['length_class_id']=$data['length_class_id'];
////		}else{
////			$data['length_class_id']="";
////		}
////		if(isset($data['status'])&&$data['status']=='true'){
////			$data['status']=1;
////		}else{
////			$data['status']=0;
////		}
////		if(isset($data['tax_class_id'])&&$data['tax_class_id']=='true'){
////			$data['tax_class_id']=9;
////		}else{
////			$data['tax_class_id']=0;
////		}
////		if(isset($data['sort_order'])){
////			$data['sort_order']=$data['sort_order'];
////		}else{
////			$data['sort_order']="";
////		}
////		if(isset($data['name'])){
////			$data['name']=$data['name'];
////		}else{
////			$data['name']="";
////		}
////		if(isset($data['description'])){
////			$data['description']=$data['description'];
////		}else{
////			$data['description']="";
////		}
////		if(isset($data['tag'])){
////			$data['tag']=$data['tag'];
////		}else{
////			$data['tag']="";
////		}
////		if(isset($data['meta_title'])){
////			$data['meta_title']=$data['meta_title'];
////		}else{
////			$data['meta_title']="";
////		}
////		if(isset($data['meta_keyword'])){
////			$data['meta_keyword']=$data['meta_keyword'];
////		}else{
////			$data['meta_keyword']="";
////		}
////		if(isset($data['meta_description'])){
////			$data['meta_description']=$data['meta_description'];
////		}else{
////			$data['meta_description']="";
////		}
//
//        $query = $this->db->query("INSERT INTO " . DB_PREFIX . "temp_dsr SET customer_id = '" . (int) $data['customer_id'] . "', job_no = '" . $this->db->escape($data['job_no']) . "', igm_no ='" . $this->db->escape($data['igm_no']) . "', igm_date ='" . $this->db->escape($data['igm_date']) . "', po_no ='" . $this->db->escape($data['po_no']) . "', shipper ='" . $this->db->escape($data['shipper']) . "', be_heading ='" . $this->db->escape($data['be_heading']) . "', no_of_package ='" . $this->db->escape($data['no_of_package']) . "', unit ='" . $this->db->escape($data['unit']) . "', net_wt ='" . $this->db->escape($data['net_wt']) . "', mode ='" . $this->db->escape($data['mode']) . "', org_eta_date ='" . $this->db->escape($data['org_eta_date']) . "', shipping_line_date ='" . $this->db->escape($data['shipping_line_date']) . "', tentative_eta_date ='" . $this->db->escape($data['tentative_eta_date']) . "', expected_date ='" . $this->db->escape($data['expected_date']) . "', invoice_no ='" . $this->db->escape($data['invoice_no']) . "', invoice_date ='" . $this->db->escape($data['invoice_date']) . "', mawb_no ='" . $this->db->escape($data['mawb_no']) . "', mawb_date ='" . $this->db->escape($data['mawb_date']) . "', be_no ='" . $this->db->escape($data['be_no']) . "', be_date ='" . $this->db->escape($data['be_date']) . "', hawb_no ='" . $this->db->escape($data['hawb_no']) . "', hawb_date ='" . $this->db->escape($data['hawb_date']) . "', airline ='" . $this->db->escape($data['airline']) . "', n_document_date ='" . $this->db->escape($data['n_document_date']) . "', org_doc_date ='" . $this->db->escape($data['org_doc_date']) . "', duty_inform_date ='" . $this->db->escape($data['duty_inform_date']) . "', duty_received_date ='" . $this->db->escape($data['duty_received_date']) . "', duty_paid_date ='" . $this->db->escape($data['duty_paid_date']) . "', total_duty ='" . $this->db->escape($data['total_duty']) . "', container_cleared_date ='" . $this->db->escape($data['container_cleared_date']) . "', detention_amt ='" . $this->db->escape($data['detention_amt']) . "', customer_remark ='" . $this->db->escape($data['customer_remark']) . "', delivery_location_remark ='" . $this->db->escape($data['delivery_location_remark']) . "', container_no ='" . $this->db->escape($data['container_no']) . "', free_period_shipping_date ='" . $this->db->escape($data['free_period_shipping_date']) . "', expected_free_dt_date ='" . $this->db->escape($data['expected_free_dt_date']) . "', expected_free_dt_remark ='" . $this->db->escape($data['expected_free_dt_remark']) . "'");
//
////		$this->db->query("INSERT INTO " . DB_PREFIX . "product SET model = '" . $this->db->escape($data['model']) . "', sku = '" . $this->db->escape($data['sku']) . "', upc = '" . $this->db->escape($data['upc']) . "', ean = '" . $this->db->escape($data['ean']) . "', jan = '" . $this->db->escape($data['jan']) . "', isbn = '" . $this->db->escape($data['isbn']) . "', mpn = '" . $this->db->escape($data['mpn']) . "', location = '" . $this->db->escape($data['location']) . "', quantity = '" . (int)$data['quantity'] . "', minimum = '" . (int)$data['minimum'] . "', subtract = '" . (int)$data['subtract'] . "', stock_status_id = '" . (int)$data['stock_status_id'] . "', date_available = '" . $this->db->escape($data['date_available']) . "', manufacturer_id = '" . (int)$data['manufacturer_id'] . "', shipping = '" . (int)$data['shipping'] . "', price = '" . (float)$data['price'] . "', points = '" . (int)$data['points'] . "', weight = '" . (float)$data['weight'] . "', weight_class_id = '" . (int)$data['weight_class_id'] . "', length = '" . (float)$data['length'] . "', width = '" . (float)$data['width'] . "', height = '" . (float)$data['height'] . "', length_class_id = '" . (int)$data['length_class_id'] . "', status = '" . (int)$data['status'] . "', tax_class_id = '" . (int)$data['tax_class_id'] . "', sort_order = '" . (int)$data['sort_order'] . "', date_added = NOW()");
////        $product_id = $this->db->getLastId();
////
////		if (isset($data['image'])) {
////			$serv1 = stripos($data['image'], 'http://');
////			$serv2 = stripos($data['image'], 'https://');
////			if($serv1 !== false || $serv2 !== false){
////	    		$url = $data['image'];
////          $csvimg = DIR_IMAGE.'catalog/'.$product_id.'_'.date('Ymdhis').'_csvimages.jpg';
////					$data['image']='catalog/'.$product_id.'_'.date('Ymdhis').'_csvimages.jpg';
////          file_put_contents($csvimg, file_get_contents($url));
////				}else{
////				$data['image']=	$data['image'];
////				}
////			$this->db->query("UPDATE " . DB_PREFIX . "product SET image = '" . $this->db->escape($data['image']) . "' WHERE product_id = '" . (int)$product_id . "'");
////		}
////	//	foreach ($data['product_description'] as $language_id => $value) {
////$language_id=1;
////			$this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($data['name']) . "', description = '" . $this->db->escape($data['description']) . "', tag = '" . $this->db->escape($data['tag']) . "', meta_title = '" . $this->db->escape($data['meta_title']) . "', meta_description = '" . $this->db->escape($data['meta_description']) . "', meta_keyword = '" . $this->db->escape($data['meta_keyword']) . "'");
////	//	}
////$store_id=0;
////    	$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "'");
////
////		if (isset($data['category'])) {
////			$product_category=explode(",",$data['category']);
////			foreach ($product_category as $category_id) {
////				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "'");
////			}
////		}
////
////		if (isset($data['product_image'])) {
////			$product_images=explode(",",$data['product_image']);
////			foreach ($product_images as $product_image) {
////				$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape($product_image) . "', sort_order = 0");
////			}
////		}
////
////		if (isset($data['keyword'])) {
////			$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'product_id=" . (int)$product_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
////		}
//        return $query;
//    }
//
//    protected function validate() {
//        if (!$this->user->hasPermission('modify', 'module/importexport')) {
//            $this->error['warning'] = $this->language->get('error_permission');
//        }
//
//        return !$this->error;
//    }

}
