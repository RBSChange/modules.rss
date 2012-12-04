<?php
class rss_TextContentFormatter implements rss_ContentFormatter
{
	/**
	 * @param string $content
	 * @return string
	 */
	public function format($content)
	{
		return f_util_HtmlUtils::htmlToText($content);
	}
}