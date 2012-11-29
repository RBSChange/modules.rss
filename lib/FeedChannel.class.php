<?php
class rss_FeedChannel
{
	/**
	 * @var string
	 */
	private $title;
	
	/**
	 * @var string
	 */
	private $link;
	
	/**
	 * @var rss_FeedItem[]
	 */
	private $items = array();
	
	/**
	 * @param string $xml
	 */
	public function __construct($xml, $richContentLevel)
	{
		$doc = new DOMDocument();
		if ($doc->loadXML($xml) === false)
		{
			return;
		}
		$chanel = $doc->getElementsByTagName('channel')->item(0);
		if ($chanel !== null)
		{
			foreach ($chanel->childNodes as $node)
			{
				switch ($node->nodeName)
				{
					case 'title' :
						$this->title = rss_FeedChannel::fixTitle($node->textContent);
						break;
					
					case 'link' :
						$this->link = rss_FeedChannel::fixUrl($node->textContent);
						break;
					
					case 'atom:link' :
						$this->link = rss_FeedChannel::fixUrl($node->getAttribute('href'));
						break;
					
					case 'item' :
						$item = new rss_FeedItem($node, $richContentLevel, $this);
						$this->items[] = $item;
						break;
				}
			}
		}
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
	public function getLink()
	{
		return $this->link;
	}
	
	/**
	 * @param integer $start
	 * @param integer $count
	 * @return rss_FeedItem[]
	 */
	public function getItems($start = 0, $count = 0)
	{
		if ($start == 0 && $count == 0)
		{
			return $this->items;
		}
		
		return array_slice($this->items, $start, $count);
	}
	
	/**
	 * @return boolean
	 */
	public function hasItems()
	{
		return count($this->items) > 0;
	}
	
	/**
	 * @return integer
	 */
	public function length()
	{
		return count($this->items);
	}
	
	/**
	 * @param integer $start
	 * @param integer $count
	 */
	public function sliceItems($start = 0, $count = 0)
	{
		if ($start > 0 || $count > 0)
		{
			$this->items = array_slice($this->items, $start, $count);
		}
	}
	
	/**
	 * @return string
	 */
	public function getFeedType()
	{
		return 'RSS';
	}
	
	/**
	 * @param rssreader_FeedChannel[] $channelArray
	 * @param string $title
	 * @return rssreader_FeedChannel
	 */
	public static function mergeItems($channelArray, $title = null)
	{
		$finalChanel = null;
		$items = array();
		$itemTotalCount = 0;
		foreach ($channelArray as $channel)
		{
			$itemTotalCount += count($channel->items);
		}
		foreach ($channelArray as $channel)
		{
			if ($finalChanel === null)
			{
				$finalChanel = $channel;
				if ($title !== null)
				{
					$finalChanel->title = $title;
				}
			}
			foreach ($channel->items as $item)
			{
				/* @var $item rss_FeedItem */
				$timestamp = date_Calendar::getInstance(date_Converter::convertDateToLocal($item->getDate()))->getTimestamp();
				$items[$timestamp . '-' . str_pad($itemTotalCount, 5, '0')] = $item;
				$itemTotalCount--;
			}
		}
		krsort($items);
		$finalChanel->items = $items;
		return $finalChanel;
	}
	
	/**
	 * @param string $url
	 * @return string
	 */
	public static function fixTitle($url)
	{
		// Titles should contain only plain text.
		return htmlspecialchars($url);
	}
	
	/**
	 * @param string $url
	 * @return string
	 */
	public static function fixUrl($url)
	{
		$url = trim($url);
		// Prevent JavaScript injections.
		if (f_util_StringUtils::beginsWith('javascript:', $url) || f_util_StringUtils::contains($url, '"'))
		{
			return '#';
		}
		return htmlspecialchars($url);
	}
}