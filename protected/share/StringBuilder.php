<?php
/**
 * @author Nikita Melnikov <n.melnikov@dengionline.com>
 * @link skype: xnicksevenfoldx
 */
class StringBuilder
{
	/**
	 * @var string
	 */
	protected $string;

	/**
	 * @param $string
	 */
	public function __construct($string = '')
	{
		$this->string = $string;
	}

	function __toString()
	{
		return $this->string;
	}

	/**
	 * @return string
	 */
	public function get()
	{
		return $this->string;
	}

	/**
	 * @param string $delimiter
	 * @return array
	 */
	public function explode($delimiter)
	{
		return explode($delimiter, $this->string);
	}

	/**
	 * Добавление к строке следующую
	 *
	 * @param string $string
	 * @param null $prefix префикс|символ перед основной строкой
	 * @return StringBuilder
	 */
	public function append($string, $prefix = null)
	{
		$this->string .= $prefix.$string;
		return $this;
	}

	/**
	 * @param $string
	 * @return StringBuilder
	 */
	public function remove($string)
	{
		$this->replace($string, null);
		return $this;
	}

	/**
	 * @param string $search
	 * @param string|callable $replace
	 * @param bool $regexp
	 * @return $this
	 */
	public function replace($search, $replace, $regexp = false)
	{
		if ($regexp) {
			if (is_callable($replace)) {
				$this->string = preg_replace_callback($search, $replace, $this->string);
			} else {
				$this->string = preg_replace($search, $replace, $this->string);
			}
		} else {
			$this-> string = str_replace($search, $replace, $this->string);
		}

		return $this;
	}
}