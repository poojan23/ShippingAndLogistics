<?php

class ControllerCommonHeader extends PT_Controller
{
    public function index()
    {
        if(is_file(DIR_IMAGE . $this->config->get('config_icon'))) {
            $data['icon'] = $this->config->get('config_url') . 'image/' . $this->config->get('config_icon');
        }
        
        $data['title'] = $this->document->getTitle();

        $data['base'] = $this->config->get('config_url');
        $data['description'] = $this->document->getDescription();
        $data['keywords'] = $this->document->getKeywords();
        $data['lang'] = $this->language->get('code');
        $data['direction'] = $this->language->get('direction');

        return $this->load->view('common/header', $data);
    }
}
