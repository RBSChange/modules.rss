<?php
/**
 * @package modules.rss
 */
class rss_Setup extends object_InitDataSetup
{
	public function install()
	{
		$this->executeModuleScript('init.xml');
	}
}