<?php
class rss_ChannelItem
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
	protected $date;
	
	/**
	 * @var string
	 */
	protected $description;
	
	/**
	 * @var string
	 */
	protected $channel;
	
	/**
	 * @var rss_ContentFormatter
	 */
	protected $formatter;
	
	/**
	 * @param rss_Channel $channel
	 */
	public function __construct($channel)
	{
		$this->channel = $channel;
	}
	
	/**
	 * @param string $channel
	 */
	public function setChannel($channel)
	{
		$this->channel = $channel;
	}
	
	/**
	 * @return string
	 */
	public function getChannel()
	{
		return $this->channel;
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
		return htmlspecialchars($this->link, ENT_COMPAT, "utf-8");
	}
	
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
	 * @param integer $maxLenght
	 * @return string
	 */
	public function getShortTitle($maxLenght = 80)
	{
		return f_util_StringUtils::shortenString($this->title, $maxLenght);
	}
	
	/**
	 * @param integer $maxLenght
	 * @return string
	 */
	public function getShortTitleAsHtml($maxLenght = 80)
	{
		return f_util_StringUtils::shortenString(f_util_HtmlUtils::textToHtml($this->title), $maxLenght);
	}
	
	/**
	 * @param string date
	 */
	public function setDate($date)
	{
		$this->date = $date;
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
	public function getUIDate()
	{
		return date_Converter::convertDateToLocal($this->date);
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
		$formatter = $this->getFormatter();
		if ($formatter instanceof rss_ContentFormatter)
		{
			return $formatter->format($this->description);
		}
		return $this->description;
	}
	
	/**
	 * @param rss_ContentFormatter $formatter
	 */
	public function setFormatter($formatter)
	{
		$this->formatter = $formatter;
	}
	
	/**
	 * @return rss_ContentFormatter
	 */
	public function getFormatter()
	{
		return $this->formatter;
	}
}