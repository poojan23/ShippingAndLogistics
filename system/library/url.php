<?php

class Url
{
	private $url;
	private $rewrite = array();

	public function __construct($url, $ssl = '')
	{
		$this->url = $url;
	}

	public function addRewrite($rewrite)
	{
		$this->rewrite[] = $rewrite;
	}

	public function link($route, $args = '')
	{
		$url = $this->url . 'index.php?url=' . (string)$route;

		if ($args) {
			if (is_array($args)) {
				$url .= '&amp;' . http_build_query($args, '', '&amp;');
			} else {
				$url .= str_replace('&', '&amp;', '&' . ltrim($args, '&'));
			}
		}

		foreach ($this->rewrite as $rewrite) {
			$url = $rewrite->rewrite($url);
		}

		return $url;
	}
}
