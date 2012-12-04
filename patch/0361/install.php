<?php
/**
 * rss_patch_0361
 * @package modules.rss
 */
class rss_patch_0361 extends patch_BasePatch
{
	/**
	 * Entry point of the patch execution.
	 */
	public function execute()
	{
		// Override blocks.xml.
		$srcFile = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'blocks.xml';
		$destFile = f_util_FileUtils::buildOverridePath('modules', 'rss', 'config', 'blocks.xml');
		if (!file_exists($destFile))
		{
			f_util_FileUtils::cp($srcFile, $destFile);
		}
		elseif (file_get_contents($srcFile) !== file_get_contents($destFile))
		{
			$this->logWarning('File (' . $destFile . ') already exist.');
		}
	}
}