<?php

class ControllerReportDsr extends PT_Controller {

    private $error = array();

    public function index() {
        $this->load->language('report/dsr');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('report/dsr');

        $this->getList();
    }

    public function getList() {

        $this->document->addStyle("template/view/dist/plugins/DataTables/DataTables-1.10.18/css/dataTables.bootstrap4.min.css");
        $this->document->addStyle("template/view/dist/plugins/DataTables/Buttons-1.5.6/css/buttons.bootstrap4.min.css");
        $this->document->addStyle("template/view/dist/plugins/DataTables/FixedHeader-3.1.4/css/fixedHeader.bootstrap4.min.css");
        $this->document->addStyle("template/view/dist/plugins/DataTables/Responsive-2.2.2/css/responsive.bootstrap4.min.css");
        $this->document->addScript("template/view/dist/plugins/DataTables/DataTables-1.10.18/js/jquery.dataTables.min.js");
        $this->document->addScript("template/view/dist/plugins/DataTables/DataTables-1.10.18/js/dataTables.bootstrap4.min.js");
        $this->document->addScript("template/view/dist/plugins/DataTables/Buttons-1.5.6/js/dataTables.buttons.min.js");
        $this->document->addScript("template/view/dist/plugins/DataTables/Buttons-1.5.6/js/buttons.bootstrap4.min.js");
        $this->document->addScript("template/view/dist/plugins/DataTables/JSZip-2.5.0/jszip.min.js");
        $this->document->addScript("template/view/dist/plugins/DataTables/pdfmake-0.1.36/pdfmake.min.js");
        $this->document->addScript("template/view/dist/plugins/DataTables/pdfmake-0.1.36/vfs_fonts.js");
        $this->document->addScript("template/view/dist/plugins/DataTables/Buttons-1.5.6/js/buttons.html5.min.js");
        $this->document->addScript("template/view/dist/plugins/DataTables/Buttons-1.5.6/js/buttons.print.min.js");
        $this->document->addScript("template/view/dist/plugins/DataTables/Buttons-1.5.6/js/buttons.colVis.min.js");
        $this->document->addScript("template/view/dist/plugins/DataTables/FixedHeader-3.1.4/js/dataTables.fixedHeader.min.js");
        $this->document->addScript("template/view/dist/plugins/DataTables/FixedHeader-3.1.4/js/fixedHeader.bootstrap4.min.js");
        $this->document->addScript("template/view/dist/plugins/DataTables/Responsive-2.2.2/js/dataTables.responsive.min.js");
        $this->document->addScript("template/view/dist/plugins/DataTables/Responsive-2.2.2/js/responsive.bootstrap4.min.js");

        if (!$this->customer->isLogged()) {
            $this->response->redirect($this->url->link('customer/login'));
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_dashboard'),
            'href' => $this->url->link('customer/dashboard')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('report/dsr')
        );

        $this->load->model('report/dsr');

        $data['fields'] = array();

        $fields = array();

        $custom_fields = $this->model_report_dsr->getCustomFields($this->customer->getId());

        foreach ($custom_fields as $result) {
            $fields[] = $result['field_name'];

            $data['fields'][] = array(
                'key' => $result['field_name'],
                'name' => (!empty($result['name']) ? $result['name'] : ucwords(str_replace('_', ' ', $result['field_name'])))
            );
        }

        $data['dsrs'] = array();

        $value = array();

        $dsrs = $this->model_report_dsr->getDSR($this->customer->getId());

        foreach ($dsrs as $dsr) {
            
        }


        $custom_fields = array();

        $results = $this->model_report_dsr->compareValues($this->customer->getId());

        foreach ($results as $result) {
            $custom_fields[] = $result['COLUMN_NAME'];
        }

        $data['array_new'] = [];

        for ($i = 0; $i < count($dsrs); $i++) {
            for ($j = 0; $j < count($custom_fields); $j++) {
                if (array_key_exists($custom_fields[$j], $dsrs[$i])) {
                    $data['array_new'][$i][] = $dsrs[$i][$custom_fields[$j]];
                }
            }
        }

//        echo "<pre>";
//
////        print_r($data['fields']);
//        print_r($data['array_new']);
//        exit;


        if (isset($this->request->post['selected'])) {
            $data['selected'] = (array) $this->request->post['selected'];
        } else {
            $data['selected'] = array();
        }

        $data['customer_id'] = $this->customer->getId();
        $data['customer_name'] = $this->customer->getFirstName();
        $data['area_id'] = $this->customer->getAreaId();
        $data['mobile'] = $this->customer->getMobile();
        $data['email'] = $this->customer->getEmail();

        $data['header'] = $this->load->controller('common/header');
        $data['nav'] = $this->load->controller('common/nav');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('report/dsr_list', $data));
    }

}
