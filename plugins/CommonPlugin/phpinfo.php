<?php
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
 *  This page displays phpinfo
 *
 */
function CommonPlugin_phpinfo($html)
{
    if (!extension_loaded('xsl')) {
        throw new Exception('The xsl extension must be installed to display phpinfo');
    }

    $xml = new DOMDocument;
    $xml->loadHTML($html);
    $xsl = new DOMDocument;
    $xsl->loadXML(<<<END
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="html" indent="yes" encoding="UTF-8"/>

<!-- identity transformation -->
    <xsl:template match="@*|node()">
        <xsl:copy>
            <xsl:apply-templates select="@*|node()"/>
        </xsl:copy>
    </xsl:template>

<!-- process only style and body elements -->
    <xsl:template match="/">
        <xsl:text>&#x0A;</xsl:text>
        <xsl:apply-templates select="html/head/style"/>
        <xsl:text>&#x0A;</xsl:text>
        <xsl:apply-templates select="html/body/node()"/>
    </xsl:template>

</xsl:stylesheet>
END
    );

    $proc = new XSLTProcessor;
    $proc->importStyleSheet($xsl);
    return $proc->transformToXML($xml);
}

$level = error_reporting(E_ALL | E_STRICT);

set_error_handler('CommonPlugin_Exception::errorHandler', E_ALL | E_STRICT);
ob_start();
try {
    phpinfo();
    echo CommonPlugin_phpinfo(ob_get_clean());
} catch (Exception $e) {
    echo ob_get_clean();
    echo $e->getMessage();
}
restore_error_handler();
error_reporting($level);
