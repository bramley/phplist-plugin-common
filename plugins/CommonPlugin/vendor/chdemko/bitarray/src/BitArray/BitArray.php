<?php

/**
 * chdemko\BitArray\BitArray class
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
 * Array of bits
 *
 * @package  BitArray
 * 
 * @since    1.0.0
 */
class BitArray implements \ArrayAccess, \Countable, \IteratorAggregate, \JsonSerializable
{
	/**
	 * @var  integer[]  Number of bits for each value between 0 and 255
	 */
	private static $count = [
		0, 1, 1, 2, 1, 2, 2, 3, 1, 2, 2, 3, 2, 3, 3, 4,
		1, 2, 2, 3, 2, 3, 3, 4, 2, 3, 3, 4, 3, 4, 4, 5,
		1, 2, 2, 3, 2, 3, 3, 4, 2, 3, 3, 4, 3, 4, 4, 5,
		2, 3, 3, 4, 3, 4, 4, 5, 3, 4, 4, 5, 4, 5, 5, 6,
		1, 2, 2, 3, 2, 3, 3, 4, 2, 3, 3, 4, 3, 4, 4, 5,
		2, 3, 3, 4, 3, 4, 4, 5, 3, 4, 4, 5, 4, 5, 5, 6,
		2, 3, 3, 4, 3, 4, 4, 5, 3, 4, 4, 5, 4, 5, 5, 6,
		3, 4, 4, 5, 4, 5, 5, 6, 4, 5, 5, 6, 5, 6, 6, 7,
		1, 2, 2, 3, 2, 3, 3, 4, 2, 3, 3, 4, 3, 4, 4, 5,
		2, 3, 3, 4, 3, 4, 4, 5, 3, 4, 4, 5, 4, 5, 5, 6,
		2, 3, 3, 4, 3, 4, 4, 5, 3, 4, 4, 5, 4, 5, 5, 6,
		3, 4, 4, 5, 4, 5, 5, 6, 4, 5, 5, 6, 5, 6, 6, 7,
		2, 3, 3, 4, 3, 4, 4, 5, 3, 4, 4, 5, 4, 5, 5, 6,
		3, 4, 4, 5, 4, 5, 5, 6, 4, 5, 5, 6, 5, 6, 6, 7,
		3, 4, 4, 5, 4, 5, 5, 6, 4, 5, 5, 6, 5, 6, 6, 7,
		4, 5, 5, 6, 5, 6, 6, 7, 5, 6, 6, 7, 6, 7, 7, 8
	];

	/**
	 * @var     string  Underlying data
	 *
	 * @since   1.0.0
	 */
	private $data;

	/**
	 * @var     integer  Size of the bit array
	 *
	 * @since   1.0.0
	 */
	private $size;

	/**
	 * Create a new bit array of the given size
	 *
	 * @param   integer  $size  The BitArray size
	 *
	 * @since   1.0.0
	 */
	protected function __construct($size)
	{
		$this->size = (int) $size;
		$this->data = str_repeat("\0", ceil($this->size / 8));
	}

	/**
	 * Clone a bitarray
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function __clone()
	{
		$this->data = str_repeat($this->data, 1);
	}

	/**
	 * Convert the object to a string
	 *
	 * @return  string  String representation of this object
	 *
	 * @since   1.0.0
	 */
	public function __toString()
	{
		$string = str_repeat('0', $this->size);

		for ($offset = 0; $offset < $this->size; $offset++)
		{
			if (ord($this->data[(int) ($offset / 8)]) & (1 << $offset % 8))
			{
				$string[$offset] = '1';
			}
		}

		return $string;
	}

	/**
	 * Magic get method
	 *
	 * @param   string  $property  The property
	 *
	 * @throws  \RuntimeException  If the property does not exist
	 *
	 * @return  mixed  The value associated to the property
	 *
	 * @since   1.0.0
	 */
	public function __get($property)
	{
		switch ($property)
		{
			case 'size':
				return $this->size;
			break;
			case 'count':
				return $this->count();
			break;
			default:
				throw new \RuntimeException('Undefined property');
			break;
		}
	}

	/**
	 * Test the existence of an index
	 *
	 * @param   integer  $offset  The offset
	 *
	 * @return  boolean  The truth value
	 *
	 * @since   1.0.0
	 */
	public function offsetExists($offset)
	{
		return is_int($offset) && $offset >= 0 && $offset < $this->size;
	}

	/**
	 * Get the truth value for an index
	 *
	 * @param   integer  $offset  The offset
	 *
	 * @return  boolean  The truth value
	 *
	 * @throw   \OutOfRangeException  Argument index must be an positive integer lesser than the size
	 *
	 * @since   1.0.0
	 */
	public function offsetGet($offset)
	{
		if ($this->offsetExists($offset))
		{
			return (bool) (ord($this->data[(int) ($offset / 8)]) & (1 << $offset % 8));
		}
		else
		{
			throw new \OutOfRangeException('Argument offset must be a positive integer lesser than the size');
		}
	}

	/**
	 * Set the truth value for an index
	 *
	 * @param   integer  $offset  The offset
	 * @param   boolean  $value   The truth value
	 *
	 * @return  void
	 *
	 * @throw   \OutOfRangeException  Argument index must be an positive integer lesser than the size
	 *
	 * @since   1.0.0
	 */
	public function offsetSet($offset, $value)
	{
		if ($this->offsetExists($offset))
		{
			$index = (int) ($offset / 8);

			if ($value)
			{
				$this->data[$index] = chr(ord($this->data[$index]) | (1 << $offset % 8));
			}
			else
			{
				$this->data[$index] = chr(ord($this->data[$index]) & ~(1 << $offset % 8));
			}
		}
		else
		{
			throw new \OutOfRangeException('Argument index must be a positive integer lesser than the size');
		}
	}

	/**
	 * Unset the existence of an index
	 *
	 * @param   integer  $offset  The index
	 *
	 * @return  void
	 *
	 * @throw   \RuntimeException  Values cannot be unset
	 *
	 * @since   1.0.0
	 */
	public function offsetUnset($offset)
	{
		throw new \RuntimeException('Values cannot be unset');
	}

	/**
	 * Return the number of true bits
	 *
	 * @return  integer  The number of true bits
	 *
	 * @since   1.0.0
	 */
	public function count()
	{
		$count = 0;

		for ($i = 0, $length = strlen($this->data); $i < $length; $i++)
		{
			$count += self::$count[ord($this->data[$i])];
		}

		return $count;
	}

	/**
	 * Serialize the object
	 *
	 * @return  array  Array of values
	 *
	 * @since   1.0.0
	 */
	public function jsonSerialize()
	{
		$array = [];

		for ($offset = 0; $offset < $this->size; $offset++)
		{
			$array[] = (bool) (ord($this->data[(int) ($offset / 8)]) & (1 << $offset % 8));
		}

		return $array;
	}

	/**
	 * Get an iterator
	 *
	 * @return  Iterator  Iterator
	 *
	 * @since   1.0.0
	 */
	public function getIterator()
	{
		return new Iterator($this);
	}

	/**
	 * Return the size
	 *
	 * @return  integer  The size
	 *
	 * @since   1.0.0
	 */
	public function size()
	{
		return $this->size;
	}

	/**
	 * Create a new BitArray from an integer
	 *
	 * @param   integer  $size  Size of the bitarray
	 *
	 * @return  BitArray  A new BitArray
	 *
	 * @since   1.0.0
	 */
	public static function fromInteger($size)
	{
		return new BitArray($size);
	}

	/**
	 * Create a new BitArray from a traversable
	 *
	 * @param   \Traversable  $traversable  A traversable and countable
	 *
	 * @return  BitArray  A new BitArray
	 *
	 * @since   1.0.0
	 */
	public static function fromTraversable($traversable)
	{
		$bits = new BitArray(count($traversable));
		$offset = 0;
		$ord = 0;

		foreach ($traversable as $value)
		{
			if ($value)
			{
				$ord |= 1 << $offset % 8;
			}

			if ($offset % 8 === 7)
			{
				$bits->data[(int) ($offset / 8)] = chr($ord);
				$ord = 0;
			}

			$offset++;
		}

		if ($offset % 8 !== 0)
		{
			$bits->data[(int) ($offset / 8)] = chr($ord);
		}

		return $bits;
	}

	/**
	 * Create a new BitArray from a bit string
	 *
	 * @param   string  $string  A bit string
	 *
	 * @return  BitArray  A new BitArray
	 *
	 * @since   1.0.0
	 */
	public static function fromString($string)
	{
		$bits = new BitArray(strlen($string));
		$ord = 0;

		for ($offset = 0; $offset < $bits->size; $offset++)
		{
			if ($string[$offset] !== '0')
			{
				$ord |= 1 << $offset % 8;
			}

			if ($offset % 8 === 7)
			{
				$bits->data[(int) ($offset / 8)] = chr($ord);
				$ord = 0;
			}
		}

		if ($offset % 8 !== 0)
		{
			$bits->data[(int) ($offset / 8)] = chr($ord);
		}

		return $bits;
	}

	/**
	 * Create a new BitArray from json
	 *
	 * @param   string  $json  A json encoded value
	 *
	 * @return  BitArray  A new BitArray
	 *
	 * @since   1.0.0
	 */
	public static function fromJson($json)
	{
		return self::fromTraversable(json_decode($json));
	}

	/**
	 * Complement the bit array
	 *
	 * @return  BitArray  This object for chaining
	 *
	 * @since   1.0.0
	 */
	public function applyComplement()
	{
		$length = strlen($this->data);

		for ($i = 0; $i < $length; $i++)
		{
			$this->data[$i] = chr(~ ord($this->data[$i]));
		}

		return $this;
	}

	/**
	 * Or with an another bit array
	 *
	 * @param   BitArray  $bits  A bit array
	 *
	 * @return  BitArray  This object for chaining
	 *
	 * @throw   \InvalidArgumentException  Argument must be of equal size
	 *
	 * @since   1.0.0
	 */
	public function applyOr(BitArray $bits)
	{
		if ($this->size == $bits->size)
		{
			$length = strlen($this->data);

			for ($i = 0; $i < $length; $i++)
			{
				$this->data[$i] = chr(ord($this->data[$i]) | ord($bits->data[$i]));
			}

			return $this;
		}
		else
		{
			throw new \InvalidArgumentException('Argument must be of equal size');
		}
	}

	/**
	 * And with an another bit array
	 *
	 * @param   BitArray  $bits  A bit array
	 *
	 * @return  BitArray  This object for chaining
	 *
	 * @throw   \InvalidArgumentException  Argument must be of equal size
	 *
	 * @since   1.0.0
	 */
	public function applyAnd(BitArray $bits)
	{
		if ($this->size == $bits->size)
		{
			$length = strlen($this->data);

			for ($i = 0; $i < $length; $i++)
			{
				$this->data[$i] = chr(ord($this->data[$i]) & ord($bits->data[$i]));
			}

			return $this;
		}
		else
		{
			throw new \InvalidArgumentException('Argument must be of equal size');
		}
	}

	/**
	 * Xor with an another bit array
	 *
	 * @param   BitArray  $bits  A bit array
	 *
	 * @return  BitArray  This object for chaining
	 *
	 * @throw   \InvalidArgumentException  Argument must be of equal size
	 *
	 * @since   1.0.0
	 */
	public function applyXor(BitArray $bits)
	{
		if ($this->size == $bits->size)
		{
			$length = strlen($this->data);

			for ($i = 0; $i < $length; $i++)
			{
				$this->data[$i] = chr(ord($this->data[$i]) ^ ord($bits->data[$i]));
			}

			return $this;
		}
		else
		{
			throw new \InvalidArgumentException('Argument must be of equal size');
		}
	}
}
