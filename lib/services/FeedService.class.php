<?php
/**
 * rss_FeedService
 * @package modules.rss
 */
class rss_FeedService extends f_persistentdocument_DocumentService
{
        const FEED_ITEM_CACHE_NS = 'feedItemCache';
	/**
	 * @var rss_FeedService
	 */
	private static $instance;

	/**
	 * @return rss_FeedService
	 */
	public static function getInstance()
	{
		if (self::$instance === null)
		{
			self::$instance = self::getServiceClassInstance(get_class());
		}
		return self::$instance;
	}

	/**
	 * @return rss_persistentdocument_feed
	 */
	public function getNewDocumentInstance()
	{
		return $this->getNewDocumentInstanceByModelName('modules_rss/feed');
	}

	/**
	 * Create a query based on 'modules_rss/feed' model.
	 * Return document that are instance of modules_rss/feed,
	 * including potential children.
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createQuery()
	{
		return $this->pp->createQuery('modules_rss/feed');
	}
	
	/**
	 * Create a query based on 'modules_rss/feed' model.
	 * Only documents that are strictly instance of modules_rss/feed
	 * (not children) will be retrieved
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createStrictQuery()
	{
		return $this->pp->createQuery('modules_rss/feed', false);
	}
	
        /**
         *
         * @param rss_persistentdocument_feed $feed 
         */
        public function getRawContent($feed)
        {
            $cacheItem = null;
            $useCache = $feed->getExpirestime() > 0;
            $dcs = f_DataCacheService::getInstance();
            if ($useCache)
            {
                $cacheItem = $dcs->readFromCache(self::FEED_ITEM_CACHE_NS, array('feedId' => $feed->getId()));
                if ($cacheItem !== null)
                {
                    $cacheItem->setTTL($feed->getExpirestime()*60);
                }
            }
            if ($cacheItem !== null && $cacheItem->isValid()) 
            {
                return $cacheItem->getValue("xml");
            }
            // No cache or expired cache
            $feedUrl = $feed->getFeedurl();
            $client = HTTPClientService::getInstance()->getNewHTTPClient();
            $data = $client->get($feedUrl);
            if ($client->getHTTPReturnCode() != 200)
            {
                return null;
            }
            
            if ($useCache)
            {
                $cacheItem = $dcs->getNewCacheItem(self::FEED_ITEM_CACHE_NS, array('feedId' => $feed->getId()), array('modules_rss/feed'));
                $cacheItem->setTTL($feed->getExpirestime()*60);
                $cacheItem->setValue("xml", $data);
                $dcs->writeToCache($cacheItem);
            }
            return $data;
        }
		
}