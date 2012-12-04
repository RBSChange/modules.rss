<?php
abstract class rss_AbstractFeedParser
{
	/**
	 * @var rss_AbstractContentFormatter
	 */
	protected $formatter;
	
	/**
	 * @param rss_AbstractContentFormatter $formatter
	 */
	public function __construct($formatter)
	{
		$this->formatter = $formatter;
	}
	
	/**
	 * @param rss_AbstractContentFormatter $formatter
	 */
	public function setFormatter($formatter)
	{
		$this->formatter = $formatter;
	}
	
	/**
	 * @return rss_ContentFormatter
	 */
	protected function getFormatter()
	{
		return $this->formatter;
	}
	
	/**
	 * @param string $string
	 * @return rss_Channel
	 */
	public abstract function parse($string);
}