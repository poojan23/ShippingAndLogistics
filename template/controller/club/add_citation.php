<?php

class ControllerClubAddCitation extends PT_Controller
{
    public function index()
    {
        $this->load->language('club/add_citation');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('club/add_citation');

        // login session check
        if (!$this->customer->isLogged()) {
            $this->response->redirect($this->url->link('club/login'));
        }  
        
        //  Breadcrumbs
        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
        );

         $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_dashboard'),
            'href' => $this->url->link('club/dashboard')
        );

        $data['club_id'] = $this->customer->getId();
        $club_id = $this->customer->getId();

        $data['citations'] = $this->model_club_add_citation->getCitationTableForm();

        // button link
        $data['cancel'] = $this->url->link('club/dashboard');
        $data['continue'] = $this->url->link('common/home');

        // include file
        $data['header'] = $this->load->controller('common/header');
        $data['nav'] = $this->load->controller('common/nav');
        $data['navpage'] = $this->load->controller('common/navpage');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('club/add_citation', $data));
    }

}
