<?php
/**
 * @package modules.rss
 * @method rss_BlockChannelConfiguration getConfiguration()
 */
class rss_BlockChannelAction extends website_BlockAction
{
	/**
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @return String
	 */
	public function execute($request, $response)
	{
		if ($this->isInBackofficeEdition())
		{
			return website_BlockView::NONE;
		}
	
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
		
		// Parse feeds as rss_Channel objects.
		$channels = array();
		foreach ($feeds as $feed)
		{
			$content = $feed->getDocumentService()->getRawContent($feed);
			if ($content !== null)
			{
				$channel = $this->getParser($feed)->parse($content);
				if ($channel && $channel->hasItems())
				{
					$channels[] = $channel;
				}
			}
		}
		if (count($channels) === 0)
		{
			return website_BlockView::NONE;
		}
		
		// Merge the channels.
		$mergedChannel = rss_ModuleService::getInstance()->mergeChannels($channels, $this->getConfiguration()->getItemCount());
		if ($this->getConfiguration()->getUseLabel())
		{
			$mergedChannel->setTitle($document->getLabel());
		}
		
		$request->setAttribute('document', $document);
		$request->setAttribute('channel', $mergedChannel);
		$request->setAttribute('backoffice', $this->isInBackofficeEdition());
		
		return $this->getConfiguration()->getDisplayMode();
	}
	
	/**
	 * @param rss_persistentdocument_feed $feed
	 * @return rss_AbstractFeedParser
	 */
	protected function getParser($feed)
	{
		$formatterClass = $this->getConfiguration()->getContentFormatter();
		$formatter = new $formatterClass();
		$parser = new rss_Rss2FeedParser($formatter);
		return $parser;
	}
}