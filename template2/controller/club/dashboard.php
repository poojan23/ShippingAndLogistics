<?php

class ControllerClubDashboard extends PT_Controller
{
    public function index()
    {
        $this->load->language('club/dashboard');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('club/dashboard');

        // login session
        if (!$this->customer->isLogged()) {
            $this->response->redirect($this->url->link('club/login'));
        }      
        
        

        $data['club_id'] = $this->customer->getId();
        $data['club_name'] = $this->customer->getFirstName();
        $data['date'] = $this->customer->getDate();
        $data['mobile'] = $this->customer->getMobile();
        $data['email'] = $this->customer->getEmail();
        $data['president'] = $this->customer->getPresident();
        $data['image'] = $this->customer->getImage();
        $data['assistant_governor'] = $this->customer->getAssistant();
        $data['district_secretary'] = $this->customer->getDistrict();

        $this->load->model('tool/image');

        $data['placeholder'] = $this->model_tool_image->resize('no-image.png', 100, 100);

        if (is_file(DIR_IMAGE . html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8'))) {
            $data['thumb'] = $this->model_tool_image->resize(html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8'), 100, 100);
        } else {
            $data['thumb'] = $data['placeholder'];
        }

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

        $data['continue'] = $this->url->link('common/home');

        // page menu
        $data['dashboard'] = $this->url->link('club/dashboard');
        $data['add_data'] = $this->url->link('club/add_data');
        $data['project'] = $this->url->link('club/project');
        $data['trf'] = $this->url->link('club/trf');
        $data['member'] = $this->url->link('club/member');
        $data['profile'] = $this->url->link('club/profile');
        $data['logout'] = $this->url->link('club/logout');

        // dashboard count box data
        $data['trfs'] = $this->model_club_dashboard->getTotalTrf($data['club_id']); 
        $data['projects'] = $this->model_club_dashboard->getTotalProject($data['club_id']); 
        $data['members'] = $this->model_club_dashboard->getTotalMember($data['club_id']); 
        $data['clubs'] = $this->model_club_dashboard->getTotalClub($data['club_id']); 

        //dashboard table data


        $data['TableMembers'] = array();

        $results = $this->model_club_dashboard->getMemberTable($data['club_id']);
        
        foreach ($results as $result) {
            $data['TableMembers'][] = array(
                'member_id' => $result['member_id'],
                'date' => $result['date'],
                'induction' => $result['induction'],
                'unlist' => $result['unlist'],
                'net' => $result['net'],
                'points' => $result['points'],
                'view' => $this->url->link('club/member/view', 'member_id=' . $result['member_id'])
            );
        }

        $data['TableProjects'] = array();

        $results = $this->model_club_dashboard->getProjectTable($data['club_id']);
        
        foreach ($results as $result) {
            $data['TableProjects'][] = array(
                'project_id'    => $result['project_id'],
                'date'         => $result['date'],
                'title'    => $result['title'],
                'description'    => $result['description'],
                'amount'    => $result['amount'],
                'no_of_beneficiary'    => $result['no_of_beneficiary'],
                'points'    => $result['points'],
                'view'          => $this->url->link('club/project/view', 'project_id=' . $result['project_id'])
            );
        }

        $data['TableTrf'] = array();

        $results = $this->model_club_dashboard->getTrfTable($data['club_id']);
        
        foreach ($results as $result) {
            $data['TableTrf'][] = array(
                'trf_id' => $result['trf_id'],
                'date' => $result['date'],
                'amount_usd' => $result['amount_usd'],
                'points' => $result['points'],
                'view' => $this->url->link('club/trf/view', 'trf_id=' . $result['trf_id'])
            );
        }
        // echo '<pre>';
        // print_r($data['TableMembers']);exit;

        // include file
        $data['header'] = $this->load->controller('common/header');
        $data['nav'] = $this->load->controller('common/nav');
        $data['navpage'] = $this->load->controller('common/navpage');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('club/dashboard', $data));
    }
}
