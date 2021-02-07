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

class FPDF extends \FPDF
{
    public function __construct()
    {
        parent::__construct();
        $this->SetFont('Arial');
        $this->SetFontSize(9);
        $this->SetLeftMargin(20);
        $this->SetRightMargin(20);
    }

    public function Header()
    {
        $this->Image(getConfig('common_pdf_logo_path'), 20);
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
}
