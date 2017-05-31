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

define('PN_PAGE', 'pn_page');

/**
 * Navigation control for grids
 */
class MGridNavigator extends MControl {

    public $pageLength;
    public $pageNumber;
    public $action;
    public $range;
    public $rowCount;
    public $gridCount;
    public $pageCount;
    public $idxFirst;
    public $idxLast;
    public $showPageNo = false;
    public $linktype; // hyperlink or linkbutton
    public $grid;
    private $goPage;
    private $jsId;

    public function __construct($length = 20, // Number of records per page
            $total = 0, // Number total of records
            $action = '?', // Action URL
            $grid = NULL) {// The grid which contains this component )
        parent::__construct();
        $this->pageLength = $length;
        $this->gridCount = $length;
        $this->setRowCount($total);
        $this->grid = $grid;
        $this->setPageNumber($this->grid->pageNumber);
        $this->action = $action;
        $this->linktype = 'hyperlink';
    }

    public function setAction($url) {
        $this->action = $url;
    }

    public function setLinkType($linktype) {
        $this->linktype = $linktype;
    }

    public function setRowCount($rowCount) {
        $this->rowCount = $rowCount;
        $this->pageCount = ($this->pageLength > 0) ? (int) (($this->rowCount + $this->pageLength - 1) / $this->pageLength) : 1;
    }

    public function setGridCount($gridCount) {
        $this->gridCount = $gridCount;
    }

    public function setPageNumber($num) {
        $this->pageNumber = (int) ($num ? $num : 1);
        $this->range = new MRange($this->pageNumber, $this->pageLength, $this->rowCount);
        $this->setIndexes();
    }

    public function setCurrentPage($pageNumber) {
        $this->setPageNumber($pageNumber);
    }

    public function setIndexes() {
        $this->range->__construct($this->pageNumber, $this->pageLength, $this->rowCount);
        $this->idxFirst = $this->range->offset;
        $this->idxLast = $this->range->offset + $this->range->rows - 1;
        $this->setGridCount($this->range->rows);
    }

    public function setGridParameters($pageLength, $rowCount, $action, $grid) {
        $this->pageLength = $pageLength;
        $this->setRowCount($rowCount);
        $this->action = $action;
        $this->grid = $grid;
        $this->setIndexes();
    }

    public function getRowCount() {
        return $this->rowCount;
    }

    public function getGridCount() {
        return $this->gridCount;
    }

    public function getPageNumber() {
        return $this->pageNumber;
    }

    public function getPageCount() {
        return $this->pageCount;
    }

    public function getPagePosition($showPage = true) {
        $position = '[' . ($showPage ? _M('Page') : '') . ' ' . $this->getPageNumber() . ' ' . _M('of') . ' '
                . $this->getPageCount() . "]";
        return $position;
    }

    public function getPageLinks($showPage = true, $limit = 10) {
        $pageCount = $this->getPageCount();
        $pageNumber = $this->getPageNumber();
        $pageLinks = array();

        $p = 0;

        if (!$this->getRowCount()) {
            $pageLinks[$p] = new MLabel('&nbsp;&nbsp;&nbsp;');
            $pageLinks[$p++]->setClass('mGridNavigatorText');
        } else {
            if ($showPage) {
                $pageLinks[$p] = new MText('', ''/* &nbsp;' . _M("") . '&nbsp;' */);
                $pageLinks[$p++]->setClass('mGridNavigatorText');
            }

            if ($pageNumber <= $limit) {
                $o = 1;
            } else {
                $o = ((int) (($pageNumber - 1) / $limit)) * $limit;
                //$pageLinks[$p] = new MLinkButton('', '...', "$this->action&" . PN_PAGE . "=" . $o++ . "&gridName=" . urlencode($this->grid->name));
                $pageLinks[$p] = new MLink('', '...');
                $this->goPage[] = array('id' => $pageLinks[$p]->getId(), 'page' => $o);
                //-$pageLinks[$p]->setAction('!' . $this->grid->name . '.goPage(' . $o++ . ');');
                $pageLinks[$p++]->setClass('mGridNavigatorLink');
            }

            for ($i = 0; ($i < $limit) && ($o <= $pageCount); $i++, $o++) {
                $pg = $o;
                if ($o != $pageNumber) {
//                    $pageLinks[$p] = new MLinkButton('', $pg, "$this->action&" . PN_PAGE . "=" . $o . "&gridName=" . urlencode($this->grid->name));
                    $pageLinks[$p] = new MLink('', $pg);
                    $this->goPage[] = array('id' => $pageLinks[$p]->getId(), 'page' => $o);
                    //-$pageLinks[$p]->setAction('!' . $this->grid->name . '.goPage(' . $o . ');');
                    $pageLinks[$p++]->setClass('mGridNavigatorLink');
//                    $pageLinks[$p++]->addEvent('mouseover', "top.status='PÃ¡gina $pg'");
                } else {
                    //$pageLinks[$p] = new MLinkButton('', "$pg", "$this->action&" . PN_PAGE . "=" . $o . "&gridName=" . urlencode($this->grid->name));
                    $pageLinks[$p] = new MLink('', $pg);
                    $this->goPage[] = array('id' => $pageLinks[$p]->getId(), 'page' => $o);
                    //-$pageLinks[$p]->setAction('!' . $this->grid->name . '.goPage(' . $o . ');');
                    $pageLinks[$p++]->setClass('mGridNavigatorSelected');
                }
            }

            if ($o < $pageCount) {
                $pageLinks[$p++] = new MLabel('');
//                $pageLinks[$p] = new MLinkButton('', '...', "$this->action&" . PN_PAGE . "=" . $o . "&gridName=" . urlencode($this->grid->name));
                $pageLinks[$p] = new MLink('', '...');
                $this->goPage[] = array('id' => $pageLinks[$p]->getId(), 'page' => $o);
                //-$pageLinks[$p]->setAction('!' . $this->grid->name . '.goPage(' . $o . ');');
                $pageLinks[$p++]->setClass('mGridNavigatorLink');
            }
        }

        $d = new MDiv('', $pageLinks, 'mGridNavigatorLinks');
        return $d;
    }

    public function getPageRange($subject = '') {
        if (!$this->getRowCount()) {
            $range = 'Nenhum dado';
        } else {
            $first = $this->idxFirst + 1;
            $last = $this->idxLast + 1;
            $range = $first . ' - ' . $last . ' de ' . $this->getRowCount() . $subject;
        }

        return new MDiv('', $range, 'mGridNavigatorRange');
    }

    public function getPageRows($subject = '') {
        $rows = $this->getGridCount() . '&nbsp;' . $subject;
        return $rows;
    }

    public function getPageFirst() {
        $pageNumber = $this->getPageNumber();
        if ($pageNumber > 1) {
            $image = new MLink('', '&nbsp;');
//            $image->setAction('!' . $this->grid->name . '.goPage(1);');
            $this->goPage[] = array('id' => $image->getId(), 'page' => 1);
            $image->setClass('mGridNavigatorImage mGridNavigatorImageFirstOn');
        } else {
            $image = new MDiv('', '&nbsp;');
            $image->setClass('mGridNavigatorImage mGridNavigatorImageFirstOff');
        }
        return $image;
    }

    public function getPagePrev() {
        $pageNumber = $this->getPageNumber();
        $pagePrev = $pageNumber - 1;

        if ($pageNumber > 1) {
            $image = new MLink('', '&nbsp;');
//            $image->setAction('!' . $this->grid->name . '.goPage(' . $pagePrev . ');');
            $this->goPage[] = array('id' => $image->getId(), 'page' => $pagePrev);
            $image->setClass('mGridNavigatorImage mGridNavigatorImagePrevOn');
        } else {
            $image = new MDiv('', '&nbsp;');
            $image->setClass('mGridNavigatorImage mGridNavigatorImagePrevOff');
        }
        return $image;
    }

    public function getPageNext() {
        $pageNumber = $this->getPageNumber();
        $pageNext = $pageNumber + 1;
        $pageCount = $this->getPageCount();

        if ($pageNumber < $pageCount) {
            $image = new MLink('', '&nbsp;');
//            $image->setAction('!' . $this->grid->name . '.goPage(' . $pageNext . ');');
            $this->goPage[] = array('id' => $image->getId(), 'page' => $pageNext);
            $image->setClass('mGridNavigatorImage mGridNavigatorImageNextOn');
        } else {
            $image = new MDiv('', '&nbsp;');
            $image->setClass('mGridNavigatorImage mGridNavigatorImageNextOff');
        }

        return $image;
    }

    public function getPageLast() {
        $pageNumber = $this->getPageNumber();
        $pageCount = $this->getPageCount();

        if ($pageNumber < $pageCount) {
            $image = new MLink('', '&nbsp;');
//            $image->setAction('!' . $this->grid->name . '.goPage(' . $pageCount . ');');
            $this->goPage[] = array('id' => $image->getId(), 'page' => $pageCount);
            $image->setClass('mGridNavigatorImage mGridNavigatorImageLastOn');
        } else {
            $image = new MDiv('', '&nbsp;');
            $image->setClass('mGridNavigatorImage mGridNavigatorImageLastOff');
        }
        return $image;
    }

    public function getPageImages() {
        $array[0] = $this->getPageFirst();
        $array[1] = $this->getPagePrev();
        $array[2] = $this->getPageRange();
        $array[3] = $this->getPageNext();
        $array[4] = $this->getPageLast();
        $d = new MDiv('', $array, 'mGridNavigatorImages');
        return $d;
    }

    public function getPageImagesLinks() {
        $array[0] = $this->getPageFirst();
        $array[1] = $this->getPagePrev();
        $array[2] = $this->getPageLinks();
        $array[3] = $this->getPageNext();
        $array[4] = $this->getPageLast();
        $d = new MDiv('', $array, 'mGridNavigatorImages');
        return $d;
    }

    public function generateJsCode() {
        $this->jsId = str_replace('::', '', $this->grid->getId());
        $jsData = '[';
        if (count($this->goPage)) {

            $firstRowAdded = false;

            foreach ($this->goPage as $i => $row) {

                if ($firstRowAdded)
                    $jsData .= ",";

                $jsData .= "{";
                $firstColumnAdded = false;
                foreach ($row as $j => $column) {
                    if ($firstColumnAdded){
                        $jsData .= ",";
                    }    
                    $jsData .= "{$j}:\"{$column}\"";
                    $firstColumnAdded = true;
                }
                $firstRowAdded = true;
                $jsData .= "}\n";
            }
        }
        $jsData .= "]";
        $jsCode = $this->jsId . ".addGoPage({$jsData});";
        $this->page->addJsCode($jsCode);
    }

    public function generate() {
        $this->page->addDojoRequire("dojo/store/Memory");        
        $this->goPage = array();
        $element[] = $this->getPageImagesLinks();
        $element[] = $this->getPageRange();
        $this->generateJsCode();
        $d = new MDiv('', $element, 'mGridNavigation');
        $code = "function(e){var page = {$this->jsId}.getGoPage().get(e.target.id).page; {$this->jsId}.goPage(page);}";        
        $d->addEvent("click","!" . $code);
        return $d;
    }
           

}

?>
