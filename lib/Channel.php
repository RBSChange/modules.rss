<?php
class rss_Channel
{
	/**
	 * @var string
	 */
	protected $title;
	
	/**
	 * @var string
	 */
	protected $link;
	
	/**
	 * @var string
	 */
	protected $description;
	
	/**
	 * @var rss_ChannelItem[]
	 */
	protected $items = array();
	
	/**
	 * @param string $title
	 */
	public function setTitle($title)
	{
		$this->title = $title;
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
	public function getTitleAsHtml()
	{
		return f_util_HtmlUtils::textToHtml($this->title);
	}
	
	/**
	 * @param string $link
	 */
	public function setLink($link)
	{
		$this->link = $link;
	}
	
	/**
	 * @return string
	 */
	public function getLink()
	{
		return $this->link;
	}
	
	/**
	 * @return string
	 */
	public function getLinkAsHtml()
	{
		return f_util_HtmlUtils::textToHtml($this->link);
	}
	
	/**
	 * @param string $description
	 */
	public function setDescription($description)
	{
		$this->description = $description;
	}
	
	/**
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
	}
	
	/**
	 * @return string
	 */
	public function getDescriptionAsHtml()
	{
		return f_util_HtmlUtils::textToHtml($this->description);
	}
	
	/**
	 * @param rss_ChannelItem[] $items
	 */
	public function setItems($items)
	{
		$this->items = array_values($items);
	}
	
	/**
	 * @param rss_ChannelItem $item
	 */
	public function addItem($item)
	{
		$this->items[] = $item;
	}
	
	/**
	 * @return boolean
	 */
	public function hasItems()
	{
		return count($this->items) > 0;
	}
	
	/**
	 * @param integer $start
	 * @param integer $count
	 * @return rss_ChannelItem[]
	 */
	public function getItems($start = 0, $count = null)
	{
		if ($start == 0 && $count === null)
		{
			return $this->items;
		}
		return array_slice($this->items, $start, $count);
	}
	
	/**
	 * @return integer
	 */
	public function getItemCount()
	{
		return count($this->items);
	}
}