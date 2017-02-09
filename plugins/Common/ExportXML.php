<?php

namespace phpList\plugin\Common;

use XMLWriter;

/**
 * CommonPlugin for phplist
 * 
 * This file is a part of CommonPlugin.
 *
 * @category  phplist
 * @package   CommonPlugin
 * @author    Duncan Cameron
 * @copyright 2011-2017 Duncan Cameron
 * @license   http://www.gnu.org/licenses/gpl.html GNU General Public License, Version 3
 */

/**
 * This class manages the export in XML format
 * 
 */
class ExportXML
{
    public function __construct()
    {
    }

    public function export(IExportable $exporter)
    {
        $fileName = $exporter->exportFileName();
    
        ob_end_clean();
        Header('Content-type: text/xml');
        Header("Content-disposition:  attachment; filename={$fileName}.xml");

        $oXMLout = new XMLWriter();
        $oXMLout->openMemory();
        $oXMLout->setIndent(true);
        $oXMLout->startDocument();
        $oXMLout->startElement('root');

        $fields = preg_replace(
            array('/\s/', '/\W/'),
            array('_', ''),
            $exporter->exportFieldNames()
        );

        foreach ($exporter->exportRows() as $row) {
            $oXMLout->startElement('row');

            foreach ($exporter->exportValues($row) as $i => $value) {
                $oXMLout->writeElement($fields[$i], $value);
            }
            $oXMLout->endElement();
        }
        $oXMLout->endElement();
        $oXMLout->endDocument();
        print $oXMLout->outputMemory();
    }
}
