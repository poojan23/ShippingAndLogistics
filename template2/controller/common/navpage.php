<?php

class ControllerCommonNavpage extends PT_Controller
{
    public function index()
    {
        $this->load->language('common/navpage');

        $data['name'] = $this->config->get('config_name');
        

        if (!$this->customer->isLogged()) {
            $this->response->redirect($this->url->link('club/login'));
        }      
        
        $data['club_id'] = $this->customer->getId();
        $data['club_name'] = $this->customer->getFirstName();
        $data['date'] = $this->customer->getDate();
        $data['mobile'] = $this->customer->getMobile();
        $data['email'] = $this->customer->getEmail();
        $data['president'] = $this->customer->getPresident();
        $data['image_club'] = $this->customer->getImage();
        $data['assistant_governor'] = $this->customer->getAssistant();
        $data['district_secretary'] = $this->customer->getDistrict();

        $this->load->model('tool/image');

        $data['placeholder'] = $this->model_tool_image->resize('no-image.png', 200, 50);

        if (is_file(DIR_IMAGE . html_entity_decode($data['image_club'], ENT_QUOTES, 'UTF-8'))) {
            $data['thumb_club'] = $this->model_tool_image->resize(html_entity_decode($data['image_club'], ENT_QUOTES, 'UTF-8'), 200, 50);
        } else {
            $data['thumb_club'] = $data['placeholder'];
        }

        $this->load->model('club/add_data');
        $data['total_point_members'] = $this->model_club_add_data->getMemberPoints($data['club_id']);
        $data['total_point_trfs'] = $this->model_club_add_data->getTrfPoints($data['club_id']);
        $data['total_point_projects'] = $this->model_club_add_data->getProjectPoints($data['club_id']);
    
        $data['dashboard'] = $this->url->link('club/dashboard');
        $data['add_data'] = $this->url->link('club/add_data');
        $data['add_citation'] = $this->url->link('club/add_citation');
        $data['project'] = $this->url->link('club/project');
        $data['trf'] = $this->url->link('club/trf');
        $data['member'] = $this->url->link('club/member');
        $data['profile'] = $this->url->link('club/profile');
        $data['logout'] = $this->url->link('club/logout');

        return $this->load->view('common/navpage', $data);
    }
}
