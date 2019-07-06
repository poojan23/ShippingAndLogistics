<?php

class ControllerStartupRouter extends PT_Controller
{
    public function index()
    {
        if (isset($this->request->get['url']) && $this->request->get['url'] != 'startup/router') {
            $route = $this->request->get['url'];
        } else {
            $route = $this->config->get('action_default');
        }

        $data = array();

        # Sanitize the call
        $route = preg_replace('/[^a-zA-Z0-9_\/]/', '', (string)$route);

        # Trigger the pre events
        $result = $this->event->trigger('controller/' . $route . '/before', array(&$route, &$data));

        if (!is_null($result)) {
            return $result;
        }

        $action = new Action($route);

        # Any output needs to be another Action object
        $output = $action->execute($this->registry, $data);

        # Trigger the post events
        $result = $this->event->trigger('controller/' . $route . '/after', array(&$route, &$output));

        if (!is_null($result)) {
            return $result;
        }

        return $output;
    }
}
