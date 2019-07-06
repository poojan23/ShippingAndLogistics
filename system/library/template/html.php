<?php
namespace Template;

final class Html
{
	protected $code;
	protected $data = array();

	public function set($key, $value)
	{
		$this->data[$key] = $value;
	}

	public function render($filename, $cache = true)
	{
		$file = DIR_TEMPLATE . $filename . '.html';

		if (is_file($file)) {
			$this->code = file_get_contents($file);

			ob_start();

			extract($this->data);

			echo eval('?>' . $this->code);

			/*if (!$cache && function_exists('eval')) {
				extract($this->data);

				echo eval('?>' . $this->code);
			} else {
				extract($this->data);

				include($this->compile($file, $this->code));
			}*/

			return ob_get_clean();
		} else {
			throw new \Exception('Error: Could not load template ' . $file . '!');
			exit();
		}
	}

	/*public function render($filename, $code = '')
	{
		if (!$code) {
			$file = DIR_TEMPLATE . $filename . '.html';

			if (is_file($file)) {
				$code = file_get_contents($file);

				ob_start();

				extract($this->data);

				include($this->compile($filename . '.html', $code));

				return ob_get_clean();
			} else {
				throw new \Exception('Error: Could not load template ' . $file . '!');
				exit();
			}
		}

		if ($code) {
			ob_start();

			extract($this->data);

			include($this->compile($filename . '.html', $code));

			return ob_get_clean();
		}
	}*/

	protected function compile($filename, $code)
	{
		$file = DIR_CACHE . 'template/' . hash('md5', $filename . $code) . '.php';

		if (!is_file($file)) {
			file_put_contents($file, $code, LOCK_EX);
		}

		return $file;
	}
}
