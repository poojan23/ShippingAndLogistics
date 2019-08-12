<?php

class ControllerCommonNav extends PT_Controller
{
    public function index()
    {
        $this->load->language('common/nav');

        $data['name'] = $this->config->get('config_name');

        if (is_file(DIR_IMAGE . $this->config->get('config_logo')) && is_file(DIR_IMAGE . $this->config->get('config_district_logo')) && is_file(DIR_IMAGE . $this->config->get('config_governor_logo'))) {
            $data['logo'] = $this->config->get('config_url') . 'image/' . $this->config->get('config_logo');
            $data['district_logo'] = $this->config->get('config_url') . 'image/' . $this->config->get('config_district_logo');
            $data['governor_logo'] = $this->config->get('config_url') . 'image/' . $this->config->get('config_governor_logo');
        } else {
            $data['logo'] = '';
            $data['district_logo'] = '';
            $data['governor_logo'] = '';
        }

        $data['home'] = $this->url->link('common/home');
        $data['contact'] = $this->url->link('information/contact');
        $data['club'] = $this->url->link('club/login');
        $data['admin'] = HTTP_SERVER.'admin';
        # information
        $this->load->model('information/information_group');
        $this->load->model('information/information');

        $data['information'] = array();

        $information = $this->model_information_information_group->getInformationGroups();
        
        foreach ($information as $info){
        
            $children_data = array();
            
            $children = $this->model_information_information->getInformationsByGroupId($info['information_group_id']);
        
            foreach ($children as $child) {
                $children_data[] = array(
                    'title'         => $child['title'],
                    'description'   => $child['description'],
                    'href'          => $this->url->link('information/information', 'path=' . $info['information_group_id'] . '_' . $child['information_id'])
                );
            }
            
            $data['information'][] = array(
                    'name'         => $info['group_name'],
                    'children'     => $children_data,
                    'href'         => $this->url->link('information/information', 'path=' . $info['information_group_id'])
                );
        }
        // echo '<pre>';
        // print_r($data['information']); exit;

        return $this->load->view('common/nav', $data);
    }
}
