<?php

class ControllerClubAddData extends PT_Controller
{
    public function index()
    {
        $this->load->language('club/add_data');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('club/project');

        // login session
        if (!$this->customer->isLogged()) {
            $this->response->redirect($this->url->link('club/login'));
        }  
        

        $data['club_id'] = $this->customer->getId();
        
        // points from three tables
        $this->load->model('club/add_data');
        $data['total_point_members'] = $this->model_club_add_data->getMemberPoints($data['club_id']);

        $data['total_point_trfs'] = $this->model_club_add_data->getTrfPoints($data['club_id']);
        $data['total_point_projects'] = $this->model_club_add_data->getProjectPoints($data['club_id']);

        // exchange rate
        $data['exchange_rate'] = $this->model_club_add_data->getExchangeRate();

     
        $this->load->model('tool/image');

        $data['placeholder'] = $this->model_tool_image->resize('no-image.png', 90, 90);
        $data['thumb'] = $data['placeholder'];

        // if (is_file(DIR_IMAGE . html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8'))) {
        //     $data['thumb'] = $this->model_tool_image->resize(html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8'), 100, 100);
        // } else {
        //     $data['thumb'] = $data['placeholder'];
        // }
		
        // breadcrums
        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
        );

         $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_dashboard'),
            'href' => $this->url->link('club/dashboard')
        );

        // member project id

        $data['MemberProjectId'] = $this->model_club_add_data->GetMemberProjectIds();

        $data['TrfProjectId'] = $this->model_club_add_data->GetTrfProjectIds();

        #category 
        $data['categoires'] = array();

        $results = $this->model_club_project->getCategories();

        foreach ($results as $result) {
            $data['categoires'][] = array(
                'category_id'    => $result['category_id'],
                'name'         => $result['name']
            );
        }

        $data['action1'] = $this->url->link('club/add_data/member');
        $data['action2'] = $this->url->link('club/add_data/trf');
        $data['action3'] = $this->url->link('club/add_data/project');
        $data['action4'] = $this->url->link('club/add_data/club');

        $data['cancel'] = $this->url->link('club/dashboard');

        $data['continue'] = $this->url->link('common/home');

        // include file
        $data['header'] = $this->load->controller('common/header');
        $data['nav'] = $this->load->controller('common/nav');
        $data['navpage'] = $this->load->controller('common/navpage');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('club/add_data', $data));
    }


     public function member()
    {
        $this->load->language('club/add_data');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('club/add_data');

        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
           $json = $this->model_club_add_data->addMember($this->request->post);
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }



     public function project()
    {
        $this->load->language('club/add_data');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('club/add_data');
       
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            
          $json =  $this->model_club_add_data->addProject($this->request->post);
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function getMemberProjectId()
    {
        $this->load->model('club/add_data');

        $json = $this->model_club_add_data->createMemberId($this->request->post['project_id']);
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function trf()
    {
        $this->load->language('club/add_data');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('club/add_data');

        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            
            $json = $this->model_club_add_data->addTrf($this->request->post);

        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
      
    }

    public function getTrfProjectId()
    {
        $this->load->model('club/add_data');
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

        $json = $this->model_club_add_data->createTrfId($this->request->post['project_id']);
        
        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

}
