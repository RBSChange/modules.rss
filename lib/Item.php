<?php
/**
 * @package modules.rss.lib
 */
interface rss_Item
{
	/**
	 * @see rss_FeedWriter::writeItem
	 * @return string
	 */
	public function getRSSLabel();
	
	/**
	 * @see rss_FeedWriter::writeItem
	 * @return string
	 */
	public function getRSSDescription();
	
	/**
	 * @example return LinkHelper::getPermalink($this);
	 * @see rss_FeedWriter::writeItem
	 * @return string
	 */
	public function getRSSGuid();
	
	/**
	 * @see rss_FeedWriter::writeItem
	 * @return string
	 */
	public function getRSSDate();
	
	/**
	 * @example return LinkHelper::getDocumentUrl($this);
	 * @see rss_FeedWriter::writeItem
	 * @return string
	 */
	public function getRSSLink();
}