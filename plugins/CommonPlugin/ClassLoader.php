<?php
/**
 * CommonPlugin for phplist
 * 
 * This file is a part of CommonPlugin.
 *
 * @category  phplist
 * @package   CommonPlugin
 * @author    Duncan Cameron
 * @copyright 2011-2012 Duncan Cameron
 * @license   http://www.gnu.org/licenses/gpl.html GNU General Public License, Version 3
 * @version   SVN: $Id: ClassLoader.php 1014 2012-08-20 15:23:25Z Duncan $
 * @link      http://forums.phplist.com/viewtopic.php?f=7&t=35427
 */


/**
 * This class provides a class loader function that loads classes using base paths and the class name
 * 
 */

class CommonPlugin_ClassLoader
{
	private $basePaths;

	public function register()
	{
		spl_autoload_register(array($this, 'load'));
	}

	public function addBasePath($basePath)
	{
		$this->basePaths[] = $basePath;
	}

	public function load($class)
	{
		$normalisedClass = str_replace('_', '/', $class) . '.php';

		foreach ($this->basePaths as $base) {
			if (file_exists($file = $base . '/' . $normalisedClass)) {
				require $file;
				return;
			}
		}
	}
}
