<?php
class ControllerStartupMaintenance extends PT_Controller
{
	public function index()
	{
		if ($this->config->get('config_maintenance')) {
			# Route
			if (isset($this->request->get['url']) && $this->request->get['url'] != 'startup/router') {
				$route = $this->request->get['url'];
			} else {
				$route = $this->config->get('action_default');
			}

			$ignore = array(
				'common/language/language',
				'common/currency/currency'
			);

			# Show site if logged in as admin
			$this->user = new Account\User($this->registry);

			if ((substr($route, 0, 17) != 'extension/payment' && substr($route, 0, 3) != 'api') && !in_array($route, $ignore) && !$this->user->isLogged()) {
				return new Action('common/maintenance');
			}
		}
	}
}
