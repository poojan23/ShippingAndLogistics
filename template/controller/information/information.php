<?php

class ControllerInformationInformation extends PT_Controller {

    public function index() {
        $this->load->language('information/information');

        $this->load->model('information/information');

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('home/home')
        );

        if (isset($this->request->get['path'])) {
            $path = '';

            $parts = explode('_', $this->request->get['path']);

            $information_id = (int) array_pop($parts);

            foreach ($parts as $path_id) {
                if (!$path) {
                    $path = (int) $path_id;
                } else {
                    $path .= '_' . (int) $path_id;
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
        
        

        $data['information_info'] = array();

        $information_info = $this->model_information_information->getInformation($information_id);

        foreach ($information_info as $info) {
            $data['breadcrumbs'][] = array(
                'text'  => $info['title'],
                'href'  => $this->url->link('information/information', 'path=' . $info['information_id'])
            );
            
            $data['information_info'][] = array(
                'id' => $info['information_id'],
                'name' => $info['title'],
                'description' => html_entity_decode($info['description'], ENT_QUOTES, 'UTF-8'),
                'href' => $this->url->link('information/information', 'path=' . $info['information_id'])
            );
        }

        $data['continue'] = $this->url->link('common/home');

        $data['header'] = $this->load->controller('common/header');
        $data['nav'] = $this->load->controller('common/nav');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('information/information', $data));
    }

}
