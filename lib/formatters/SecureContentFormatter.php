<?php
class rss_SecureContentFormatter implements rss_ContentFormatter
{
	/**
	 * @var string[]
	 */
	protected $removedElements;
	
	/**
	 * @var string[]
	 */
	protected $safeAttributes;
	
	/**
	 */
	public function __construct()
	{
		$this->removedElements = array('applet', 'button', 'canvas', 'command', 'embed', 'form', 'frame', 'frameset', 'iframe', 'input', 
			'map', 'noframes', 'soscript', 'object', 'optgroup', 'option', 'output', 'param', 'script', 'select', 'style', 'textarea');
		$this->safeAttributes = array('dir', 'lang', 'title');
	}
	
	/**
	 * @param string $content
	 * @return string
	 */
	public function format($content)
	{
		$document = new DOMDocument('1.0', 'UTF-8');
		if (!$document->loadXML('<div>' . $content . '</div>'))
		{
			// If the content is not a parsable HTML fragment, strip all tags.
			return f_util_HtmlUtils::htmlToText($content);
		}
		
		$finalDocument = new DOMDocument('1.0', 'UTF-8');
		$finalDocument->loadXML('<div></div>');
		$this->parseElement($document->documentElement, $finalDocument->documentElement, true);
		return $finalDocument->saveXML($finalDocument->documentElement);
	}
	
	/**
	 * @param DOMElement $element
	 * @param DOMElement $finalParent
	 */
	protected function parseElement($element, $finalParent, $isRoot = false)
	{
		if ($isRoot)
		{
			$newNode = $finalParent;
		}
		else
		{
			$elementName = strtolower($element->localName);
			$getter = 'convert' . ucfirst($elementName);
			if (in_array($elementName, $this->removedElements))
			{
				$newNode = null;
			}
			elseif (method_exists($this, $getter))
			{
				$newNode = $this->$getter($element, $finalParent);
			}
			else
			{
				$newNode = $this->defaultConvert($element, $finalParent);
			}
		}
		
		if ($newNode)
		{
			foreach ($element->childNodes as $node)
			{
				switch ($node->nodeType)
				{
					case XML_ELEMENT_NODE :
						$this->parseElement($node, $newNode);
						break;
					
					case XML_TEXT_NODE :
						$newNode->appendChild($newNode->ownerDocument->createTextNode($node->data));
						break;
				}
			}
		}
	}
	
	/**
	 * @param string[] $attributeNames
	 * @param DOMElement $fromNode
	 * @param DOMElement $toNode
	 */
	protected function copyAttributes($attributeNames, $fromNode, $toNode)
	{
		foreach ($attributeNames as $attributeName)
		{
			if ($fromNode->hasAttribute($attributeName))
			{
				$toNode->setAttribute($attributeName, $fromNode->getAttribute($attributeName));
			}
		}
	}
	
	/**
	 * @param string $url
	 * @return string
	 */
	protected function cleanUrl($url)
	{
		if (!preg_match('/^https?:\/\//i', $url))
		{
			return '#';
		}
		return $url;
	}
	
	/**
	 * @param DOMElement $element
	 * @param DOMElement $finalParent
	 * @return DOMElement
	 */
	protected function defaultConvert($element, $finalParent)
	{
		$newNode = $finalParent->ownerDocument->createElement(strtolower($element->localName));
		$this->copyAttributes($this->safeAttributes, $element, $newNode);
		$finalParent->appendChild($newNode);
		return $newNode;
	}
	
	/**
	 * @param DOMElement $element
	 * @param DOMElement $finalParent
	 * @return DOMElement
	 */
	protected function convertA($element, $finalParent)
	{
		$newNode = $this->defaultConvert($element, $finalParent);
		$newNode->setAttribute('href', $this->cleanUrl($element->getAttribute('href')));
		$newNode->setAttribute('class', 'link');
		$this->copyAttributes(array('hreflang', 'type', 'rel', 'media'), $element, $newNode);
		return $newNode;
	}
	
	/**
	 * @param DOMElement $element
	 * @param DOMElement $finalParent
	 * @return DOMElement
	 */
	protected function convertImg($element, $finalParent)
	{
		$newNode = $this->defaultConvert($element, $finalParent);
		$newNode->setAttribute('src', $this->cleanUrl($element->getAttribute('src')));
		$newNode->setAttribute('class', 'image');
		$this->copyAttributes(array('alt', 'width', 'height'), $element, $newNode);
		return $newNode;
	}
	
	/**
	 * @param DOMElement $element
	 * @param DOMElement $finalParent
	 * @return DOMElement
	 */
	protected function convertBlockquote($element, $finalParent)
	{
		$newNode = $this->defaultConvert($element, $finalParent);
		$newNode->setAttribute('cite', $this->cleanUrl($element->getAttribute('cite')));
		return $newNode;
	}
	
	/**
	 * @param DOMElement $element
	 * @param DOMElement $finalParent
	 * @return DOMElement
	 */
	protected function convertQ($element, $finalParent)
	{
		$newNode = $this->defaultConvert($element, $finalParent);
		$newNode->setAttribute('cite', $this->cleanUrl($element->getAttribute('cite')));
		return $newNode;
	}
	
	/**
	 * @param DOMElement $element
	 * @param DOMElement $finalParent
	 * @return DOMElement
	 */
	protected function convertDel($element, $finalParent)
	{
		$newNode = $this->defaultConvert($element, $finalParent);
		$newNode->setAttribute('cite', $this->cleanUrl($element->getAttribute('cite')));
		$this->copyAttributes(array('datetime'), $element, $newNode);
		return $newNode;
	}
	
	/**
	 * @param DOMElement $element
	 * @param DOMElement $finalParent
	 * @return DOMElement
	 */
	protected function convertIns($element, $finalParent)
	{
		$newNode = $this->defaultConvert($element, $finalParent);
		$newNode->setAttribute('cite', $this->cleanUrl($element->getAttribute('cite')));
		$this->copyAttributes(array('datetime'), $element, $newNode);
		return $newNode;
	}
	
	/**
	 * @param DOMElement $element
	 * @param DOMElement $finalParent
	 * @return DOMElement
	 */
	protected function convertUl($element, $finalParent)
	{
		$newNode = $this->defaultConvert($element, $finalParent);
		$newNode->setAttribute('class', 'normal');
		return $newNode;
	}
	
	/**
	 * @param DOMElement $element
	 * @param DOMElement $finalParent
	 * @return DOMElement
	 */
	protected function convertOl($element, $finalParent)
	{
		$newNode = $this->defaultConvert($element, $finalParent);
		$newNode->setAttribute('class', 'normal');
		return $newNode;
	}
	
	/**
	 * @param DOMElement $element
	 * @param DOMElement $finalParent
	 * @return DOMElement
	 */
	protected function convertTable($element, $finalParent)
	{
		$newNode = $this->defaultConvert($element, $finalParent);
		$newNode->setAttribute('class', 'normal');
		return $newNode;
	}
	
	/**
	 * @param DOMElement $element
	 * @param DOMElement $finalParent
	 * @return DOMElement
	 */
	protected function convertTh($element, $finalParent)
	{
		$newNode = $this->defaultConvert($element, $finalParent);
		$this->copyAttributes(array('colspan', 'rowspan'), $element, $newNode);
		return $newNode;
	}
	
	/**
	 * @param DOMElement $element
	 * @param DOMElement $finalParent
	 * @return DOMElement
	 */
	protected function convertTd($element, $finalParent)
	{
		$newNode = $this->defaultConvert($element, $finalParent);
		$this->copyAttributes(array('colspan', 'rowspan'), $element, $newNode);
		return $newNode;
	}
	
	/**
	 * @param DOMElement $element
	 * @param DOMElement $finalParent
	 * @return DOMElement
	 */
	protected function convertP($element, $finalParent)
	{
		$newNode = $this->defaultConvert($element, $finalParent);
		$newNode->setAttribute('class', 'normal');
		return $newNode;
	}
	
	/**
	 * @param DOMElement $element
	 * @param DOMElement $finalParent
	 * @return DOMElement
	 */
	protected function convertH1($element, $finalParent)
	{
		$newNode = $this->defaultConvert($element, $finalParent);
		$newNode->setAttribute('class', 'h-1');
		return $newNode;
	}
	
	/**
	 * @param DOMElement $element
	 * @param DOMElement $finalParent
	 * @return DOMElement
	 */
	protected function convertH2($element, $finalParent)
	{
		$newNode = $this->defaultConvert($element, $finalParent);
		$newNode->setAttribute('class', 'h-2');
		return $newNode;
	}
	
	/**
	 * @param DOMElement $element
	 * @param DOMElement $finalParent
	 * @return DOMElement
	 */
	protected function convertH3($element, $finalParent)
	{
		$newNode = $this->defaultConvert($element, $finalParent);
		$newNode->setAttribute('class', 'h-3');
		return $newNode;
	}
	
	/**
	 * @param DOMElement $element
	 * @param DOMElement $finalParent
	 * @return DOMElement
	 */
	protected function convertH4($element, $finalParent)
	{
		$newNode = $this->defaultConvert($element, $finalParent);
		$newNode->setAttribute('class', 'h-4');
		return $newNode;
	}
	
	/**
	 * @param DOMElement $element
	 * @param DOMElement $finalParent
	 * @return DOMElement
	 */
	protected function convertH5($element, $finalParent)
	{
		$newNode = $this->defaultConvert($element, $finalParent);
		$newNode->setAttribute('class', 'h-5');
		return $newNode;
	}
	
	/**
	 * @param DOMElement $element
	 * @param DOMElement $finalParent
	 * @return DOMElement
	 */
	protected function convertH6($element, $finalParent)
	{
		$newNode = $this->defaultConvert($element, $finalParent);
		$newNode->setAttribute('class', 'h-6');
		return $newNode;
	}
	
	/**
	 * @param DOMElement $element
	 * @param DOMElement $finalParent
	 * @return DOMElement
	 */
	protected function convertProgress($element, $finalParent)
	{
		$newNode = $this->defaultConvert($element, $finalParent);
		$this->copyAttributes(array('max', 'value'), $element, $newNode);
		return $newNode;
	}
	
	/**
	 * @param DOMElement $element
	 * @param DOMElement $finalParent
	 * @return DOMElement
	 */
	protected function convertTime($element, $finalParent)
	{
		$newNode = $this->defaultConvert($element, $finalParent);
		$this->copyAttributes(array('datetime', 'pubdate'), $element, $newNode);
		return $newNode;
	}
	
	/**
	 * @param DOMElement $element
	 * @param DOMElement $finalParent
	 * @return DOMElement
	 */
	protected function convertAudio($element, $finalParent)
	{
		$newNode = $this->defaultConvert($element, $finalParent);
		$newNode->setAttribute('preload', 'none');
		$this->copyAttributes(array('controls', 'loop', 'src'), $element, $newNode);
		return $newNode;
	}
	
	/**
	 * @param DOMElement $element
	 * @param DOMElement $finalParent
	 * @return DOMElement
	 */
	protected function convertVideo($element, $finalParent)
	{
		$newNode = $this->defaultConvert($element, $finalParent);
		$newNode->setAttribute('preload', 'none');
		$this->copyAttributes(array('controls', 'height', 'loop', 'poster', 'src', 'width'), $element, $newNode);
		return $newNode;
	}
	
	/**
	 * @param DOMElement $element
	 * @param DOMElement $finalParent
	 * @return DOMElement
	 */
	protected function convertSource($element, $finalParent)
	{
		$newNode = $this->defaultConvert($element, $finalParent);
		$newNode->setAttribute('src', $this->cleanUrl($element->getAttribute('src')));
		$this->copyAttributes(array('media', 'type'), $element, $newNode);
		return $newNode;
	}
	
	/**
	 * @param DOMElement $element
	 * @param DOMElement $finalParent
	 * @return DOMElement
	 */
	protected function convertTrack($element, $finalParent)
	{
		$newNode = $this->defaultConvert($element, $finalParent);
		$newNode->setAttribute('src', $this->cleanUrl($element->getAttribute('src')));
		$this->copyAttributes(array('default', 'kind', 'label', 'srclang'), $element, $newNode);
		return $newNode;
	}
	
	/**
	 * @param DOMElement $element
	 * @param DOMElement $finalParent
	 * @return DOMElement
	 */
	protected function convertMeter($element, $finalParent)
	{
		$newNode = $this->defaultConvert($element, $finalParent);
		$this->copyAttributes(array('high', 'low', 'max', 'min', 'optimum', 'value'), $element, $newNode);
		return $newNode;
	}
	
	/**
	 * @param DOMElement $element
	 * @param DOMElement $finalParent
	 * @return DOMElement
	 */
	protected function convertDiv($element, $finalParent)
	{
		return $finalParent;
	}
}