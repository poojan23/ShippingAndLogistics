<?php

class ControllerStartupLogin extends PT_Controller
{
    public function index()
    {
        $route = isset($this->request->get['url']) ? $this->request->get['url'] : '';

        $ignore = array(
            'user/login',
            'user/forgotten',
            'user/reset',
            'common/cron'
        );

        # User
        $this->registry->set('user', new Account\User($this->registry));

        if (!$this->user->isLogged() && !in_array($route, $ignore)) {
            return new Action('user/login');
        }

        if (isset($this->request->get['url'])) {
            $ignore = array(
                'user/login',
                'user/logout',
                'user/forgotten',
                'user/reset',
                'common/cron',
                'error/not_found',
                'error/permission'
            );

            if (!in_array($route, $ignore) && (!isset($this->request->get['user_token']) || !isset($this->session->data['user_token']) || ($this->request->get['user_token'] != $this->session->data['user_token']))) {
                return new Action('user/login');
            }
        } else {
            if (!isset($this->request->get['user_token']) || !isset($this->session->data['user_token']) || ($this->request->get['user_token'] != $this->session->data['user_token'])) {
                return new Action('user/login');
            }
        }
    }
}
