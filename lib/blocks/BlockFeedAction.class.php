<?php
/**
 * @deprecated use rss_BlockChannelAction
 */
class rss_BlockFeedAction extends website_BlockAction
{
	/**
	 * @deprecated
	 */
	public function execute($request, $response)
	{
		if ($this->isInBackofficeEdition())
		{
			return website_BlockView::NONE;
		}
		
		$document = $this->getDocumentParameter();
		if ($document === null || ! $document->isPublished())
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
				$c = new rss_FeedChannel($xmlContent, $this->getConfiguration()->getCleanLevel());
				if ($c->hasItems())
				{
					$channels[] = $c;
				}
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