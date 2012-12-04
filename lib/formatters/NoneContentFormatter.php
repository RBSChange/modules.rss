<?php
class rss_NoneContentFormatter implements rss_ContentFormatter
{
	/**
	 * @param string $content
	 * @return string
	 */
	public function format($content)
	{
		return $content;
	}
}