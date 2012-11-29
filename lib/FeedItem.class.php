<?php
class rss_FeedItem
{
	/**
	 * @var string
	 */
	private $title;
	
	/**
	 * @var string
	 */
	private $permalink;
	
	/**
	 * @var string
	 */
	private $rawDate;
	
	/**
	 * @var string
	 */
	private $date;
	
	/**
	 * @var string
	 */
	private $description;
	
	/**
	 * @var string
	 */
	private $channelLink;
	
	/**
	 * @var string
	 */
	private $channelTitle;
	
	/**
	 * @param DOMElement $item
	 * @param string $richContentLevel
	 * @param rss_FeedChannel $parent
	 */
	public function __construct($item, $richContentLevel, $parent)
	{
		foreach ($item->childNodes as $node)
		{
			switch ($node->nodeName)
			{
				case 'title' :
					$this->title = rss_FeedChannel::fixTitle($node->textContent);
					break;
				
				case 'link' :
					$this->permalink = rss_FeedChannel::fixUrl($node->textContent);
					break;
				
				case 'guid' :
					if (!$this->link)
					{
						$this->permalink = rss_FeedChannel::fixUrl($node->getAttribute('textContent'));
					}
					break;
				
				case 'pubDate' :
					$date = trim($node->textContent);
					$this->date = $this->parseRSSDate($date);
					$this->rawDate = $date;
					break;
				
				case 'description' :
					$this->description = $this->filterContent(trim($node->textContent), $richContentLevel);
					break;
			}
		}
		
		$this->channelLink = $parent->getLink();
		$this->channelTitle = $parent->getTitle();
	}
	
	/**
	 * @return string
	 */
	public function getPermalink()
	{
		return $this->permalink;
	}
	
	/**
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}
	
	/**
	 * @return string
	 */
	public function getCropTitle()
	{
		return f_util_StringUtils::shortenString($this->title, 80);
	}
	
	/**
	 * @return string
	 */
	public function getRawDate()
	{
		return $this->rawDate;
	}
	
	/**
	 * @return string
	 */
	public function getDate()
	{
		return $this->date;
	}
	
	/**
	 * @param string $format
	 * @return string
	 */
	public function getLocalDate($format = 'd/m/Y - H:i')
	{
		$date = date_Calendar::getInstance(date_Converter::convertDateToLocal($this->date));
		return date_DateFormat::format($date, $format);
	}
	
	/**
	 * @return string
	 */
	public function getContentForBackoffice()
	{
		$warning = MediaHelper::getIcon('warning', 'small');
		$text = LocaleService::getInstance()->transFO('m.rss.bo.general.not-display-bo', array('ucf'));
		return '<img src="' . $warning . '" style="border:0; margin-top:-5px; margin-right:5px" />' . $text;
	}
	
	/**
	 * @return string
	 */
	public function getContent()
	{
		return $this->description;
	}
	
	/**
	 * @param string $html
	 * @param string $richContentLevel
	 * @return string
	 */
	private function filterContent($html, $richContentLevel)
	{
		// Prevent JavaScript injections.
		$html = preg_replace('/on[a-zA-Z]+="[^"]*"/mi', '', $html);
		$html = preg_replace('/([a-zA-Z]+)="\s*javascript:[^"]*"/mi', '\\1="#"', $html);
		
		$tagsSometimes = '<embed><img>';
		$tagsAllowed = '<a><acronym><address><b><blockquote><br><center><cite><code><dd><div><dl><dt><em><fieldset><font><h2><h3><h4><h5><h6><i><li><map><ol><p><pre><q><s><small><span><strike><strong><sub><sup><table><tbody><td><tfoot><th><thead><title><tr><tt><u><ul>';
		$tagmin = '<a><p><br>';
		
		if ($richContentLevel == 'high')
		{
			return strip_tags($html, $tagmin);
		}
		elseif ($richContentLevel == 'medium')
		{
			return strip_tags($html, $tagsAllowed);
		}
		elseif ($richContentLevel == 'low')
		{
			return strip_tags($html, $tagsAllowed . $tagsSometimes);
		}
		
		return $html;
	}
	
	/**
	 * @param string $string
	 * @return string
	 */
	private function parseRSSDate($string)
	{
		$terms = explode(' ', $string);
		if (!is_numeric($terms[0]))
		{
			$terms = array_slice($terms, 1);
		}
		$day = intval($terms[0]);
		$month = array_search($terms[1], array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec')) + 1;
		$year = $terms[2];
		list ($hour, $minute, $second) = explode(':', $terms[3] . ':00');
		$timestamp = mktime($hour, $minute, $second, $month, $day, $year);
		return date('Y-m-d H:i:s', $timestamp);
	}
	
	/**
	 * @return string
	 */
	public function getChannelLink()
	{
		return $this->channelLink;
	}
	
	/**
	 * @return string
	 */
	public function getChannelTitle()
	{
		return $this->channelTitle;
	}
}