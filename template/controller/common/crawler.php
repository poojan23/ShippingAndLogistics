<?php

class ControllerCommonCrawler extends PT_Controller
{
    public function index()
    {
        $this->load->language('common/crawler');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('tool/crawler');

        if (isset($this->request->server['HTTP_X_REAL_IP'])) {
            $ip = $this->request->server['HTTP_X_REAL_IP'];
        } else if (isset($this->request->server['REMOTE_ADDR'])) {
            $ip = $this->request->server['REMOTE_ADDR'];
        } else {
            $ip = '';
        }

        if (isset($this->request->server['HTTP_HOST']) && isset($this->request->server['REQUEST_URI'])) {
            $url = ($this->request->server['HTTPS'] ? 'https://' : 'http://') . $this->request->server['HTTP_HOST'] . $this->request->server['REQUEST_URI'];
        } else {
            $url = '';
        }

        if (isset($this->request->server['HTTP_REFERER'])) {
            $referer = $this->request->server['HTTP_REFERER'];
        } else {
            $referer = '';
        }

        $this->model_tool_crawler->addCrawler($ip, $url, $referer);

        $data['title'] = $this->language->get('text_title');
        $data['crawler'] = $this->language->get('text_crawler');
        $data['message'] = $this->language->get('text_message');

        $data['header'] = $this->load->controller('common/header');
        $data['nav'] = $this->load->controller('common/nav');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('common/crawler', $data));
    }
}
