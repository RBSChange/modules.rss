<?php
/**
 * rss_FeedgroupScriptDocumentElement
 * @package modules.rss.persistentdocument.import
 */
class rss_FeedgroupScriptDocumentElement extends import_ScriptDocumentElement
{
    /**
     * @return rss_persistentdocument_feedgroup
     */
    protected function initPersistentDocument()
    {
    	return rss_FeedgroupService::getInstance()->getNewDocumentInstance();
    }
    
    /**
	 * @return f_persistentdocument_PersistentDocumentModel
	 */
	protected function getDocumentModel()
	{
		return f_persistentdocument_PersistentDocumentModel::getInstanceFromDocumentModelName('modules_rss/feedgroup');
	}
}