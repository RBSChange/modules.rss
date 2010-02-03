<?php
/**
 * @package modules.rss.lib.services
 */
class rss_ModuleService extends ModuleBaseService
{
	/**
	 * Singleton
	 * @var rss_ModuleService
	 */
	private static $instance = null;

	/**
	 * @return rss_ModuleService
	 */
	public static function getInstance()
	{
		if (is_null(self::$instance))
		{
			self::$instance = self::getServiceClassInstance(get_class());
		}
		return self::$instance;
	}
	
	/**
	 * @param Integer $documentId
	 * @return f_persistentdocument_PersistentTreeNode
	 */
//	public function getParentNodeForPermissions($documentId)
//	{
//		// Define this method to handle permissions on a virtual tree node. Example available in list module.
//	}
}