<?php

class ControllerStartupDatabase extends PT_Controller
{
    public function index()
    {
        if (is_file(DIR_POPAYA . 'config.php') && filesize(DIR_POPAYA . 'config.php') > 0) {
            $lines = file(DIR_POPAYA . 'config.php');

            foreach ($lines as $line) {
                if (strpos(strtoupper($line), 'DB_') !== false) {
                    eval($line);
                }
            }

            if (defined('DB_PORT')) {
                $port = DB_PORT;
            } else {
                $port = ini_get('mysqli.default_port');
            }

            $this->registry->set('db', new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, $port));
        }
    }
}
