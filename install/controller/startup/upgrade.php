<?php

class ControllerStartupUpgrade extends PT_Controller
{
    public function index()
    {
        $upgrade = false;

        if (is_file(DIR_POPAYA . 'config.php') && filesize(DIR_POPAYA . 'config.php') > 0) {
            $upgrade = true;
        }

        if (isset($this->request->get['url'])) {
            if (($this->request->get['url'] == 'install/step_5') || (substr($this->request->get['url'], 0, 8) == 'upgrade/') || (substr($this->request->get['url'], 0, 10) == '3rd_party/')) {
                $upgrade = false;
            }
        }

        if ($upgrade) {
            $this->response->redirect($this->url->link('upgrade/upgrade'));
        }
    }
}
