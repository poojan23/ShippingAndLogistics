<?php

class ControllerCommonNav extends PT_Controller
{
    public function index()
    {
        $this->load->language('common/nav');

        # Step
        $data['text_welcome'] = $this->language->get('text_welcome');
        $data['text_authentication'] = $this->language->get('text_authentication');
        $data['text_profile'] = $this->language->get('text_profile');
        $data['text_success'] = $this->language->get('text_success');
        $data['text_finished'] = $this->language->get('text_finished');
        $data['text_language'] = $this->language->get('text_language');

        if (isset($this->request->get['url'])) {
            $data['url'] = (string)$this->request->get['url'];
        } else {
            $data['url'] = 'install/step_1';
        }

        # Language
        $data['action'] = $this->url->link('common/nav/language', '', $this->request->server['HTTPS']);

        if (isset($this->session->data['language'])) {
            $data['code'] = $this->session->data['language'];
        } else {
            $data['code'] = $this->config->get('language.default');
        }

        $data['languages'] = array();

        $languages = glob(DIR_LANGUAGE . '*', GLOB_ONLYDIR);

        foreach ($languages as $language) {
            $data['languages'][] = array(
                'text'  => $this->language->get('text_' . basename($language)),
                'value' => basename($language)
            );
        }

        if (!isset($this->request->get['url'])) {
            $data['redirect'] = $this->url->link('install/step_1');
        } else {
            $url_data = $this->request->get;

            $route = $url_data['url'];

            unset($url_data['url']);

            $url = '';

            if ($url_data) {
                $url = '&' . urldecode(http_build_query($url_data, '', '&'));
            }

            $data['redirect'] = $this->url->link($route, $url, $this->request->server['HTTPS']);
        }

        return $this->load->view('common/nav', $data);
    }

    public function language()
    {
        if (isset($this->request->post['code']) && is_dir(DIR_LANGUAGE . basename($this->request->post['code']))) {
            $this->session->data['language'] = $this->request->post['code'];
        }

        if (isset($this->request->post['redirect'])) {
            $this->response->redirect($this->request->post['redirect']);
        } else {
            $this->response->redirect($this->url->link('install/step_1'));
        }
    }
}
