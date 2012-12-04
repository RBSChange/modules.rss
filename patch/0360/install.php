<?php
/**
 * rss_patch_0360
 * @package modules.rss
 */
class rss_patch_0360 extends patch_BasePatch
{
	/**
	 * Entry point of the patch execution.
	 */
	public function execute()
	{
		$this->executeLocalXmlScript('list.xml');
	}
}