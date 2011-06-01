<?php
/**
 * @author intportg
 * @package modules.rss.lib
 */
interface rss_Item
{
	/**
	 * @see rss_FeedWriter::writeItem
	 * @return String
	 */
	public function getRSSLabel();
	
	/**
	 * @see rss_FeedWriter::writeItem
	 * @return String
	 */
	public function getRSSDescription();
	
	/**
	 * @see rss_FeedWriter::writeItem
	 * @return String
	 */
	public function getRSSGuid();
	
	/**
	 * @see rss_FeedWriter::writeItem
	 * @return String
	 */
	public function getRSSDate();
	
	/**
	 * Optionnal: if not defined, the link tag of the item will contain the guid.
	 * @see rss_FeedWriter::writeItem
	 * @return String
	 */
//	public function getRSSLink();
}