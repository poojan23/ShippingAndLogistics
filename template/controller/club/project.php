<?php

class ControllerClubProject extends PT_Controller
{
    public function index()
    {
        $this->load->language('club/project');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('club/project');

        if (!$this->customer->isLogged()) {
            $this->response->redirect($this->url->link('club/login'));
        }
       
        $this->getList();
    }
    public function add()
    {
        $this->load->language('club/project');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('club/project');

        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

            // echo '<pre>';
            // print_r($this->request->post); exit;
            $this->model_club_project->addProject($this->request->post);

            $this->response->redirect($this->url->link('club/project'));
        }

        $this->getForm();
    }

protected function getList()
    {
        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text'  => $this->language->get('text_home'),
            'href'  => $this->url->link('common/dashboard')
        );

        $data['breadcrumbs'][] = array(
            'text'  => $this->language->get('heading_title'),
            'href'  => $this->url->link('club/project')
        );

        $data['add'] = $this->url->link('club/project/add');

        $data['club_id'] = $this->customer->getId();
        $data['club_name'] = $this->customer->getFirstName();
        $data['date'] = $this->customer->getDate();
        $data['mobile'] = $this->customer->getMobile();
        $data['email'] = $this->customer->getEmail();
        $data['image'] = $this->customer->getImage();
        $data['president'] = $this->customer->getPresident();
        $data['assistant_governor'] = $this->customer->getAssistant();
        $data['district_secretary'] = $this->customer->getDistrict();
        
        $this->load->model('tool/image');

        $data['placeholder'] = $this->model_tool_image->resize('no-image.png', 100, 100);

        if (is_file(DIR_IMAGE . html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8'))) {
            $data['thumb'] = $this->model_tool_image->resize(html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8'), 100, 100);
        } else {
            $data['thumb'] = $data['placeholder'];
        }

        $data['projects'] = array();


        $results = $this->model_club_project->getProjectById($this->customer->getId());

        foreach ($results as $result) {
            $data['projects'][] = array(
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

        if (isset($this->error['warning'])) {
            $data['warning_err'] = $this->error['warning'];
        } else {
            $data['warning_err'] = '';
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        if (isset($this->request->post['selected'])) {
            $data['selected'] = (array)$this->request->post['selected'];
        } else {
            $data['selected'] = array();
        }

        $data['cancel'] = $this->url->link('club/dashboard');

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
        );

         $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('club/project')
        );


        $data['continue'] = $this->url->link('common/home');
        $data['add_project'] = $this->url->link('club/project/add');
        $data['dashboard'] = $this->url->link('club/dashboard');
        $data['project'] = $this->url->link('club/project');
        $data['trf'] = $this->url->link('club/project');
        $data['member'] = $this->url->link('club/member');
        $data['profile'] = $this->url->link('club/profile');
        $data['logout'] = $this->url->link('club/logout');

        $data['header'] = $this->load->controller('common/header');
        $data['nav'] = $this->load->controller('common/nav');
        $data['navpage'] = $this->load->controller('common/navpage');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('club/project', $data));
    }

    protected function getForm()
    {
        $this->load->model('club/project');

        $data['text_form'] = !isset($this->request->get['project_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

        $club_id = $this->customer->getId();

        #category 
        $data['categoires'] = array();

        $results = $this->model_club_project->getCategories();

        foreach ($results as $result) {
            $data['categoires'][] = array(
                'category_id'    => $result['category_id'],
                'name'         => $result['name']
            );
        }

        #form 
        if (isset($this->error['warning'])) {
            $data['warning_err'] = $this->error['warning'];
        } else {
            $data['warning_err'] = '';
        }

        if (isset($this->error['month'])) {
            $data['month_err'] = $this->error['month'];
        } else {
            $data['month_err'] = '';
        }

        if (isset($this->error['year'])) {
            $data['year_err'] = $this->error['year'];
        } else {
            $data['year_err'] = '';
        }

        if (isset($this->error['title'])) {
            $data['title_err'] = $this->error['title'];
        } else {
            $data['title_err'] = '';
        }

        if (isset($this->error['description'])) {
            $data['description_err'] = $this->error['description'];
        } else {
            $data['description_err'] = '';
        }

        if (isset($this->error['amount'])) {
            $data['amount_err'] = $this->error['amount'];
        } else {
            $data['amount_err'] = '';
        }

        if (isset($this->error['no_of_beneficiary'])) {
            $data['no_of_beneficiary_err'] = $this->error['no_of_beneficiary'];
        } else {
            $data['no_of_beneficiary_err'] = '';
        }

        if (isset($this->error['points'])) {
            $data['points_err'] = $this->error['points'];
        } else {
            $data['points_err'] = '';
        }

        if (isset($this->error['image'])) {
            $data['image_err'] = $this->error['image'];
        } else {
            $data['image_err'] = '';
        }

        if (isset($this->error['category'])) {
            $data['category_err'] = $this->error['category'];
        } else {
            $data['category_err'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text'  => $this->language->get('text_home'),
            'href'  => $this->url->link('common/dashboard')
        );

        $data['breadcrumbs'][] = array(
            'text'  => $this->language->get('heading_title'),
            'href'  => $this->url->link('club/project')
        );

        if (!isset($this->request->get['project_id'])) {
            $data['action'] = $this->url->link('club/project/add');
            $data['breadcrumbs'][] = array(
                'text'  => $this->language->get('text_add'),
                'href'  => $this->url->link('club/project/add')
            );
        } else {
            $data['action'] = $this->url->link('club/project/edit' . '&project_id=' . $this->request->get['project_id']);
            $data['breadcrumbs'][] = array(
                'text'  => $this->language->get('text_edit'),
                'href'  => $this->url->link('club/project/edit')
            );
        }

        $data['cancel'] = $this->url->link('club/project');

        if (isset($this->request->get['project_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $project_info = $this->model_club_project->getProject($this->request->get['project_id']);
        }
        
        if (isset($this->request->post['month'])) {
            $data['month'] = $this->request->post['month'];
        } elseif (!empty($project_info)) {
            $data['month'] = $project_info['month'];
        } else {
            $data['month'] = '';
        }


        if (isset($this->request->post['year'])) {
            $data['year'] = $this->request->post['year'];
        } elseif (!empty($project_info)) {
            $data['year'] = $project_info['year'];
        } else {
            $data['year'] = '';
        }

        if (isset($this->request->post['title'])) {
            $data['title'] = $this->request->post['title'];
        } elseif (!empty($project_info)) {
            $data['title'] = $project_info['title'];
        } else {
            $data['title'] = '';
        }

        if (isset($this->request->post['description'])) {
            $data['description'] = $this->request->post['description'];
        } elseif (!empty($project_info)) {
            $data['description'] = $project_info['description'];
        } else {
            $data['description'] = '';
        }

        if (isset($this->request->post['amount'])) {
            $data['amount'] = $this->request->post['amount'];
        } elseif (!empty($project_info)) {
            $data['amount'] = $project_info['amount'];
        } else {
            $data['amount'] = '';
        }

        if (isset($this->request->post['no_of_beneficiary'])) {
            $data['no_of_beneficiary'] = $this->request->post['no_of_beneficiary'];
        } elseif (!empty($project_info)) {
            $data['no_of_beneficiary'] = $project_info['no_of_beneficiary'];
        } else {
            $data['no_of_beneficiary'] = '';
        }

        if (isset($this->request->post['points'])) {
            $data['points'] = $this->request->post['points'];
        } elseif (!empty($project_info)) {
            $data['points'] = $project_info['points'];
        } else {
            $data['points'] = '';
        }

        if (isset($this->request->post['image'])) {
            $data['image'] = $this->request->post['image'];
        } elseif (!empty($information_info)) {
            $data['image'] = $information_info['image'];
        } else {
            $data['image'] = '';
        }

        if (isset($this->request->post['category'])) {
            $data['category'] = $this->request->post['category'];
        } elseif (!empty($information_info)) {
            $data['category'] = $information_info['category'];
        } else {
            $data['category'] = '';
        }

       
        $data['club_id'] = $this->customer->getId();
        $data['club_name'] = $this->customer->getFirstName();
        $data['date'] = $this->customer->getDate();
        $data['mobile'] = $this->customer->getMobile();
        $data['email'] = $this->customer->getEmail();
        $data['image'] = $this->customer->getImage();
        $data['president'] = $this->customer->getPresident();
        $data['assistant_governor'] = $this->customer->getAssistant();
        $data['district_secretary'] = $this->customer->getDistrict();
        
        $this->load->model('tool/image');

        $data['placeholder'] = $this->model_tool_image->resize('no-image.png', 100, 100);

        if (is_file(DIR_IMAGE . html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8'))) {
            $data['thumb'] = $this->model_tool_image->resize(html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8'), 100, 100);
        } else {
            $data['thumb'] = $data['placeholder'];
        }

        if (!$this->customer->isLogged()) {
            $this->response->redirect($this->url->link('club/login'));
        }

        $data['continue'] = $this->url->link('common/home');
        $data['add_project'] = $this->url->link('club/project/add');
        $data['dashboard'] = $this->url->link('club/dashboard');
        $data['project'] = $this->url->link('club/project');
        $data['trf'] = $this->url->link('club/project');
        $data['member'] = $this->url->link('club/member');
        $data['profile'] = $this->url->link('club/profile');
        $data['logout'] = $this->url->link('club/logout');


        $data['header'] = $this->load->controller('common/header');
        $data['nav'] = $this->load->controller('common/nav');
        $data['navpage'] = $this->load->controller('common/navpage');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('club/project_form', $data));
    }
    public function view() {

        $this->load->model('club/project');

        $data['club_id'] = $this->customer->getId();
        $data['club_name'] = $this->customer->getFirstName();
        $data['date'] = $this->customer->getDate();
        $data['mobile'] = $this->customer->getMobile();
        $data['email'] = $this->customer->getEmail();
        $data['image'] = $this->customer->getImage();
        $data['president'] = $this->customer->getPresident();
        $data['assistant_governor'] = $this->customer->getAssistant();
        $data['district_secretary'] = $this->customer->getDistrict();
        
        $this->load->model('tool/image');

        $data['placeholder'] = $this->model_tool_image->resize('no-image.png', 100, 100);

        if (is_file(DIR_IMAGE . html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8'))) {
            $data['thumb'] = $this->model_tool_image->resize(html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8'), 100, 100);
        } else {
            $data['thumb'] = $data['placeholder'];
        }
		

        $this->load->language('club/project');

        $this->document->setTitle($this->language->get('heading_title'));

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('club/project')
        );

        $data['cancel'] = $this->url->link('club/project');
        $data['continue'] = $this->url->link('common/home');
        $data['add_member'] = $this->url->link('club/member/add');
        $data['dashboard'] = $this->url->link('club/dashboard');
        $data['project'] = $this->url->link('club/project');
        $data['trf'] = $this->url->link('club/trf');
        $data['member'] = $this->url->link('club/member');
        $data['profile'] = $this->url->link('club/profile');
        $data['logout'] = $this->url->link('club/logout');

        if (!$this->customer->isLogged()) {
            $this->response->redirect($this->url->link('club/login'));
        }

        $this->load->model('club/project');

        if (isset($this->request->get['project_id'])) {

            $data['projects'] =array();

            $results = $this->model_club_project->getProject($this->request->get['project_id']);

            
            foreach ($results as $result) {
                    $data['projects'][] = array(
                    'date' => $result['date'],
                    'title'    => $result['title'],
                    'description'    => $result['description'],
                    'amount'    => $result['amount'],
                    'no_of_beneficiary'    => $result['no_of_beneficiary'],
                    'points'    => $result['points'],
                );
            }

            #category 
            $data['categoires'] = array();

            $results = $this->model_club_project->getCategoryByProjectId($this->request->get['project_id']);

            foreach ($results as $result) {
                $data['categoires'][] = array(
                    'category_id'    => $result['category_id'],
                    'name'         => $result['name']
                );
            }

            #category 
            $data['images'] = array();

            $results = $this->model_club_project->getImageByProjectId($this->request->get['project_id']);

            foreach ($results as $result) {

                if (is_file(DIR_IMAGE . html_entity_decode($result['image'], ENT_QUOTES, 'UTF-8'))) {
                    $data['thumb2'] = $this->model_tool_image->resize(html_entity_decode($result['image'], ENT_QUOTES, 'UTF-8'), 100, 100);
                } else {
                    $data['thumb2'] = $data['placeholder'];
                }
            
                $data['images'][] = array(
                    'image_id'    => $result['project_image_id'],
                    'thumb2'         => $data['thumb2']
                );
            }
        }
         
        $data['header'] = $this->load->controller('common/header');
        $data['nav'] = $this->load->controller('common/nav');
        $data['navpage'] = $this->load->controller('common/navpage');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('club/view_project', $data));
    }
}
