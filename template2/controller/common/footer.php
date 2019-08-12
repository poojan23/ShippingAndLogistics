<?php

class ControllerCommonFooter extends PT_Controller
{
    public function index()
    {
        $this->load->language('common/footer');

        $this->load->model('tool/online');

        $this->load->model('tool/crawler');

        $data['name'] = $this->config->get('config_name');

        if (is_file(DIR_IMAGE . $this->config->get('config_logo_colour'))) {
            $data['logo_colour'] = $this->config->get('config_url') . 'image/' . $this->config->get('config_logo_colour');
        } else {
            $data['logo_colour'] = '';
        }

        $data['address'] = $this->config->get('config_address');
        $data['telephone'] = $this->config->get('config_telephone');
        $data['email'] = $this->config->get('config_email');

        if ($this->bots($this->request->server['HTTP_USER_AGENT'], $this->request->server['REMOTE_ADDR']) === FALSE) {
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

            $this->model_tool_online->addOnline($ip, $url, $referer);
        }

        $data['home'] = $this->url->link('common/home', 'language=' . $this->config->get('config_language'));
        $data['crawler'] = $this->url->link('common/crawler', 'language=' . $this->config->get('config_language'));

        return $this->load->view('common/footer', $data);
    }

    protected function bots($user_agent, $user_ip)
    {
        if (!empty($user_agent) && preg_match('/bot|crawl|curl|dataprovider|search|get|spider|find|java|majesticsEO|google|yahoo|teoma|contaxe|yandex|libwww-perl|facebookexternalhit/i', $user_agent)) {
            return true;
        }

        $this->load->model('tool/crawler');

        $results = $this->model_tool_crawler->getCrawlers();

        foreach ($results as $result) {
            if ($user_ip == $result['ip']) {
                return true;
            }
        }

        return false;
    }
}
