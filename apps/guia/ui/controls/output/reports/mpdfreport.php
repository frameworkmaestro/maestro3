<?php
/* Copyright [2011, 2012, 2013] da Universidade Federal de Juiz de Fora
 * Este arquivo é parte do programa Framework Maestro.
 * O Framework Maestro é um software livre; você pode redistribuí-lo e/ou 
 * modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada 
 * pela Fundação do Software Livre (FSF); na versão 2 da Licença.
 * Este programa é distribuído na esperança que possa ser  útil, 
 * mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer
 * MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL 
 * em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título
 * "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software
 * Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a 
 * Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA
 * 02110-1301, USA.
 */

class MPDFReportColumn extends MGridColumn {
    public $name;

    public function __construct($name, $title='', $align='left', $nowrap=false, $width=0, $visible=true, $options=null, $order=false, $filter=false) {
        parent::__construct($title, $align, $nowrap, $width, $visible, $options, $order, $filter);
        $this->name = $name;
    }

    public function generate() {
    }

}

class MPDFReportControl extends MGridControl {
    public $name;

    public function __construct($name, $control, $title='', $align='left', $nowrap=false, $width=0, $visible=true) {
        parent::__construct($control, $title, $align, $nowrap, $width, $visible);
        $this->name = $name;
    }

    public function generate() {
    }

}

class MPDFReport extends MGrid {

    public $options;
    public $rawdata;
    public $slot;
    public $ezpdf;  // ezPdfReport object
    public $pdf;    // MCezPdf object
    public $y;

    public function __construct($name, $data, $columns, $pageLength=1, $index=0, $orientation = 'portrait', $paper='a4') {
        $this->setPDF(new MezPDFReport('2', $orientation, $paper));
        parent::__construct($name, $data, $columns, '', $pageLength, $index);
        $this->slot = array();
        $this->rawdata = NULL;
        $this->initializeOptions();
        $this->setWidth(100);
    }

    public function initializeOptions() {
        $this->options['showLines'] = 0;
        $this->options['showHeadings'] = 1;
        $this->options['showTableTitle'] = 1;
        $this->options['shaded'] = 1;
        $this->options['shadeCol'] = array(0.8, 0.8, 0.8);
        $this->options['shadeCol2'] = array(0.7, 0.7, 0.7);
        $this->options['fontSize'] = 10;
        $this->options['textCol'] = array(0, 0, 0);
        $this->options['titleFontSize'] = 14;
        $this->options['rowGap'] = 2;
        $this->options['colGap'] = 5;
        $this->options['lineCol'] = array(0, 0, 0);
        $this->options['xPos'] = 'center';
        $this->options['xOrientation'] = 'center';
        $this->options['width'] = 0;
        $this->options['maxWidth'] = 596;
        $this->options['minRowSpace'] = -100;
        $this->options['innerLineThickness'] = 1;
        $this->options['outerLineThickness'] = 1;
        $this->options['protectRows'] = 1;
    }

    public function setOption($option, $value) {
        $this->options[$option] = $value;
    }

    public function setPDF($pdf) {
        $this->ezpdf = $pdf;
        $this->pdf = $pdf->pdf;
    }

    public function getPDF() {
        return $this->ezpdf;
    }

    public function setWidth($width) {
        $width = $this->pdf->getWidthFromPercent($width);
        $this->setOption('width', $width);
    }

    public function addColumn($column) {
        $this->columns[$column->name] = $column;
        $this->columns[$column->name]->width = $this->pdf->getWidthFromPercent($column->width);
        $this->columns[$column->name]->index = count($this->columns);
    }

    public function setColumns($columns) {
        $this->columns = NULL;
        if ($columns != NULL) {
            if (!is_array($columns)) {
                $columns = array($columns);
            }    
            foreach ($columns as $k => $c) {
                $this->columns[$c->name] = $c;
                $this->columns[$c->name]->width = $this->pdf->getWidthFromPercent($c->width);
                $this->columns[$c->name]->index = $k;
            }
        }
    }

    public function getPage() {
        if (count($this->rawdata)) {
            return array_slice($this->rawdata, $this->pn->idxFirst, $this->pn->gridCount);
        }
    }

    public function generateReportHeader() {
        return NULL;
    }

    public function generatePageHeader() {
        return NULL;
    }

    public function generatePageFooter() {
        return NULL;
    }

    public function generateHeader() {
        $header[] = $this->generateReportHeader();
        $header[] = $this->generatePageHeader();
        return $header;
    }

    public function generateColumnsHeading() {
        $tbl = array();
        $p = 0;
        // generate column headings
        foreach ($this->columns as $k => $col) {
            if ((!$col->visible ) || (!$col->title ))
                continue;
            $colTitle = $col->title;
            $tbl["{$col->name}"] = $colTitle;
        }
        return $tbl;
    }

    public function generateColumns(&$tbl, $row, $i) {
        $cntRow = count($row);
        foreach ($this->columns as $k => $col) {
            if ((!$col->title ) || (!$col->visible )) {
                continue;
            }    
            if ($col instanceof mpdfreportcolumn) {
                $control = $col->baseControl ? new $col->baseControl : '';  // clonning
                $value = is_object($row[$col->index -1]) ? '' : $row[$col->index -1];
                $col->value = $value;
                if ($col->options) {
                    $value = $col->options[$value];
                    if ($this->showid) {
                        $value .= " ($row[$k])";
                    }
                }
                // by default, we align numbers to the right and text to the left
                $c = substr($value, 0, 1);
                if (!$col->align && ( $c == '-' || ( $c >= '0' && $c <= '9' ) )) {
                    $col->align = 'right';
                }
                if ($col->href != '') {
                    $href = $col->href;
                    for ($r = 0; $r < $cntRow; $r++)
                        $href = str_replace("#$r#", trim($row[$r]), $href);
                    $href = str_replace('#?#', $value, $href);
                    $control->href = $href;
                    $control->action = $href;
                    $control->label = $value;
                }
                $control->value = $value;
            } elseif ($col instanceof mpdfreportcontrol) {
                $control = clone $col->baseControl;  // clonning
                $control->generate();
            } else {
                throw new MRuntimeError("ERROR: Unknown column class '{$col->className}'!");
            }
            $tbl[$i][$col->name] = $control;
        }
    }

    public function generateEmptyMsg() {
        $tbl = new MSimpleTable('');
        $tbl->attributes['table'] = "cellspacing=\"0\" cellpadding=\"2\" border=\"0\" class=\"gridAttention\" align=\"center\" width=\"100%\"";
        $tbl->attributes['row'][0] = "class=\"gridAttention\" align=\"center\"";
        $tbl->cell[0][0] = new Text('', $this->emptyMsg);
        $tbl->cell[0][0]->setClass('gridAttention');
        return $tbl;
    }

    public function generateTableData() {
        if ($this->hasErrors()) {
            $this->generateErrors();
        }
        $tblData = array();
        if ($this->data) {
            // generate data rows
            foreach ($this->data as $i => $row) {
                if (isset($this->rowmethod)) {
                    call_user_func($this->rowmethod, &$row, &$this->columns, &$this->slot, $this);
                }
                $this->generateColumns($tblData, $row, $i);
            } // foreach row
        } // if
        foreach ($tblData as $r => $row)
            foreach ($row as $c => $cell)
                $data[$r][$c] = $cell->value;
        return $data;
    }

    public function generatePageTitle() {
        $this->pdf->ezText($this->title, $this->options['titleFontSize'], array('justification' => 'center'));
        $this->pdf->ezSetDy($this->pdf->getFontDecender($this->options['titleFontSize']));
    }

    public function generateBody($data) {
        $titles = $this->generateColumnsHeading();
        $cols = array();
        foreach ($this->columns as $k => $col) {
            if ((!$col->visible ) || (!$col->title )) {
                continue;
            }    
            $cols[$col->name] = array('justification' => $col->align, 'width' => $col->width);
        }
        $this->options['cols'] = $cols;
        if ($this->options['showTableTitle']){
            $title = $this->title;
        } else {
            $this->generatePageTitle();
            $title = '';
        }
        $this->y = $this->pdf->ezTable($data, $titles, $title, $this->options);
    }

    public function generateFooter() {
        if (!$this->data) {
            $footer[] = $this->generateEmptyMsg();
        }    
        $footer[] = $this->generatePageFooter();
        $footer[] = $this->generateReportFooter();
        return $footer;
    }

    public function generateReport() {
        $this->pdf->ezSetMargins(30, 30, 30, 30);
        $this->rawdata = $this->generateTableData();
        if ($this->pageLength) {
            $this->navigator = new MGridNavigator($this->pageLength, $this->rowCount, '');
        }
        else
            $this->navigator = null;
        for ($page = 1; $page <= $this->navigator->pageCount; $page++) {
            $this->navigator->setPageNumber($page);
            $this->generatePageHeader();
            $this->generateBody($this->getPage());
            $this->generatePageFooter();
            if ($page != $this->navigator->pageCount) {
                $this->pdf->ezNewPage();
            }    
        }
    }

    public function pageBreak() {
        if (!$this->break) {
            $this->pdf->ezNewPage();
            $this->break = true;
        }
    }

    public function clearPageBreak() {
        $this->break = false;
    }

    public function setTrigger($trigger, $class, $module, $param) {
        $this->pdf->setTrigger($trigger, $class, $module, $param);
    }

    public function setOutput($value='') {
        $this->ezpdf->setOutput();
    }

    public function execute() {
        return $this->ezpdf->execute();
    }

    public function generate() {
        $this->generateReport();
        $this->setOutput();
        $this->execute();
    }
}

?>