<?php
/**
 * @author intportg
 * @package modules.rss.lib
 */
interface rss_Item
{
	/**
	 * @return String
	 */
	public function getRSSLabel();
	
	/**
	 * @return String
	 */
	public function getRSSDescription();
	
	/**
	 * @return String
	 */
	public function getRSSGuid();
	
	/**
	 * @return String
	 */
	public function getRSSDate();
}
?>