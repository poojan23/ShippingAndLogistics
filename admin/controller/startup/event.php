<?php

class ControllerStartupEvent extends PT_Controller
{
    public function index()
    {
        # Add events from the DB
        $this->load->model('setting/event');

        $results = $this->model_setting_event->getEvents();

        foreach ($results as $result) {
            if ((substr($result['trigger'], 0, 7) == 'sadmin/') && $result['status']) {
                $this->event->register(substr($result['trigger'], 7), new Action($result['action']), $result['sort_order']);
            }
        }
    }
}
