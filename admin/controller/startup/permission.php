<?php

class ControllerStartupPermission extends PT_Controller
{
    public function index()
    {
        if (isset($this->request->get['url'])) {
            $route = '';

            $part = explode('/', $this->request->get['url']);

            if (isset($part[0])) {
                $route .= $part[0];
            }

            if (isset($part[1])) {
                $route .= '/' . $part[1];
            }

            # We want to ignore some pages from having its permission checked
            $ignore = array(
                'common/dashboard',
                'user/login',
                'user/logout',
                'user/forgotten',
                'user/reset',
                'common/cron',
                'error/not_found',
                'error/permission'
            );

            if (!in_array($route, $ignore) && !$this->user->hasPermission('access', $route)) {
                return new Action('error/permission');
            }
        }
    }
}
