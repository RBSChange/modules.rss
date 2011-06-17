<?php
/**
 * rss_BlockFeedAction
 * @package modules.rss.lib.blocks
 */
class rss_BlockFeedAction extends website_BlockAction
{
	/**
	 * @see website_BlockAction::execute()
	 *
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @return String
	 */
	public function execute($request, $response)
	{
                $document = $this->getDocumentParameter();
		if ($document === null || !$document->isPublished())
		{
			return website_BlockView::NONE;
		}
                if ($document instanceof rss_persistentdocument_feed)
                {
                    $feeds = array($document);
                }
                else if ($document instanceof rss_persistentdocument_feedgroup)
                {
                    $feeds = $document->getPublishedFeedArray();
                }
                $channels = array();
                foreach ($feeds as $feed)
                {
                    $xmlContent = $feed->getDocumentService()->getRawContent($feed);
                    if ($xmlContent !== null)
                    {
                        $channels[] = new rss_FeedChannel($xmlContent, $this->getConfiguration()->getCleanLevel());
                    }
                    
                }
                if (count($channels) === 0)
                {
                     return website_BlockView::ERROR;
                }
                $request->setAttribute('document', $document);
                $request->setAttribute('channel', rss_FeedChannel::mergeItems($channels));
		$titles = array();
		foreach ($channels as $channel)
		{
		    $request->setAttribute($channel->getTitle(), $channel->getLink());
		    $titles[] = $channel->getTitle();
		}
		$request->setAttribute('title', implode(", ", $titles));
                $request->setAttribute('backoffice', $this->isInBackofficeEdition());
		
		return $this->getConfiguration()->getDisplayMode();
	}
}