<?php

namespace phpList\plugin\Common;

use XMLWriter;

/**
 * CommonPlugin for phplist.
 *
 * This file is a part of CommonPlugin.
 *
 * @category  phplist
 *
 * @author    Duncan Cameron
 * @copyright 2011-2018 Duncan Cameron
 * @license   http://www.gnu.org/licenses/gpl.html GNU General Public License, Version 3
 */

/**
 * This class manages the export in XML format.
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
        header('Content-Type: text/xml');
        header(sprintf('Content-Disposition: attachment; filename="%s.xml"', $fileName));

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
        echo $oXMLout->outputMemory();
    }
}
