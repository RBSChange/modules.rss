<?php
class rss_FeedChannel
{
	private $title;
	
	private $link;
	
	private $items = array();
	
	/**
	 * @param String $xml
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
			$title = $chanel->getElementsByTagName('title')->item(0);
			if ($title !== null)
			{
				$this->title = $title->textContent;
			}
			$link = $chanel->getElementsByTagName('link')->item(0);
			if ($link !== null)
			{
				$this->link = $link->textContent;
			}
			
			foreach ($chanel->getElementsByTagName('item') as $nodeItem)
			{
				$item = new rss_FeedItem($nodeItem, $richContentLevel, $this);
				$this->items[] = $item;
			}
		}
	}
	
	public function getTitle()
	{
		return $this->title;
	}
	
	public function getLink()
	{
		return $this->link;
	}
	
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
	
	public function sliceItems($start = 0, $count = 0)
	{
		if ($start > 0 || $count > 0)
		{
			$this->items = array_slice($this->items, $start, $count);
		}
	}
	
	public function getFeedType()
	{
		return 'RSS';
	}
	
	/**
	 * @param rssreader_FeedChannel[] $channelArray
	 * @param string $title;
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
				$items[date_Calendar::getInstance(
						date_Converter::convertDateToLocal($item->getDate()))->getTimestamp() . '-' . str_pad(
						$itemTotalCount, 5, '0')] = $item;
				$itemTotalCount--;
			}
		}
		krsort($items);
		$finalChanel->items = $items;
		return $finalChanel;
	}
}