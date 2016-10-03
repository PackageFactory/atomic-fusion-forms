<?php
namespace PackageFactory\AtomicFusion\Forms\Fusion\Helpers;

/**
 * This file is part of the PackageFactory.AtomicFusion.Forms package
 *
 * (c) 2016 Wilhelm Behncke <wilhelm.behncke@googlemail.com>
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\TypoScript\TypoScriptObjects\AbstractTypoScriptObject;
use PackageFactory\AtomicFusion\Forms\Fusion\SpreadImplementation;

class Spread implements \Iterator, \ArrayAccess
{
	/**
	 * @var array
	 */
	protected $collection;

	/**
	 * @var array
	 */
	protected $keys;

	/**
	 * @var integer
	 */
	private $offset = 0;

	public function __construct(SpreadImplementation $spreadObject)
	{
		$collection = $spreadObject->getValueAtPath('collection');
		$itemName = $spreadObject->getValueAtPath('itemName');

		$result = [];
		foreach ($collection as $key => $item) {
			$context = [$itemName => $item];
			if ($spreadObject->checkPath('keyRenderer')) {
				$renderedKey = $spreadObject->getValueAtPath('keyRenderer', $context);
			} else {
				$renderedKey = $key;
			}
			$result[$renderedKey] = $spreadObject->getValueAtPath('itemRenderer', $context);
		}

		$this->collection = $result;
		$this->keys = array_keys($this->collection);
	}

	/**
	 * Get the collection
	 *
	 * @return array
	 */
	public function getCollection()
	{
		return $this->collection;
	}

	//
	// Iterator methods
	//

	function rewind()
	{
        $this->offset = 0;
    }

    function current()
	{
        return $this->collection[$this->keys[$this->offset]];
    }

    function key()
	{
        return $this->keys[$this->offset];
    }

    function next()
	{
        ++$this->offset;
    }

    function valid()
	{
        return isset($this->keys[$this->offset]);
    }

	//
	// ArrayAccess methods
	//

	public function offsetSet($offset, $value)
	{
        //
        // Spread is immutable
        //
    }

    public function offsetExists($offset)
	{
        return isset($this->collection[$offset]);
    }

    public function offsetUnset($offset)
	{
        unset($this->collection[$offset]);
    }

    public function offsetGet($offset)
	{
        return isset($this->collection[$offset]) ? $this->collection[$offset] : null;
    }

	//
	// Spread can be interpreted as string
	//

	public function __toString()
	{
		$collection = $this->spread();
		return implode('', $collection);
	}
}
