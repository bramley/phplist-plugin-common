<?php
/**
 * CommonPlugin for phplist.
 *
 * This file is a part of CommonPlugin.
 *
 * @category  phplist
 *
 * @author    Duncan Cameron
 * @copyright 2011-2021 Duncan Cameron
 * @license   http://www.gnu.org/licenses/gpl.html GNU General Public License, Version 3
 */

namespace phpList\plugin\Common;

class FPDF extends \tFPDF
{
    private $fontCacheDir;

    public function __construct()
    {
        global $tmpdir;

        $this->fontCacheDir = "$tmpdir/unifont/";

        if (!is_dir($this->fontCacheDir)) {
            mkdir($this->fontCacheDir, 0755);
        }

        parent::__construct();
        $this->AddFontUnicode('DejaVu', '', 'DejaVuSansCondensed.ttf');
        $this->AddFontUnicode('DejaVu', 'B', 'DejaVuSansCondensed-Bold.ttf');
        $this->SetFont('DejaVu');
        $this->SetFontSize(9);
        $this->SetLeftMargin(20);
        $this->SetRightMargin(20);
    }

    public function Header()
    {
        global $plugins;

        $imagePath = getConfig('common_pdf_logo_path') ?: $plugins['CommonPlugin']->coderoot . 'images/logo.png';
        $this->Image($imagePath, 20);
        $this->SetY($this->GetY() + 5);
        $pageWidth = $this->GetPageWidth();
        $this->Line($this->lMargin, $this->GetY(), $pageWidth - $this->rMargin, $this->GetY());
        $this->SetY($this->GetY() + 5);
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetX(-15);
        $this->SetFont('', '', 8);
        $this->Cell(0, 10, strftime('%c'), 0, 0, 'R');
    }

    /**
     * Extracted from tFPDF to allow a separate directory for the generated files.
     *
     * @param string $family
     * @param string $style
     * @param string $file
     */
    public function AddFontUnicode($family, $style, $file)
    {
        // Add a TrueType, OpenType or Type1 font
        $family = strtolower($family);
        $style = strtoupper($style);

        if ($style == 'IB') {
            $style = 'BI';
        }
        $fontkey = $family.$style;

        if (isset($this->fonts[$fontkey])) {
            return;
        }

        if (defined('_SYSTEM_TTFONTS') && file_exists(_SYSTEM_TTFONTS.$file)) {
            $ttffilename = _SYSTEM_TTFONTS . $file;
        } else {
            $ttffilename = $this->fontpath . 'unifont/' . $file;
        }
        $unifilename = $this->fontCacheDir . strtolower(strstr($file, '.', true));
        $name = '';
        $originalsize = 0;
        $ttfstat = stat($ttffilename);

        if (file_exists($unifilename . '.mtx.php')) {
            include $unifilename . '.mtx.php';
        }

        if (!isset($type) || !isset($name) || $originalsize != $ttfstat['size']) {
            $ttffile = $ttffilename;
            $ttf = new \TTFontFile();
            $ttf->getMetrics($ttffile);
            $cw = $ttf->charWidths;
            $name = preg_replace('/[ ()]/', '', $ttf->fullName);

            $desc = [
                'Ascent' => round($ttf->ascent),
                'Descent' => round($ttf->descent),
                'CapHeight' => round($ttf->capHeight),
                'Flags' => $ttf->flags,
                'FontBBox' => '['.round($ttf->bbox[0]).' '.round($ttf->bbox[1]).' '.round($ttf->bbox[2]).' '.round($ttf->bbox[3]).']',
                'ItalicAngle' => $ttf->italicAngle,
                'StemV' => round($ttf->stemV),
                'MissingWidth' => round($ttf->defaultWidth),
            ];
            $up = round($ttf->underlinePosition);
            $ut = round($ttf->underlineThickness);
            $originalsize = $ttfstat['size'] + 0;
            $type = 'TTF';
            // Generate metrics .php file
            $descExport = var_export($desc, true);
            $s = <<<END
<?php
\$name = '$name';
\$type = '$type';
\$desc = $descExport;
\$up = $up;
\$ut = $ut;
\$ttffile = '$ttffile';
\$originalsize = $originalsize;
\$fontkey = '$fontkey';
END;
            if (is_writable(dirname($unifilename))) {
                $fh = fopen($unifilename . '.mtx.php', 'w');
                fwrite($fh, $s, strlen($s));
                fclose($fh);
                $fh = fopen($unifilename . '.cw.dat', 'wb');
                fwrite($fh, $cw, strlen($cw));
                fclose($fh);
                @unlink($unifilename . '.cw127.php');
            }
            unset($ttf);
        } else {
            $cw = @file_get_contents($unifilename .'.cw.dat');
        }
        $i = count($this->fonts) + 1;

        if (!empty($this->AliasNbPages)) {
            $sbarr = range(0, 57);
        } else {
            $sbarr = range(0, 32);
        }
        $this->fonts[$fontkey] = array('i' => $i, 'type' => $type, 'name' => $name, 'desc' => $desc, 'up' => $up, 'ut' => $ut, 'cw' => $cw, 'ttffile' => $ttffile, 'fontkey' => $fontkey, 'subset' => $sbarr, 'unifilename' => $unifilename);
        $this->FontFiles[$fontkey] = array('length1' => $originalsize, 'type' => 'TTF', 'ttffile' => $ttffile);
        $this->FontFiles[$file] = array('type' => 'TTF');
        unset($cw);
    }
}
