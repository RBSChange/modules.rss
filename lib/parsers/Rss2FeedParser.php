<?php
class rss_Rss2FeedParser extends rss_AbstractFeedParser
{
	/**
	 * @param string $string
	 * @return rss_Channel|null
	 */
	public function parse($string)
	{
		$document = new DOMDocument();
		if ($document->loadXML($string) === false)
		{
			return null;
		}
		$channelElement = $document->getElementsByTagName('channel')->item(0);
		if (!$channelElement)
		{
			return null;
		}
		$formatter = $this->getFormatter();
		$channel = new rss_Channel();
		$this->parseChannel($channelElement, $channel);
		foreach ($channelElement->childNodes as $childNode)
		{
			if ($childNode->nodeName == 'item')
			{
				$item = new rss_ChannelItem($channel);
				$item->setFormatter($formatter);
				$this->parseItem($childNode, $item);
				$channel->addItem($item);
			}
		}
		return $channel;
	}
	
	/**
	 * @param DOMElement $element
	 * @param rss_Channel $channel
	 */
	protected function parseChannel($element, $channel)
	{
		foreach ($element->childNodes as $node)
		{
			switch ($node->nodeName)
			{
				case 'title' :
					$channel->setTitle(trim($node->textContent));
					break;
				
				case 'link' :
					$channel->setLink($this->parseUrl($node->textContent));
					break;
				
				case 'atom:link' :
					if (!$channel->getLink())
					{
						$channel->setLink($this->parseUrl($node->getAttribute('href')));
					}
					break;
					
				case 'description' :
					$channel->setDescription(trim($node->textContent));
					break;
			}
		}
	}
	
	/**
	 * @param DOMElement $element
	 * @param rss_ChannelItem $item
	 */
	protected function parseItem($element, $item)
	{
		foreach ($element->childNodes as $node)
		{
			switch ($node->nodeName)
			{
				case 'title' :
					$item->setTitle(trim($node->textContent));
					break;
		
				case 'link' :
					$item->setLink($this->parseUrl($node->textContent));
					break;
		
				case 'guid' :
					if (!$item->getLink())
					{
						$item->setLink($this->parseUrl($node->textContent));
					}
					break;
		
				case 'pubDate' :
					$item->setDate($this->parseDate(trim($node->textContent)));
					break;
		
				case 'description' :
					$item->setDescription(trim($node->textContent));
					break;
		
				case 'content:encoded' :
					if (!$item->getDescription())
					{
						$item->setDescription(trim($node->textContent));
					}
					break;
			}
		}
	}
	
	/**
	 * @param string $url
	 * @return string
	 */
	protected function parseUrl($url)
	{
		$url = trim($url);
		// Prevent JavaScript injections.
		if (f_util_StringUtils::contains($url, '"') || !preg_match('/^https?:\/\//i', $url))
		{
			return '#';
		}
		return $url;
	}
	
	/**
	 * @param string $date
	 * @return string
	 */
	protected function parseDate($date)
	{
		$terms = explode(' ', $date);
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
}