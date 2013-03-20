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
 * @version   SVN: $Id: GoogleChart.php 710 2012-03-28 15:59:31Z Duncan $
 * @link      http://forums.phplist.com/viewtopic.php?f=7&t=35427
 */

/**
 * This class manages the creation of a Google chart
 */
class CommonPlugin_ProxySettingsException extends CommonPlugin_Exception
{
	/*
	 *	Public methods
	 */
	public function __construct()
	{
		parent::__construct('http_proxy_options_error');
	}
}

class CommonPlugin_GoogleChartException extends CommonPlugin_Exception
{
	/*
	 *	Public methods
	 */
	public function __construct()
	{
		parent::__construct('chart_error');
	}
}

class CommonPlugin_GoogleChart
{
	const SESSION_KEY = 'ImageCache';
	/*
	 *	Public methods
	 */
	public function __construct()
	{
		$this->logger = CommonPlugin_Logger::instance();
	}

	public function url(CommonPlugin_IChartable $charter)
	{
		$url = 'http://chart.googleapis.com/chart?'
			. http_build_query(
				$charter->chartParameters() + array('chid' => md5(uniqid(rand(), true))), '', '&'
			);
		return $url;
	}

	public function createChart(CommonPlugin_IChartable $charter)
	{
		global $http_proxy_options;
		global $http_response_header;

		$params = $charter->chartParameters();
		$id = md5(serialize($params));
		$imageCache = isset($_SESSION[self::SESSION_KEY]) ? $_SESSION[self::SESSION_KEY] : array();

		if (!isset($imageCache[$id])) {
			$url = 'http://chart.googleapis.com/chart?';
			try {
				$context = stream_context_create(
					array(
						'http' => array(
							'method' => 'POST',
							'header' => "Content-type: application/x-www-form-urlencoded\r\n",
							'content' => http_build_query(
								$params + array('chid' => md5(uniqid(rand(), true))), '', '&'
							)
						)
					)
				);

				if (isset($http_proxy_options)) {
					if (!stream_context_set_option($context, array('http' => $http_proxy_options)))
						throw new CommonPlugin_ProxySettingsException();
				}
				$image = file_get_contents($url, false, $context);
			} catch (ErrorException $e) {
				$this->logger->logWarn($e->getMessage());

				if (isset($http_response_header))
					$this->logger->logDebug(print_r($http_response_header, true));
				throw new CommonPlugin_GoogleChartException();
			}

			if ($image === false) {
				if (isset($http_response_header))
					$this->logger->logDebug(print_r($http_response_header, true));
				throw new CommonPlugin_GoogleChartException();
			}
			$imageCache[$id] = $image;
			$_SESSION[self::SESSION_KEY] = $imageCache;
		}
		return $id;
	}

	public function sendChart($id)
	{
		ob_end_clean();
		header('Content-type: image/png');
		echo $_SESSION[self::SESSION_KEY][$id];
	}
}
