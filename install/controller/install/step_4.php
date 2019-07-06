<?php

class ControllerInstallStep4 extends PT_Controller
{
    private $error = array();

    public function index()
    {
        $this->load->language('install/step_4');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->load->model('install/install');

            $this->model_install_install->database($this->request->post);

            # Launch config.php
            $output  = '<?php' . "\n";
            $output .= '# HTTP' . "\n";
            $output .= 'define(\'HTTP_SERVER\', \'' . HTTP_POPAYA . 'launch/\');' . "\n";
            $output .= 'define(\'HTTP_CATALOG\', \'' . HTTP_POPAYA . '\');' . "\n\n";

            $output .= '# HTTPS' . "\n";
            $output .= 'define(\'HTTPS_SERVER\', \'' . HTTP_POPAYA . 'launch/\');' . "\n";
            $output .= 'define(\'HTTPS_CATALOG\', \'' . HTTP_POPAYA . '\');' . "\n\n";

            $output .= '# DIR' . "\n";
            $output .= 'define(\'DIR_ROOT\', \'' . addslashes(DIR_POPAYA) . '\');' . "\n";
            $output .= 'define(\'DIR_APPLICATION\', \'' . addslashes(DIR_POPAYA) . 'launch/\');' . "\n";
            $output .= 'define(\'DIR_SYSTEM\', \'' . addslashes(DIR_POPAYA) . 'system/\');' . "\n";
            $output .= 'define(\'DIR_IMAGE\', \'' . addslashes(DIR_POPAYA) . 'image/\');' . "\n";
            $output .= 'define(\'DIR_STORAGE\', DIR_SYSTEM . \'storage/\');' . "\n";
            $output .= 'define(\'DIR_CATALOG\', \'' . addslashes(DIR_POPAYA) . 'template/\');' . "\n";
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
            $output .= 'define(\'DB_DRIVER\', \'' . addslashes($this->request->post['db_driver']) . '\');' . "\n";
            $output .= 'define(\'DB_HOSTNAME\', \'' . addslashes($this->request->post['db_hostname']) . '\');' . "\n";
            $output .= 'define(\'DB_USERNAME\', \'' . addslashes($this->request->post['db_username']) . '\');' . "\n";
            $output .= 'define(\'DB_PASSWORD\', \'' . addslashes(html_entity_decode($this->request->post['db_password'], ENT_QUOTES, 'UTF-8')) . '\');' . "\n";
            $output .= 'define(\'DB_DATABASE\', \'' . addslashes($this->request->post['db_database']) . '\');' . "\n";
            $output .= 'define(\'DB_PORT\', \'' . addslashes($this->request->post['db_port']) . '\');' . "\n";
            $output .= 'define(\'DB_PREFIX\', \'' . addslashes($this->request->post['db_prefix']) . '\');' . "\n\n";

            $output .= '# POPAYA API' . "\n";
            $output .= 'define(\'POPAYA_SERVER\', \'https://www.popaya.in/\');' . "\n";

            $file = fopen(DIR_POPAYA . 'launch/config.php', 'w');

            fwrite($file, $output);

            fclose($file);

            # SAdmin config.php
            $output  = '<?php' . "\n";
            $output .= '# HTTP' . "\n";
            $output .= 'define(\'HTTP_SERVER\', \'' . HTTP_POPAYA . 'sadmin/\');' . "\n";
            $output .= 'define(\'HTTP_ADMIN\', \'' . HTTP_POPAYA . 'admin/\');' . "\n";
            $output .= 'define(\'HTTP_CATALOG\', \'' . HTTP_POPAYA . '\');' . "\n\n";

            $output .= '# HTTPS' . "\n";
            $output .= 'define(\'HTTPS_SERVER\', \'' . HTTP_POPAYA . 'sadmin/\');' . "\n";
            $output .= 'define(\'HTTPS_ADMIN\', \'' . HTTP_POPAYA . 'admin/\');' . "\n";
            $output .= 'define(\'HTTPS_CATALOG\', \'' . HTTP_POPAYA . '\');' . "\n\n";

            $output .= '# DIR' . "\n";
            $output .= 'define(\'DIR_APPLICATION\', \'' . addslashes(DIR_POPAYA) . 'sadmin/\');' . "\n";
            $output .= 'define(\'DIR_SYSTEM\', \'' . addslashes(DIR_POPAYA) . 'system/\');' . "\n";
            $output .= 'define(\'DIR_IMAGE\', \'' . addslashes(DIR_POPAYA) . 'image/\');' . "\n";
            $output .= 'define(\'DIR_STORAGE\', DIR_SYSTEM . \'storage/\');' . "\n";
            $output .= 'define(\'DIR_CATALOG\', \'' . addslashes(DIR_POPAYA) . 'template/\');' . "\n";
            $output .= 'define(\'DIR_ADMIN\', \'' . addslashes(DIR_POPAYA) . 'admin/\');' . "\n";
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
            $output .= 'define(\'DB_DRIVER\', \'' . addslashes($this->request->post['db_driver']) . '\');' . "\n";
            $output .= 'define(\'DB_HOSTNAME\', \'' . addslashes($this->request->post['db_hostname']) . '\');' . "\n";
            $output .= 'define(\'DB_USERNAME\', \'' . addslashes($this->request->post['db_username']) . '\');' . "\n";
            $output .= 'define(\'DB_PASSWORD\', \'' . addslashes(html_entity_decode($this->request->post['db_password'], ENT_QUOTES, 'UTF-8')) . '\');' . "\n";
            $output .= 'define(\'DB_DATABASE\', \'' . addslashes($this->request->post['db_database']) . '\');' . "\n";
            $output .= 'define(\'DB_PORT\', \'' . addslashes($this->request->post['db_port']) . '\');' . "\n";
            $output .= 'define(\'DB_PREFIX\', \'' . addslashes($this->request->post['db_prefix']) . '\');' . "\n\n";

            $output .= '# POPAYA API' . "\n";
            $output .= 'define(\'POPAYA_SERVER\', \'https://www.popaya.in/\');' . "\n";

            $file = fopen(DIR_POPAYA . 'sadmin/config.php', 'w');

            fwrite($file, $output);

            fclose($file);

            $this->response->redirect($this->url->link('install/step_5'));
        }

        $this->document->setTitle($this->language->get('heading_title'));

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_step_4'] = $this->language->get('text_step_4');
        $data['text_db_connection'] = $this->language->get('text_db_connection');
        $data['text_db_administration'] = $this->language->get('text_db_administration');
        $data['text_mysqli'] = $this->language->get('text_mysqli');
        $data['text_mpdo'] = $this->language->get('text_mpdo');
        $data['text_pgsql'] = $this->language->get('text_pgsql');

        $data['entry_db_driver'] = $this->language->get('entry_db_driver');
        $data['entry_db_hostname'] = $this->language->get('entry_db_hostname');
        $data['entry_db_username'] = $this->language->get('entry_db_username');
        $data['entry_db_password'] = $this->language->get('entry_db_password');
        $data['entry_db_database'] = $this->language->get('entry_db_database');
        $data['entry_db_port'] = $this->language->get('entry_db_port');
        $data['entry_db_prefix'] = $this->language->get('entry_db_prefix');
        $data['entry_firstname'] = $this->language->get('entry_firstname');
        $data['entry_lastname'] = $this->language->get('entry_lastname');
        $data['entry_password'] = $this->language->get('entry_password');
        $data['entry_email'] = $this->language->get('entry_email');

        $data['button_continue'] = $this->language->get('button_continue');
        $data['button_back'] = $this->language->get('button_back');

        if (isset($this->error['warning'])) {
            $data['warning_err'] = $this->error['warning'];
        } else {
            $data['warning_err'] = '';
        }

        if (isset($this->error['db_driver'])) {
            $data['db_driver_err'] = $this->error['db_driver'];
        } else {
            $data['db_driver_err'] = '';
        }

        if (isset($this->error['db_hostname'])) {
            $data['db_hostname_err'] = $this->error['db_hostname'];
        } else {
            $data['db_hostname_err'] = '';
        }

        if (isset($this->error['db_username'])) {
            $data['db_username_err'] = $this->error['db_username'];
        } else {
            $data['db_username_err'] = '';
        }

        if (isset($this->error['db_database'])) {
            $data['db_database_err'] = $this->error['db_database'];
        } else {
            $data['db_database_err'] = '';
        }

        if (isset($this->error['db_port'])) {
            $data['db_port_err'] = $this->error['db_port'];
        } else {
            $data['db_port_err'] = '';
        }

        if (isset($this->error['db_prefix'])) {
            $data['db_prefix_err'] = $this->error['db_prefix'];
        } else {
            $data['db_prefix_err'] = '';
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

        if (isset($this->error['password'])) {
            $data['password_err'] = $this->error['password'];
        } else {
            $data['password_err'] = '';
        }

        if (isset($this->error['email'])) {
            $data['email_err'] = $this->error['email'];
        } else {
            $data['email_err'] = '';
        }

        $data['action'] = $this->url->link('install/step_4');

        $db_drivers = array(
            'mysqli',
            'pdo',
            'pgsql'
        );

        $data['drivers'] = array();

        foreach ($db_drivers as $db_driver) {
            if (extension_loaded($db_driver)) {
                $data['drivers'][] = array(
                    'text'  => $this->language->get('text_' . $db_driver),
                    'value' => $db_driver
                );
            }
        }

        if (isset($this->request->post['db_driver'])) {
            $data['db_driver'] = $this->request->post['db_driver'];
        } else {
            $data['db_driver'] = '';
        }

        if (isset($this->request->post['db_hostname'])) {
            $data['db_hostname'] = $this->request->post['db_hostname'];
        } else {
            $data['db_hostname'] = 'localhost';
        }

        if (isset($this->request->post['db_username'])) {
            $data['db_username'] = $this->request->post['db_username'];
        } else {
            $data['db_username'] = 'root';
        }

        if (isset($this->request->post['db_password'])) {
            $data['db_password'] = $this->request->post['db_password'];
        } else {
            $data['db_password'] = '';
        }

        if (isset($this->request->post['db_database'])) {
            $data['db_database'] = $this->request->post['db_database'];
        } else {
            $data['db_database'] = '';
        }

        if (isset($this->request->post['db_port'])) {
            $data['db_port'] = $this->request->post['db_port'];
        } else {
            $data['db_port'] = 3306;
        }

        if (isset($this->request->post['db_prefix'])) {
            $data['db_prefix'] = $this->request->post['db_prefix'];
        } else {
            $data['db_prefix'] = 'pt_';
        }

        if (isset($this->request->post['email'])) {
            $data['email'] = $this->request->post['email'];
        } else {
            $data['email'] = 'info@popaya.in';
        }

        if (isset($this->request->post['password'])) {
            $data['password'] = $this->request->post['password'];
        } else {
            $data['password'] = '';
        }

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

        $data['back'] = $this->url->link('install/step_3');

        $data['header'] = $this->load->controller('common/header');
        $data['nav'] = $this->load->controller('common/nav');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('install/step_4', $data));
    }

    private function validate()
    {
        if (!$this->request->post['db_hostname']) {
            $this->error['db_hostname'] = $this->language->get('error_db_hostname');
        }

        if (!$this->request->post['db_username']) {
            $this->error['db_username'] = $this->language->get('error_db_username');
        }

        if (!$this->request->post['db_database']) {
            $this->error['db_database'] = $this->language->get('error_db_database');
        }

        if (!$this->request->post['db_port']) {
            $this->error['db_port'] = $this->language->get('error_db_port');
        }

        if ($this->request->post['db_prefix'] && preg_match('/[^a-z0-9_]/', $this->request->post['db_prefix'])) {
            $this->error['db_prefix'] = $this->language->get('error_db_prefix');
        }

        $db_drivers = array(
            'mysqli',
            'pdo',
            'pgsql'
        );

        if (!in_array($this->request->post['db_driver'], $db_drivers)) {
            $this->error['db_driver'] = $this->language->get('error_db_driver');
        } else {
            try {
                $db = new \DB($this->request->post['db_driver'], html_entity_decode($this->request->post['db_hostname'], ENT_QUOTES, 'UTF-8'), html_entity_decode($this->request->post['db_username'], ENT_QUOTES, 'UTF-8'), html_entity_decode($this->request->post['db_password'], ENT_QUOTES, 'UTF-8'), html_entity_decode($this->request->post['db_database'], ENT_QUOTES, 'UTF-8'), $this->request->post['db_port']);
            } catch (Exception $e) {
                $this->error['warning'] = $e->getMessage();
            }
        }

        if (!$this->request->post['firstname']) {
            $this->error['firstname'] = $this->language->get('error_firstname');
        }

        if (!$this->request->post['lastname']) {
            $this->error['lastname'] = $this->language->get('error_lastname');
        }

        if (!$this->request->post['password']) {
            $this->error['password'] = $this->language->get('error_password');
        }

        if ((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
            $this->error['email'] = $this->language->get('error_email');
        }

        /*if (!is_writable(DIR_POPAYA . 'config.php')) {
            $this->error['warning'] = $this->language->get('error_config') . DIR_POPAYA . 'config.php!';
        }

        if (!is_writable(DIR_POPAYA . 'admin/config.php')) {
            $this->error['warning'] = $this->language->get('error_config') . DIR_POPAYA . 'admin/config.php!';
        }*/

        if (!is_writable(DIR_POPAYA . 'launch/config.php')) {
            $this->error['warning'] = $this->language->get('error_config') . DIR_POPAYA . 'launch/config.php!';
        }

        if (!is_writable(DIR_POPAYA . 'sadmin/config.php')) {
            $this->error['warning'] = $this->language->get('error_config') . DIR_POPAYA . 'sadmin/config.php!';
        }

        return !$this->error;
    }
}
