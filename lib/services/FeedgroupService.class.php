<?php
/**
 * @package modules.rss
 * @method rss_FeedgroupService getInstance()
 */
class rss_FeedgroupService extends f_persistentdocument_DocumentService
{
	/**
	 * @return rss_persistentdocument_feedgroup
	 */
	public function getNewDocumentInstance()
	{
		return $this->getNewDocumentInstanceByModelName('modules_rss/feedgroup');
	}

	/**
	 * Create a query based on 'modules_rss/feedgroup' model.
	 * Return document that are instance of modules_rss/feedgroup,
	 * including potential children.
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createQuery()
	{
		return $this->getPersistentProvider()->createQuery('modules_rss/feedgroup');
	}
	
	/**
	 * Create a query based on 'modules_rss/feedgroup' model.
	 * Only documents that are strictly instance of modules_rss/feedgroup
	 * (not children) will be retrieved
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createStrictQuery()
	{
		return $this->getPersistentProvider()->createQuery('modules_rss/feedgroup', false);
	}
}