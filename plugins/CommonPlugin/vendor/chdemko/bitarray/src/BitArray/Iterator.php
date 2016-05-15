<?php

/**
 * chdemko\BitArray\Iterator class
 *
 * @author     Christophe Demko <chdemko@gmail.com>
 * @copyright  Copyright (C) 2014 Christophe Demko. All rights reserved.
 *
 * @license    http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html The CeCILL B license
 *
 * This file is part of the php-bitarray package https://github.com/chdemko/php-bitarray
 */

// Declare chdemko\BitArray namespace
namespace chdemko\BitArray;

/**
 * Iterator
 *
 * @package  BitArray
 *
 * @since    1.0.0
 */
class Iterator implements \Iterator
{
	/**
	 * @var     integer  Index
	 *
	 * @since   1.0.0
	 */
	private $index;

	/**
	 * @var     BitArray  bits
	 *
	 * @since   1.0.0
	 */
	private $bits;

	/**
	 * Constructor
	 *
	 * @param   BitArray  $bits  BitArray
	 *
	 * @since   1.0.0
	 */
	public function __construct(BitArray $bits)
	{
		$this->bits = $bits;
		$this->rewind();
	}

	/**
	 * Rewind the Iterator to the first element
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function rewind()
	{
		$this->index = 0;
	}

	/**
	 * Return the current key
	 *
	 * @return  mixed  The current key
	 *
	 * @since   1.0.0
	 */
	public function key()
	{
		return $this->index;
	}

	/**
	 * Return the current value
	 *
	 * @return  mixed  The current value
	 *
	 * @since   1.0.0
	 */
	public function current()
	{
		return $this->bits[$this->index];
	}

	/**
	 * Move forward to the next element
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function next()
	{
		$this->index++;
	}

	/**
	 * Checks if current position is valid
	 *
	 * @return  boolean
	 *
	 * @since   1.0.0
	 */
	public function valid()
	{
		return $this->index < count($this->bits);
	}
}
