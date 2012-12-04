<?php
interface rss_ContentFormatter
{
	/**
	 * @param string $content
	 * @return string
	 */
	function format($content);
}