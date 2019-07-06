<?php

class ControllerCommonHeader extends PT_Controller
{
    public function index()
    {
        $data['title'] = $this->document->getTitle();

        $data['base'] = HTTP_SERVER;
        $data['description'] = $this->document->getDescription();
        $data['keywords'] = $this->document->getKeywords();
        $data['links'] = $this->document->getLinks();
        $data['styles'] = $this->document->getStyles();
        $data['scripts'] = $this->document->getScripts('header');
        $data['lang'] = $this->language->get('code');
        $data['direction'] = $this->language->get('direction');

        $this->load->language('common/header');

        $data['text_logged'] = sprintf($this->language->get('text_logged'), $this->user->getUserName());

        if (!isset($this->request->get['user_token']) || !isset($this->session->data['user_token']) || ($this->request->get['user_token'] != $this->session->data['user_token'])) {
            $data['logged'] = '';

            $data['home'] = $this->url->link('user/login');
        } else {
            $data['logged'] = true;

            $data['home'] = $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token']);
            $data['logout'] = $this->url->link('user/logout', 'user_token=' . $this->session->data['user_token']);
            $data['profile'] = $this->url->link('user/profile', 'user_token=' . $this->session->data['user_token']);

            $this->load->model('tool/image');

            $data['username'] = '';
            $data['email'] = '';
            $data['user_group'] = '';
            $data['image'] = $this->model_tool_image->resize('profile.png', 25, 25);
            $data['thumb'] = $this->model_tool_image->resize('profile.png', 128, 128);

            $this->load->model('user/user');

            $user_info = $this->model_user_user->getUser($this->user->getId());

            if ($user_info) {
                $data['username'] = $user_info['name'];
                $data['email'] = $user_info['email'];
                $data['user_group'] = $user_info['user_group'];

                if (is_file(DIR_IMAGE . html_entity_decode($user_info['image'], ENT_QUOTES, 'UTF-8'))) {
                    $data['image'] = $this->model_tool_image->resize(html_entity_decode($user_info['image'], ENT_QUOTES, 'UTF-8'), 25, 25);
                    $data['thumb'] = $this->model_tool_image->resize(html_entity_decode($user_info['image'], ENT_QUOTES, 'UTF-8'), 128, 128);
                }
            }
        }

        return $this->load->view('common/header', $data);
    }
}
