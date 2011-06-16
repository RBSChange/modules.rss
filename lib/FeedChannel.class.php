<?php
class rss_FeedChannel
{
	private $title;
	
	private $items = array();
	
	/**
	 * @param String $xml
	 */
	public function __construct($xml, $richContentLevel)
	{
		$doc = new DOMDocument();
		$doc->loadXML($xml);
		
		$chanel = $doc->getElementsByTagName('channel')->item(0);
		if ($chanel !== null)
		{
			$title = $chanel->getElementsByTagName('title')->item(0);
			if ($title !== null)
			{
				$this->title = $title->textContent;	
			}
			
			foreach ($chanel->getElementsByTagName('item') as $nodeItem) 
			{
				$item = new rss_FeedItem($nodeItem, $richContentLevel);
				$this->items[] = $item;
			}
		}
	}
	
	public function getTitle()
	{
		return $this->title;
	}
	
	public function getItems($start = 0, $count = 0)
	{
		if ($start == 0 && $count == 0)
		{
			return $this->items;
		}
		
		return array_slice($this->items, $start, $count);
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
                $itemCount = 0;
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
                            $itemCount++;
                            /* @var $item rss_FeedItem */
                            $items[date_Calendar::getInstance(date_Converter::convertDateToLocal($item->getDate()))->getTimestamp().'-'.  str_pad($itemCount, 5, '0')] = $item;
                        }
		}
                krsort($items);
                $finalChanel->items = $items;
		return $finalChanel;
	}
}