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
 * @version   SVN: $Id: ExportCSV.php 717 2012-03-30 19:33:23Z Duncan $
 * @link      http://forums.phplist.com/viewtopic.php?f=7&t=35427
 */

/**
 * This class manages the export in CSV format
 * 
 */
class CommonPlugin_ExportCSV
{
	public function __construct()
	{
	}

	public function export (CommonPlugin_IExportable $exporter) 
	{
		$fileName = $exporter->exportFileName();
	
		ob_end_clean();
		Header('Content-type: text/csv');
		Header("Content-disposition:  attachment; filename={$fileName}.csv");
		$out = fopen('php://output', 'w');
		fputcsv($out, $exporter->exportFieldNames());
		$rows = $exporter->exportRows();

		foreach ($rows as $row) {
			fputcsv($out, $exporter->exportValues($row));
		}
		
		fclose($out);
	}
}
