<?php
/**
 * @package modules.rss
 * @method rss_ModuleService getInstance()
 */
class rss_ModuleService extends ModuleBaseService
{
	/**
	 * @param rss_Channel[] $channels
	 * @return rss_Channel
	 */
	public function mergeChannels($channels, $maxItemCount = null)
	{
		if (count($channels) == 1)
		{
			return f_util_ArrayUtils::firstElement($channels);
		}
		
		$mergedChannel = new rss_Channel();
		$mergedChannel->setTitle($this->mergeChannelTitles($channels));
		
		$items = array();
		foreach ($channels as $channel)
		{
			/* @var $channel rss_Channel */
			foreach ($channel->getItems(0, $maxItemCount) as $item)
			{
				/* @var $item rss_ChannelItem */
				$timestamp = date_Calendar::getInstance($item->getDate())->getTimestamp();
				$items[$timestamp . '-' . str_pad(count($items), 5, '0')] = $item;
			}
		}
		krsort($items);
		if ($maxItemCount)
		{
			$items = array_slice($items, 0, $maxItemCount);
		}
		$mergedChannel->setItems($items);
		return $mergedChannel;
	}
	
	/**
	 * @param rss_Channel[] $channels
	 * @return string
	 */
	protected function mergeChannelTitles($channels)
	{
		$titles = array();
		foreach ($channels as $channel)
		{
			$titles[] = $channel->getTitle();
		}
		return implode(', ', $titles);
	}
}