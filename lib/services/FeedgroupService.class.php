<?php
/**
 * rss_FeedgroupService
 * @package modules.rss
 */
class rss_FeedgroupService extends f_persistentdocument_DocumentService
{
	/**
	 * @var rss_FeedgroupService
	 */
	private static $instance;

	/**
	 * @return rss_FeedgroupService
	 */
	public static function getInstance()
	{
		if (self::$instance === null)
		{
			self::$instance = new self();
		}
		return self::$instance;
	}

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
		return $this->pp->createQuery('modules_rss/feedgroup');
	}
	
	/**
	 * Create a query based on 'modules_rss/feedgroup' model.
	 * Only documents that are strictly instance of modules_rss/feedgroup
	 * (not children) will be retrieved
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createStrictQuery()
	{
		return $this->pp->createQuery('modules_rss/feedgroup', false);
	}
}