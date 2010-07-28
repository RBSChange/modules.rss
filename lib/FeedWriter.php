<?php
/**
 * @author intportg
 * @package modules.rss.lib
 */
class rss_FeedWriter
{
	/**
	 * @var XmlWriter
	 */
	private $xmlWriter;
	
	/**
	 * @var String
	 */
	private $link;
	
	/**
	 * @var String
	 */
	private $description;
	
	/**
	 * @var String
	 */
	private $title;
	
	/**
	 * @var String
	 */
	private $lang;
	
	/**
	 * @var rss_Item
	 */
	private $items = array();
	
	public function __construct()
	{
		$this->xmlWriter = new XMLWriter();
		$this->xmlWriter->openMemory();
		if (Framework::isDebugEnabled())
		{
			$this->xmlWriter->setIndent(true);
		}
	}
	
	/**
	 * @return String
	 */
	public function getDescription()
	{
		return $this->description;
	}
	
	/**
	 * @param String $description
	 */
	public function setDescription($description)
	{
		$this->description = $description;
	}
	
	/**
	 * @return Boolean
	 */
	public function hasDescription()
	{
		return $this->description !== null;
	}
	
	/**
	 * @return String
	 */
	public function getLang()
	{
		return $this->lang;
	}
	
	/**
	 * @param String $lang
	 */
	public function setLang($lang)
	{
		$this->lang = $lang;
	}
	
	/**
	 * @return Boolean
	 */
	public function hasLang()
	{
		return $this->lang !== null;
	}
	
	/**
	 * @return String
	 */
	public function getLink()
	{
		return $this->link;
	}
	
	/**
	 * @param String $link
	 */
	public function setLink($link)
	{
		$this->link = $link;
	}
	
	/**
	 * @return Boolean
	 */
	public function hasLink()
	{
		return $this->link !== null;
	}
	
	/**
	 * @return String
	 */
	public function getTitle()
	{
		return $this->title;
	}
	
	/**
	 * @param String $title
	 */
	public function setTitle($title)
	{
		$this->title = $title;
	}
	
	/**
	 * @return Boolean
	 */
	public function hasTitle()
	{
		return $this->title !== null;
	}
	

	/**
	 * @param rss_Item $item
	 */
	public function addItem($item)
	{
		$this->items[] = $item;
	}
	
	/**
	 * @return String
	 */
	public function toString()
	{
		if (!$this->hasLink() || !$this->hasTitle() || ! $this->hasDescription())
		{
			throw new IllegalArgumentException(__METHOD__ . ': RSS Feed has no link, title or description');
		}
		$this->startFeed();
		$this->writeContent();
		$this->endFeed();
		return $this->xmlWriter->outputMemory(true);
	}
	
	/**
	 * @param rss_Item $item
	 */
	protected function writeItem($item)
	{
		$this->addElement('title', $item->getRSSLabel());
		$this->addElement('description', $item->getRSSDescription());
		$this->addElement('guid', str_replace('&amp;', '&', $item->getRSSGuid()));
		$date = date_Calendar::getInstance($item->getRSSDate());
		$this->addElement('pubDate', gmdate('r', $date->getTimestamp()));
	}
	
	/**
	 * @param String $name
	 * @param String $value
	 */
	private function addElement($name, $value)
	{
		$this->xmlWriter->startElement($name);
		$this->xmlWriter->text($value);
		$this->xmlWriter->endElement();
	}
	
	private function startFeed()
	{
		$this->xmlWriter->startDocument('1.0', 'UTF-8');
		$this->xmlWriter->startElement('rss');
		$this->xmlWriter->writeAttribute('xmlns:atom', 'http://www.w3.org/2005/Atom');
		$this->xmlWriter->writeAttribute('version', '2.0');
		$this->xmlWriter->startElement('channel');
		$this->writeOptionalTags();
		$this->addElement('link', $this->getLink());
		$this->addElement('description', $this->getDescription());
		$this->addElement('title', $this->getTitle());
		$this->addElement('language', $this->hasLang() ? $this->getLang() : RequestContext::getInstance()->getLang());
	}
	
	private function writeOptionalTags()
	{
		$this->xmlWriter->startElement('atom:link');
		$this->xmlWriter->writeAttribute('href', LinkHelper::getCurrentUrlComplete());
		$this->xmlWriter->writeAttribute('rel', 'self');
		$this->xmlWriter->writeAttribute('type', 'application/rss+xml');
		$this->xmlWriter->endElement();
	}
	
	private function writeContent()
	{
		foreach ($this->items as $item)
		{
			$this->xmlWriter->startElement('item');
			$this->writeItem($item);
			$this->xmlWriter->endElement();
		}
	}
	
	private function endFeed()
	{
		$this->xmlWriter->endElement();
		$this->xmlWriter->endElement();
		$this->xmlWriter->endDocument();
	}
}