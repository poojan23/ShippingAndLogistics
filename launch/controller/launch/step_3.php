<?php

class ControllerLaunchStep3 extends PT_Controller
{
    private $error = array();

    public function index()
    {
        $this->load->language('launch/step_3');

        $this->load->model('launch/launch');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_launch_launch->addUser($this->request->post);

            # Template Config.php
            $output  = '<?php' . "\n";
            $output .= '# HTTP' . "\n";
            $output .= 'define(\'HTTP_SERVER\', \'' . HTTP_CATALOG . '\');' . "\n\n";

            $output .= '# HTTPS' . "\n";
            $output .= 'define(\'HTTPS_SERVER\', \'' . HTTPS_CATALOG . '\');' . "\n\n";

            $output .= '# DIR' . "\n";
            $output .= 'define(\'DIR_APPLICATION\', \'' . addslashes(DIR_ROOT) . 'template/\');' . "\n";
            $output .= 'define(\'DIR_SYSTEM\', \'' . addslashes(DIR_ROOT) . 'system/\');' . "\n";
            $output .= 'define(\'DIR_IMAGE\', \'' . addslashes(DIR_ROOT) . 'image/\');' . "\n";
            $output .= 'define(\'DIR_STORAGE\', DIR_SYSTEM . \'storage/\');' . "\n";
            $output .= 'define(\'DIR_LANGUAGE\', DIR_APPLICATION . \'language/\');' . "\n";
            $output .= 'define(\'DIR_TEMPLATE\', DIR_APPLICATION . \'view/html/\');' . "\n";
            $output .= 'define(\'DIR_CONFIG\', DIR_SYSTEM . \'config/\');' . "\n";
            $output .= 'define(\'DIR_CACHE\', DIR_STORAGE . \'cache/\');' . "\n";
            $output .= 'define(\'DIR_DOWNLOAD\', DIR_STORAGE . \'download/\');' . "\n";
            $output .= 'define(\'DIR_LOGS\', DIR_STORAGE . \'logs/\');' . "\n";
            $output .= 'define(\'DIR_MODIFICATION\', DIR_STORAGE . \'modification/\');' . "\n";
            $output .= 'define(\'DIR_SESSION\', DIR_STORAGE . \'session/\');' . "\n";
            $output .= 'define(\'DIR_UPLOAD\', DIR_STORAGE . \'upload/\');' . "\n\n";

            $output .= '# DB' . "\n";
            $output .= 'define(\'DB_DRIVER\', \'' . DB_DRIVER . '\');' . "\n";
            $output .= 'define(\'DB_HOSTNAME\', \'' . DB_HOSTNAME . '\');' . "\n";
            $output .= 'define(\'DB_USERNAME\', \'' . DB_USERNAME . '\');' . "\n";
            $output .= 'define(\'DB_PASSWORD\', \'' . DB_PASSWORD . '\');' . "\n";
            $output .= 'define(\'DB_DATABASE\', \'' . DB_DATABASE . '\');' . "\n";
            $output .= 'define(\'DB_PORT\', \'' . DB_PORT . '\');' . "\n";
            $output .= 'define(\'DB_PREFIX\', \'' . DB_PREFIX . '\');';

            $file = fopen(DIR_ROOT . 'config.php', 'w');

            fwrite($file, $output);

            fclose($file);

            # Admin config.php
            $output  = '<?php' . "\n";
            $output .= '# HTTP' . "\n";
            $output .= 'define(\'HTTP_SERVER\', \'' . HTTP_CATALOG . 'admin/\');' . "\n";
            $output .= 'define(\'HTTP_CATALOG\', \'' . HTTP_CATALOG . '\');' . "\n\n";

            $output .= '# HTTPS' . "\n";
            $output .= 'define(\'HTTPS_SERVER\', \'' . HTTPS_CATALOG . 'admin/\');' . "\n";
            $output .= 'define(\'HTTPS_CATALOG\', \'' . HTTPS_CATALOG . '\');' . "\n\n";

            $output .= '# DIR' . "\n";
            $output .= 'define(\'DIR_APPLICATION\', \'' . addslashes(DIR_ROOT) . 'admin/\');' . "\n";
            $output .= 'define(\'DIR_SYSTEM\', \'' . addslashes(DIR_ROOT) . 'system/\');' . "\n";
            $output .= 'define(\'DIR_IMAGE\', \'' . addslashes(DIR_ROOT) . 'image/\');' . "\n";
            $output .= 'define(\'DIR_STORAGE\', DIR_SYSTEM . \'storage/\');' . "\n";
            $output .= 'define(\'DIR_CATALOG\', \'' . addslashes(DIR_ROOT) . 'template/\');' . "\n";
            $output .= 'define(\'DIR_LANGUAGE\', DIR_APPLICATION . \'language/\');' . "\n";
            $output .= 'define(\'DIR_TEMPLATE\', DIR_APPLICATION . \'view/html/\');' . "\n";
            $output .= 'define(\'DIR_CONFIG\', DIR_SYSTEM . \'config/\');' . "\n";
            $output .= 'define(\'DIR_CACHE\', DIR_STORAGE . \'cache/\');' . "\n";
            $output .= 'define(\'DIR_DOWNLOAD\', DIR_STORAGE . \'download/\');' . "\n";
            $output .= 'define(\'DIR_LOGS\', DIR_STORAGE . \'logs/\');' . "\n";
            $output .= 'define(\'DIR_MODIFICATION\', DIR_STORAGE . \'modification/\');' . "\n";
            $output .= 'define(\'DIR_SESSION\', DIR_STORAGE . \'session/\');' . "\n";
            $output .= 'define(\'DIR_UPLOAD\', DIR_STORAGE . \'upload/\');' . "\n\n";

            $output .= '# DB' . "\n";
            $output .= 'define(\'DB_DRIVER\', \'' . addslashes(DB_DRIVER) . '\');' . "\n";
            $output .= 'define(\'DB_HOSTNAME\', \'' . addslashes(DB_HOSTNAME) . '\');' . "\n";
            $output .= 'define(\'DB_USERNAME\', \'' . addslashes(DB_USERNAME) . '\');' . "\n";
            $output .= 'define(\'DB_PASSWORD\', \'' . addslashes(html_entity_decode(DB_PASSWORD, ENT_QUOTES, 'UTF-8')) . '\');' . "\n";
            $output .= 'define(\'DB_DATABASE\', \'' . addslashes(DB_DATABASE) . '\');' . "\n";
            $output .= 'define(\'DB_PORT\', \'' . addslashes(DB_PORT) . '\');' . "\n";
            $output .= 'define(\'DB_PREFIX\', \'' . addslashes(DB_PREFIX) . '\');' . "\n\n";

            $output .= '# POPAYA API' . "\n";
            $output .= 'define(\'POPAYA_SERVER\', \'https://www.popaya.in/\');' . "\n";

            $file = fopen(DIR_ROOT . 'admin/config.php', 'w');

            fwrite($file, $output);

            fclose($file);

            $this->response->redirect($this->url->link('launch/step_4'));
        }

        $this->document->setTitle($this->language->get('heading_title'));

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_step_3'] = $this->language->get('text_step_3');
        $data['text_personal'] = $this->language->get('text_personal');
        $data['text_address'] = $this->language->get('text_address');
        $data['text_db_administration'] = $this->language->get('text_db_administration');
        $data['text_select'] = $this->language->get('text_select');
        $data['text_none'] = $this->language->get('text_none');

        $data['entry_firstname'] = $this->language->get('entry_firstname');
        $data['entry_lastname'] = $this->language->get('entry_lastname');
        $data['entry_telephone'] = $this->language->get('entry_telephone');
        $data['entry_fax'] = $this->language->get('entry_fax');
        $data['entry_company'] = $this->language->get('entry_company');
        $data['entry_address_1'] = $this->language->get('entry_address_1');
        $data['entry_address_2'] = $this->language->get('entry_address_2');
        $data['entry_city'] = $this->language->get('entry_city');
        $data['entry_postcode'] = $this->language->get('entry_postcode');
        $data['entry_country'] = $this->language->get('entry_country');
        $data['entry_zone'] = $this->language->get('entry_zone');
        $data['entry_email'] = $this->language->get('entry_email');
        $data['entry_password'] = $this->language->get('entry_password');

        $data['button_continue'] = $this->language->get('button_continue');
        $data['button_back'] = $this->language->get('button_back');

        if (isset($this->error['warning'])) {
            $data['warning_err'] = $this->error['warning'];
        } else {
            $data['warning_err'] = '';
        }

        if (isset($this->error['firstname'])) {
            $data['firstname_err'] = $this->error['firstname'];
        } else {
            $data['firstname_err'] = '';
        }

        if (isset($this->error['lastname'])) {
            $data['lastname_err'] = $this->error['lastname'];
        } else {
            $data['lastname_err'] = '';
        }

        if (isset($this->error['telephone'])) {
            $data['telephone_err'] = $this->error['telephone'];
        } else {
            $data['telephone_err'] = '';
        }

        if (isset($this->error['country_id'])) {
            $data['country_id_err'] = $this->error['country_id'];
        } else {
            $data['country_id_err'] = '';
        }

        if (isset($this->error['address_1'])) {
            $data['address_1_err'] = $this->error['address_1'];
        } else {
            $data['address_1_err'] = '';
        }

        if (isset($this->error['address_2'])) {
            $data['address_2_err'] = $this->error['address_2'];
        } else {
            $data['address_2_err'] = '';
        }

        if (isset($this->error['city'])) {
            $data['city_err'] = $this->error['city'];
        } else {
            $data['city_err'] = '';
        }

        if (isset($this->error['zone_id'])) {
            $data['zone_id_err'] = $this->error['zone_id'];
        } else {
            $data['zone_id_err'] = '';
        }

        if (isset($this->error['postcode'])) {
            $data['postcode_err'] = $this->error['postcode'];
        } else {
            $data['postcode_err'] = '';
        }

        if (isset($this->error['email'])) {
            $data['email_err'] = $this->error['email'];
        } else {
            $data['email_err'] = '';
        }

        if (isset($this->error['password'])) {
            $data['password_err'] = $this->error['password'];
        } else {
            $data['password_err'] = '';
        }

        $data['action'] = $this->url->link('launch/step_3');

        if (isset($this->request->post['firstname'])) {
            $data['firstname'] = $this->request->post['firstname'];
        } else {
            $data['firstname'] = '';
        }

        if (isset($this->request->post['lastname'])) {
            $data['lastname'] = $this->request->post['lastname'];
        } else {
            $data['lastname'] = '';
        }

        if (isset($this->request->post['telephone'])) {
            $data['telephone'] = $this->request->post['telephone'];
        } else {
            $data['telephone'] = '';
        }

        if (isset($this->request->post['fax'])) {
            $data['fax'] = $this->request->post['fax'];
        } else {
            $data['fax'] = '';
        }

        if (isset($this->request->post['company'])) {
            $data['company'] = $this->request->post['company'];
        } else {
            $data['company'] = '';
        }

        $data['countries'] = $this->model_launch_launch->getCountries();

        if (isset($this->request->post['country_id'])) {
            $data['country_id'] = $this->request->post['country_id'];
        } else {
            $data['country_id'] = '';
        }

        if (isset($this->request->post['address_1'])) {
            $data['address_1'] = $this->request->post['address_1'];
        } else {
            $data['address_1'] = '';
        }

        if (isset($this->request->post['address_2'])) {
            $data['address_2'] = $this->request->post['address_2'];
        } else {
            $data['address_2'] = '';
        }

        if (isset($this->request->post['city'])) {
            $data['city'] = $this->request->post['city'];
        } else {
            $data['city'] = '';
        }

        if (isset($this->request->post['postcode'])) {
            $data['postcode'] = $this->request->post['postcode'];
        } else {
            $data['postcode'] = '';
        }

        if (isset($this->request->post['email'])) {
            $data['email'] = $this->request->post['email'];
        } else {
            $data['email'] = '';
        }

        if (isset($this->request->post['password'])) {
            $data['password'] = $this->request->post['password'];
        } else {
            $data['password'] = '';
        }

        $data['back'] = $this->url->link('launch/step_2');

        $data['header'] = $this->load->controller('common/header');
        $data['nav'] = $this->load->controller('common/nav');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('launch/step_3', $data));
    }

    private function validate()
    {
        if (!$this->request->post['firstname']) {
            $this->error['firstname'] = $this->language->get('error_firstname');
        }

        if (!$this->request->post['lastname']) {
            $this->error['lastname'] = $this->language->get('error_lastname');
        }

        if (!$this->request->post['telephone']) {
            $this->error['telephone'] = $this->language->get('error_telephone');
        }

        if (!$this->request->post['country_id'] || $this->request->post['country_id'] == '') {
            $this->error['country_id'] = $this->language->get('error_country');
        }

        if (!$this->request->post['address_1']) {
            $this->error['address_1'] = $this->language->get('error_address_1');
        } else {
            if (utf8_strlen($this->request->post['address_1']) > 128) {
                $this->error['address_1'] = $this->language->get('error_address_1_limit');
            }
        }

        if (!empty($this->request->post['address_2'])) {
            if (utf8_strlen($this->request->post['address_2']) > 128) {
                $this->error['address_2'] = $this->language->get('error_address_2_limit');
            }
        }

        if (!$this->request->post['city']) {
            $this->error['city'] = $this->language->get('error_city');
        }

        if (!$this->request->post['zone_id'] || $this->request->post['zone_id'] == '') {
            $this->error['zone_id'] = $this->language->get('error_zone');
        }

        if (!$this->request->post['postcode']) {
            $this->error['postcode'] = $this->language->get('error_postcode');
        }

        if ((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
            $this->error['email'] = $this->language->get('error_email');
        }

        if (!$this->request->post['password']) {
            $this->error['password'] = $this->language->get('error_password');
        }

        if (!is_writable(DIR_ROOT . 'config.php')) {
            $this->error['warning'] = $this->language->get('error_config') . DIR_ROOT . 'config.php';
        }

        if (!is_writable(DIR_ROOT . 'admin/config.php')) {
            $this->error['warning'] = $this->language->get('error_config') . DIR_ROOT . 'admin/config.php';
        }

        return !$this->error;
    }

    public function country()
    {
        $json = array();

        $this->load->model('launch/launch');

        $country_info = $this->model_launch_launch->getCountry($this->request->get['country_id']);

        if ($country_info) {
            $json = array(
                'country_id'        => $country_info['country_id'],
                'name'              => $country_info['name'],
                'iso_code_2'        => $country_info['iso_code_2'],
                'iso_code_3'        => $country_info['iso_code_3'],
                'address_format'    => $country_info['address_format'],
                'postcode_required' => $country_info['postcode_required'],
                'zone'              => $this->model_launch_launch->getZonesByCountryId($country_info['country_id']),
                'status'            => $country_info['status']
            );
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}
