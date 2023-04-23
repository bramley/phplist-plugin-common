<?php

/**
 * chdemko\BitArray\BitArray class
 *
 * @author     Christophe Demko <chdemko@gmail.com>
 * @copyright  Copyright (C) 2012-2018 Christophe Demko. All rights reserved.
 *
 * @license    BSD 3-Clause License
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
 * @property-read  integer  $count  The number of bits set to true
 * @property-read  integer  $size   The number of bits
 *
 * @since    1.0.0
 */
class BitArray implements \ArrayAccess, \Countable, \IteratorAggregate, \JsonSerializable
{
	/**
	 * @var  BitArray  Empty bit array
	 */
	private static $empty;

	/**
	 * @var  integer[]  Number of bits for each value between 0 and 255
	 */
	private static $count = array(
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
	);

	/**
	 * @var  integer[]  Mask for restricting complements
	 */
	private static $restrict = array(255, 1, 3, 7, 15, 31, 63, 127);

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
	 * @param   integer  $size     The BitArray size
	 * @param   boolean  $default  The default value for bits
	 *
	 * @since   1.0.0
	 */
	protected function __construct($size = 0, $default = false)
	{
		$this->size = (int) $size;

		if ($default)
		{
			$this->data = str_repeat(chr(255), ceil($this->size / 8));
			$this->restrict();
		}
		else
		{
			$this->data = str_repeat(chr(0), ceil($this->size / 8));
		}
	}

	/**
	 * Remove useless bits for simplifying count operation.
	 *
	 * @return  BitArray  $this for chaining
	 *
	 * @since   1.2.0
	 */
	protected function restrict()
	{
		$length = strlen($this->data);

		if ($length > 0)
		{
			$this->data[$length - 1] = chr(ord($this->data[$length - 1]) & self::$restrict[$this->size % 8]);
		}

		return $this;
	}

	/**
	 * Clone a BitArray
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
			case 'count':
				return $this->count();
			default:
				throw new \RuntimeException('Undefined property');
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

		for ($index = 0, $length = strlen($this->data); $index < $length; $index++)
		{
			$count += self::$count[ord($this->data[$index])];
		}

		return $count;
	}

	/**
	 * Transform the object to an array
	 *
	 * @return  array  Array of values
	 *
	 * @since   1.1.0
	 */
	public function toArray()
	{
		$array = array();

		for ($index = 0; $index < $this->size; $index++)
		{
			$array[] = (bool) (ord($this->data[(int) ($index / 8)]) & (1 << $index % 8));
		}

		return $array;
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
		return $this->toArray();
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
	 * Copy bits directly from a BitArray
	 *
	 * @param   BitArray  $bits    A BitArray to copy
	 * @param   int       $index   Starting index for destination
	 * @param   int       $offset  Starting index for copying
	 * @param   int       $size    Copy size
	 *
	 * @return  BitArray  This object for chaining
	 *
	 * @throw   \OutOfRangeException  Argument index must be an positive integer lesser than the size
	 *
	 * @since   1.1.0
	 */
	public function directCopy(BitArray $bits, $index = 0, $offset = 0, $size = 0)
	{
		if ($offset > $index)
		{
			for ($i = 0; $i < $size; $i++)
			{
				$this[$i + $index] = $bits[$i + $offset];
			}
		}
		else
		{
			for ($i = $size - 1; $i >= 0; $i--)
			{
				$this[$i + $index] = $bits[$i + $offset];
			}
		}

		return $this;
	}

	/**
	 * Copy bits from a BitArray
	 *
	 * @param   BitArray  $bits    A BitArray to copy
	 * @param   int       $index   Starting index for destination.
	 *                             If index is non-negative, the index parameter is used as it is, keeping its real value between 0 and size-1.
	 *                             If index is negative, the index parameter starts from the end, keeping its real value between 0 and size-1.
	 * @param   int       $offset  Starting index for copying.
	 *                             If offset is non-negative, the offset parameter is used as it is, keeping its positive value between 0 and size-1.
	 *                             If offset is negative, the offset parameter starts from the end, keeping its real value between 0 and size-1.
	 * @param   mixed     $size    Copy size.
	 *                             If size is given and is positive, then the copy will copy size elements.
	 *                             If the bits argument is shorter than the size, then only the available elements will be copied.
	 *                             If size is given and is negative then the copy starts from the end.
	 *                             If it is omitted, then the copy will have everything from offset up until the end of the bits argument.
	 *
	 * @return  BitArray  This object for chaining
	 *
	 * @since   1.1.0
	 */
	public function copy(BitArray $bits, $index = 0, $offset = 0, $size = null)
	{
		$index = $this->getRealOffset($index);
		$offset = $bits->getRealOffset($offset);
		$size = $bits->getRealSize($offset, $size);

		if ($size > $this->size - $index)
		{
			$size = $this->size - $index;
		}

		return $this->directCopy($bits, $index, $offset, $size);
	}

	/**
	 * Get the real offset using a positive or negative offset
	 *
	 * @param   int  $offset  If offset is non-negative, the offset parameter is used as it is, keeping its real value between 0 and size-1.
	 *                        If offset is negative, the offset parameter starts from the end, keeping its real value between 0 and size-1.
	 *
	 * @return  integer  The real offset
	 *
	 * @since   1.1.0
	 */
	protected function getRealOffset($offset)
	{
		$offset = (int) $offset;

		if ($offset < 0)
		{
			// Start from the end
			$offset = $this->size + $offset;

			if ($offset < 0)
			{
				$offset = 0;
			}
		}
		elseif ($offset > $this->size)
		{
			$offset = $this->size;
		}

		return $offset;
	}

	/**
	 * Get the real offset using a positive or negative offset
	 *
	 * @param   int    $offset  The real offset.
	 * @param   mixed  $size    If size is given and is positive, then the real size will be between 0 and the current size-1.
	 *                          If size is given and is negative then the real size starts from the end.
	 *                          If it is omitted, then the size goes until the end of the BitArray.
	 *
	 * @return  integer  The real size
	 *
	 * @since   1.1.0
	 */
	protected function getRealSize($offset, $size)
	{
		if ($size === null)
		{
			$size = $this->size - $offset;
		}
		else
		{
			$size = (int) $size;

			if ($size < 0)
			{
				$size = $this->size + $size - $offset;

				if ($size < 0)
				{
					$size = 0;
				}
			}
			elseif ($size > $this->size - $offset)
			{
				$size = $this->size - $offset;
			}
		}

		return $size;
	}

	/**
	 * Create a new BitArray from an integer
	 *
	 * @param   integer  $size     Size of the BitArray
	 * @param   boolean  $default  The default value for bits
	 *
	 * @return  BitArray  A new BitArray
	 *
	 * @since   1.0.0
	 */
	public static function fromInteger($size, $default = false)
	{
		return new BitArray($size, (bool) $default);
	}

	/**
	 * Create a new BitArray from a sequence of bits.
	 *
	 * @param   integer  $size    Size of the BitArray
	 * @param   integer  $values  The values for the bits
	 *
	 * @return  BitArray  A new BitArray
	 *
	 * @since   1.2.0
	 */
	public static function fromDecimal($size, $values = 0)
	{
		$size = min((int) $size, PHP_INT_SIZE);
		$values <<= PHP_INT_SIZE - $size;
		$bits = new BitArray($size);

		for ($i = 0; $i < PHP_INT_SIZE; $i++)
		{
			$bits->data[$i] = chr(($values & (0xff << (PHP_INT_SIZE - 8))) >> (PHP_INT_SIZE - 8));
			$values <<= 8;
		}

		return $bits;
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
	 * Create a new BitArray using a slice
	 *
	 * @param   BitArray  $bits    A BitArray to get the slice
	 * @param   int       $offset  If offset is non-negative, the slice will start at that offset in the bits argument.
	 *                             If offset is negative, the slice will start from the end of the bits argument.
	 * @param   mixed     $size    If size is given and is positive, then the slice will have up to that many elements in it.
	 *                             If the bits argument is shorter than the size, then only the available elements will be present.
	 *                             If size is given and is negative then the slice will stop that many elements from the end of the bits argument.
	 *                             If it is omitted, then the slice will have everything from offset up until the end of the bits argument.
	 *
	 * @return  BitArray  A new BitArray
	 *
	 * @since   1.1.0
	 */
	public static function fromSlice(BitArray $bits, $offset = 0, $size = null)
	{
		$offset = $bits->getRealOffset($offset);
		$size = $bits->getRealSize($offset, $size);
		$slice = new BitArray($size);

		return $slice->directCopy($bits, 0, $offset, $size);
	}

	/**
	 * Create a new BitArray using the concat operation
	 *
	 * @param   BitArray  $bits1  A BitArray
	 * @param   BitArray  $bits2  A BitArray
	 *
	 * @return  BitArray  A new BitArray
	 *
	 * @since   1.1.0
	 */
	public static function fromConcat(BitArray $bits1, BitArray $bits2)
	{
		$size = $bits1->size + $bits2->size;
		$concat = new BitArray($size);
		$concat->directCopy($bits1, 0, 0, $bits1->size);
		$concat->directCopy($bits2, $bits1->size, 0, $bits2->size);

		return $concat;
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

		for ($index = 0; $index < $length; $index++)
		{
			$this->data[$index] = chr(~ ord($this->data[$index]));
		}

		return $this->restrict();
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

			for ($index = 0; $index < $length; $index++)
			{
				$this->data[$index] = chr(ord($this->data[$index]) | ord($bits->data[$index]));
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

			for ($index = 0; $index < $length; $index++)
			{
				$this->data[$index] = chr(ord($this->data[$index]) & ord($bits->data[$index]));
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

			for ($index = 0; $index < $length; $index++)
			{
				$this->data[$index] = chr(ord($this->data[$index]) ^ ord($bits->data[$index]));
			}

			return $this;
		}
		else
		{
			throw new \InvalidArgumentException('Argument must be of equal size');
		}
	}

	/**
	 * Shift a bit array.
	 *
	 * @param   int       $size   Size to shift.
	 *                            Negative value means the shifting is done right to left while
	 *                            positive value means the shifting is done left to right.
	 * @param   boolean   $value  Value to shift
	 *
	 * @return  BitArray  $this for chaining
	 *
	 * @since   1.2.0
	 */
	public function shift($size = 1, $value = false)
	{
		$size = (int) $size;

		if ($size > 0)
		{
			$min = min($this->size, $size);

			for ($i = $this->size - 1; $i >= $min; $i--)
			{
				$this[$i] = $this[$i - $min];
			}

			for ($i = 0; $i < $min; $i++)
			{
				$this[$i] = $value;
			}
		}
		else
		{
			$min = min($this->size, -$size);

			for ($i = 0; $i < $this->size - $min; $i++)
			{
				$this[$i] = $this[$i + $min];
			}

			for ($i = $this->size - $min; $i < $this->size; $i++)
			{
				$this[$i] = $value;
			}
		}

		return $this;
	}
}
