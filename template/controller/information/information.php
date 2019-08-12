<?php

class ControllerInformationInformation extends PT_Controller {

    public function index() {
        $this->load->language('information/information');

        $this->load->model('information/information_group');
        $this->load->model('information/information');

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('home/home')
        );

        if (isset($this->request->get['path'])) {
            $path = '';

            $parts = explode('_', $this->request->get['path']);

            if($parts[0]) {
                $data['information_group_id'] = $parts[0];
            } else {
                $data['information_group_id'] = 0;
            }

            $information_id = (int)array_pop($parts);

            foreach ($parts as $path_id) {
                if (!$path) {
                    $path = (int)$path_id;
                } else {
                    $path .= '_' . (int)$path_id;
                }

                $information_info = $this->model_information_information->getInformationByGroupId($path_id);

                if ($information_info) {
                    $data['breadcrumbs'][] = array(
                        'text' => $information_info['group_name'],
                        'href' => $this->url->link('information/information', 'path=' . $path)
                    );
                }
            }
        } else {
            $information_id = 0;
        }

        $data['information_id'] = $information_id;

        if(isset($parts[1])) {
            $data['parts'] = $parts[1];
        } else {
            $data['parts'] = array();
        }

        $data['information_groups'] = array();

        $results = $this->model_information_information->getInformationByInformationGroups($data['information_group_id']);

        foreach($results as $result) {
            if($result['information_group_id'] == $data['information_group_id']) {
                $children_data = array();
                
                $children = $this->model_information_information->getInformations($result['information_id']);

                foreach($children as $child) {
                    $children_data[] = array(
                        'information_id'    => $child['information_id'],
                        'title'             => $child['title'],
                        'parent_id'         => $child['parent_id'],
                        'href'              => $this->url->link('information/information', 'path=' . $result['information_group_id'] . '_' . $result['information_id'] . '_' . $child['information_id'])
                    );
                }
            }

            if($children_data) {
                $data['information_groups'][] = array(
                    'information_id'    => $result['information_id'],
                    'title'             => $result['title'],
                    'children'          => $children_data,
                    'href'              => ''
                );
            } else {
                $data['information_groups'][] = array(
                    'information_id'    => $result['information_id'],
                    'title'             => $result['title'],
                    'children'          => array(),
                    'href'              => $this->url->link('information/information', 'path=' . $result['information_group_id'] . '_' . $result['information_id'])
                );
            }
            
        }

        $data['informations'] = array();

        $information_info = $this->model_information_information->getInformation($information_id);

        foreach ($information_info as $information) {
            $data['breadcrumbs'][] = array(
                'text'  => $information['title'],
                'href'  => $this->url->link('information/information', 'path=' . $information['information_id'])
            );
            
            $data['informations'][] = array(
                'id' => $information['information_id'],
                'name' => $information['title'],
                'description' => html_entity_decode($information['description'], ENT_QUOTES, 'UTF-8'),
                'href' => $this->url->link('information/information', 'path=' . $information['information_id'])
            );
        }

        $data['continue'] = $this->url->link('common/home');

        $data['header'] = $this->load->controller('common/header');
        $data['nav'] = $this->load->controller('common/nav');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('information/information', $data));
    }

}
