<?php

class ControllerCommonNav extends PT_Controller
{
    public function index()
    {
        $this->load->language('common/nav');

        if(isset($this->request->get['path'])) {
            $parts = explode('_', (string)$this->request->get['path']);
        } else {
            $parts = array();
        }

        if(isset($parts[0])) {
            $data['information_group_id'] = $parts[0];
        } else {
            $data['information_group_id'] = 0;
        }

        if(isset($parts[1])) {
            $data['information_id'] = $parts[1];
        } else {
            $data['information_id'] = 0;
        }

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

        $data['information_groups'] = array();

        $information_group_info = $this->model_information_information_group->getInformationGroups();

        foreach($information_group_info as $information_group) {
            $information_data = array();

            $information_info = $this->model_information_information->getInformationByInformationGroups($information_group['information_group_id']);

            foreach($information_info as $information) {
                $information_data[] = array(
                    'information_id'    => $information['information_id'],
                    'title'             => $information['title'],
                    'description'       => trim(strip_tags(html_entity_decode($information['description'], ENT_QUOTES, 'UTF-8'))),
                    'href'              => $this->url->link('information/information', 'path=' . $information_group['information_group_id'] . '_' . $information['information_id'])
                );
            }

            $data['information_groups'][] = array(
                'information_group_id'  => $information_group['information_group_id'],
                'title'                 => $information_group['group_name'],
                'children'              => $information_data,
                'href'                  => $this->url->link('information/information', 'path=' . $information_group['information_group_id'] . '_' . $information_group['information_id'])
            );
        }

        // $data['information'] = array();

        // $information = $this->model_information_information_group->getInformationGroups();
        
        // foreach ($information as $info){
        
        //     $children_data = array();
            
        //     $children = $this->model_information_information->getInformationsByGroupId($info['information_group_id']);
        
        //     foreach ($children as $child) {
        //         $children_data[] = array(
        //             'title'         => $child['title'],
        //             'description'   => $child['description'],
        //             'href'          => $this->url->link('information/information', 'path=' . $info['information_group_id'] . '_' . $child['information_id'])
        //         );
        //     }
            
        //     $data['information'][] = array(
        //             'name'         => $info['group_name'],
        //             'children'     => $children_data,
        //             'href'         => $this->url->link('information/information', 'path=' . $info['information_group_id'])
        //         );
        // }
        // // echo '<pre>';
        // // print_r($data['information']); exit;

        return $this->load->view('common/nav', $data);
    }
}
