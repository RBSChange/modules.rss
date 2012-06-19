<?php
/**
 * rss_FeedScriptDocumentElement
 * @package modules.rss.persistentdocument.import
 */
class rss_FeedScriptDocumentElement extends import_ScriptDocumentElement
{
	/**
	 * @return rss_persistentdocument_feed
	 */
	protected function initPersistentDocument()
	{
		return rss_FeedService::getInstance()->getNewDocumentInstance();
	}
	
	/**
	 * @return f_persistentdocument_PersistentDocumentModel
	 */
	protected function getDocumentModel()
	{
		return f_persistentdocument_PersistentDocumentModel::getInstanceFromDocumentModelName('modules_rss/feed');
	}
}