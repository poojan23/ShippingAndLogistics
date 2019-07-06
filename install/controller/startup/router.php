<?php

class ControllerStartupRouter extends PT_Controller
{
    public function index()
    {
        if (isset($this->request->get['url']) && $this->request->get['url'] != 'action/route') {
            return new Action($this->request->get['url']);
        } else {
            return new Action($this->config->get('action_default'));
        }
    }
}
