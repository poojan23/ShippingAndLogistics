<?php
class ControllerModuleImportexport extends PT_Controller {
	private $error = array();

	public function index() {
		$this->load->language('module/importexport');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('importexport', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], true));
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
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_module'),
			'href' => $this->url->link('extension/module', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('module/importexport', 'token=' . $this->session->data['token'], true)
		);

		$data['action'] = $this->url->link('module/importexport/uploadcsv', 'token=' . $this->session->data['token'], true);

		$data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], true);

		if (isset($this->request->post['importexport_status'])) {
			$data['importexport_status'] = $this->request->post['importexport_status'];
		} else {
			$data['importexport_status'] = $this->config->get('importexport_status');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('module/importexport', $data));
	}

	public function uploadcsv(){
		$this->load->language('module/importexport');

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
					'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
				);

				$data['breadcrumbs'][] = array(
					'text' => $this->language->get('text_module'),
					'href' => $this->url->link('extension/module', 'token=' . $this->session->data['token'], true)
				);

				$data['breadcrumbs'][] = array(
					'text' => $this->language->get('heading_title'),
					'href' => $this->url->link('module/importexport', 'token=' . $this->session->data['token'], true)
				);

				$data['action'] = $this->url->link('module/importexport/mapToCsv', 'token=' . $this->session->data['token'], true);

				$data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], true);


				$data['products']=array();

				$uploads_dir = DIR_IMAGE."temp/";

				$tmp_name = $_FILES["uploadcsvfile"]["tmp_name"];

				$name = basename($_FILES["uploadcsvfile"]["name"]);

				$randfilename=date('Ymdhis');

				move_uploaded_file($tmp_name, $uploads_dir.$randfilename.$name);

				$handle=fopen($uploads_dir.$randfilename.$name,"r");


				$header = null;
		    while ($row = fgetcsv($handle)) {

			    if ($header === null) {

			        $header = $row;

			        continue;

		        }
				$data['headercsv']=$header;

		    $data['products'][]= array_combine($header, $row);

				}

				$data['uploadedFileName']=$randfilename.$name;

				$data['table_structure']=array();

			  $productTable=	$this->getProductTable();

			  $productDescriptionTable=	$this->getProductDescriptionTable();

				$data['table_structure'] = array_merge($productTable,$productDescriptionTable);



		/*foreach ($data['products'] as $key => $value) {
			$this->addCsvProducts($value);
		}*/

		  fclose($handle);

			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');

			$this->response->setOutput($this->load->view('module/importexportlist', $data));

//	$this->response->redirect($this->url->link('module/importexport', 'token=' . $this->session->data['token'], true));
	}

	public function getProductTable(){
		$table_structure=$this->db->query("DESCRIBE ". DB_PREFIX ."product");

			if($table_structure->num_rows){

			  return $table_structure->rows;

			}
	}

	public function getProductDescriptionTable(){
		$table_structure_des=$this->db->query("DESCRIBE ". DB_PREFIX ."product_description");

			if($table_structure_des->num_rows){

			  return $table_structure_des->rows;

			}
	}

	public function mapToCsv(){

		$readable_dir = DIR_IMAGE."temp/";

  //  if(isset($this->request->post['maptocsv'])){

			$fieldsname=$this->request->post['mapto'];



			$uploadedFileName=$this->request->post['uploadedFileName'];

			$handle=fopen($readable_dir.$uploadedFileName,"r");


			$header = null;

			while ($row = fgetcsv($handle)) {

				if ($header === null) {

						$header = $row;

						continue;

					}
			$data['headercsv']=$header;

			$data['products'][]= array_combine($fieldsname, $row);

			}
//var_dump($data['products']);
//exit;
	//	}

	foreach ($data['products'] as $key => $value) {
		$this->addCsvProducts($value);
	}
$this->response->redirect($this->url->link('module/importexport', 'token=' . $this->session->data['token'], true));
	}

	public function addCsvProducts($data){

		if(isset($data['model'])){
			$data['model']=$data['model'];
		}else{
			$data['model']="";
		}
		if(isset($data['sku'])){
			$data['sku']=$data['sku'];
		}else{
			$data['sku']="";
		}
		if(isset($data['upc'])){
			$data['upc']=$data['upc'];
		}else{
			$data['upc']="";
		}
		if(isset($data['ean'])){
			$data['ean']=$data['ean'];
		}else{
			$data['ean']="";
		}
		if(isset($data['jan'])){
			$data['jan']=$data['jan'];
		}else{
			$data['jan']="";
		}
		if(isset($data['isbn'])){
			$data['isbn']=$data['isbn'];
		}else{
			$data['isbn']="";
		}
		if(isset($data['mpn'])){
			$data['mpn']=$data['mpn'];
		}else{
			$data['mpn']="";
		}
		if(isset($data['location'])){
			$data['location']=$data['location'];
		}else{
			$data['location']="";
		}
		if(isset($data['quantity'])){
			$data['quantity']=$data['quantity'];
		}else{
			$data['quantity']=0;
		}
		if(isset($data['minimum'])){
			$data['minimum']=$data['minimum'];
		}else{
			$data['minimum']="";
		}
		if(isset($data['subtract'])){
			$data['subtract']=$data['subtract'];
		}else{
			$data['subtract']="";
		}
		if(isset($data['stock_status_id'])){
			$data['stock_status_id']=$data['stock_status_id'];
		}else{
			$data['stock_status_id']="";
		}
		if(isset($data['date_available'])){
			$data['date_available']=$data['date_available'];
		}else{
			$data['date_available']=date('Y-m-d');
		}
		if(isset($data['manufacturer_id'])){
			$data['manufacturer_id']=$data['manufacturer_id'];
		}else{
			$data['manufacturer_id']=0;
		}
		if(isset($data['shipping'])){
			$data['shipping']=$data['shipping'];
		}else{
			$data['shipping']=0;
		}
		if(isset($data['price'])){
			$data['price']=$data['price'];
		}else{
			$data['price']=0;
		}
		if(isset($data['points'])){
			$data['points']=$data['points'];
		}else{
			$data['points']=0;
		}
		if(isset($data['weight'])){
			$data['weight']=$data['weight'];
			$datap['weight_class_id']=2;
		}else{
			$data['weight']=0;
			$data['weight_class_id']=0;
		}
		if(isset($data['weight'])){
			$data['weight_class_id']=2;
		}else{
			$data['weight_class_id']=0;
		}
		if(isset($data['length'])){
			$data['length']=$data['length'];
		}else{
			$data['length']=0;
		}
		if(isset($data['width'])){
			$data['width']=$data['width'];
		}else{
			$data['width']=0;
		}
		if(isset($data['height'])){
			$data['height']=$data['height'];
		}else{
			$data['height']=0;
		}
		if(isset($data['length_class_id'])){
			$data['length_class_id']=$data['length_class_id'];
		}else{
			$data['length_class_id']="";
		}
		if(isset($data['status'])&&$data['status']=='true'){
			$data['status']=1;
		}else{
			$data['status']=0;
		}
		if(isset($data['tax_class_id'])&&$data['tax_class_id']=='true'){
			$data['tax_class_id']=9;
		}else{
			$data['tax_class_id']=0;
		}
		if(isset($data['sort_order'])){
			$data['sort_order']=$data['sort_order'];
		}else{
			$data['sort_order']="";
		}
		if(isset($data['name'])){
			$data['name']=$data['name'];
		}else{
			$data['name']="";
		}
		if(isset($data['description'])){
			$data['description']=$data['description'];
		}else{
			$data['description']="";
		}
		if(isset($data['tag'])){
			$data['tag']=$data['tag'];
		}else{
			$data['tag']="";
		}
		if(isset($data['meta_title'])){
			$data['meta_title']=$data['meta_title'];
		}else{
			$data['meta_title']="";
		}
		if(isset($data['meta_keyword'])){
			$data['meta_keyword']=$data['meta_keyword'];
		}else{
			$data['meta_keyword']="";
		}
		if(isset($data['meta_description'])){
			$data['meta_description']=$data['meta_description'];
		}else{
			$data['meta_description']="";
		}


		$this->db->query("INSERT INTO " . DB_PREFIX . "product SET model = '" . $this->db->escape($data['model']) . "', sku = '" . $this->db->escape($data['sku']) . "', upc = '" . $this->db->escape($data['upc']) . "', ean = '" . $this->db->escape($data['ean']) . "', jan = '" . $this->db->escape($data['jan']) . "', isbn = '" . $this->db->escape($data['isbn']) . "', mpn = '" . $this->db->escape($data['mpn']) . "', location = '" . $this->db->escape($data['location']) . "', quantity = '" . (int)$data['quantity'] . "', minimum = '" . (int)$data['minimum'] . "', subtract = '" . (int)$data['subtract'] . "', stock_status_id = '" . (int)$data['stock_status_id'] . "', date_available = '" . $this->db->escape($data['date_available']) . "', manufacturer_id = '" . (int)$data['manufacturer_id'] . "', shipping = '" . (int)$data['shipping'] . "', price = '" . (float)$data['price'] . "', points = '" . (int)$data['points'] . "', weight = '" . (float)$data['weight'] . "', weight_class_id = '" . (int)$data['weight_class_id'] . "', length = '" . (float)$data['length'] . "', width = '" . (float)$data['width'] . "', height = '" . (float)$data['height'] . "', length_class_id = '" . (int)$data['length_class_id'] . "', status = '" . (int)$data['status'] . "', tax_class_id = '" . (int)$data['tax_class_id'] . "', sort_order = '" . (int)$data['sort_order'] . "', date_added = NOW()");

		$product_id = $this->db->getLastId();

		if (isset($data['image'])) {
			$serv1 = stripos($data['image'], 'http://');
			$serv2 = stripos($data['image'], 'https://');
			if($serv1 !== false || $serv2 !== false){
	    		$url = $data['image'];
          $csvimg = DIR_IMAGE.'catalog/'.$product_id.'_'.date('Ymdhis').'_csvimages.jpg';
					$data['image']='catalog/'.$product_id.'_'.date('Ymdhis').'_csvimages.jpg';
          file_put_contents($csvimg, file_get_contents($url));
				}else{
				$data['image']=	$data['image'];
				}
			$this->db->query("UPDATE " . DB_PREFIX . "product SET image = '" . $this->db->escape($data['image']) . "' WHERE product_id = '" . (int)$product_id . "'");
		}

	//	foreach ($data['product_description'] as $language_id => $value) {
$language_id=1;
			$this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($data['name']) . "', description = '" . $this->db->escape($data['description']) . "', tag = '" . $this->db->escape($data['tag']) . "', meta_title = '" . $this->db->escape($data['meta_title']) . "', meta_description = '" . $this->db->escape($data['meta_description']) . "', meta_keyword = '" . $this->db->escape($data['meta_keyword']) . "'");
	//	}
$store_id=0;
    	$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "'");

		if (isset($data['category'])) {
			$product_category=explode(",",$data['category']);
			foreach ($product_category as $category_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "'");
			}
		}

		if (isset($data['product_image'])) {
			$product_images=explode(",",$data['product_image']);
			foreach ($product_images as $product_image) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape($product_image) . "', sort_order = 0");
			}
		}

		if (isset($data['keyword'])) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'product_id=" . (int)$product_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
		}

	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'module/importexport')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
