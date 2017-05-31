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

class MCriteriaGrid extends MObjectGrid {

    public $totalRecords;
    public $criteria;

    public function init($name = '', $criteria, $columns, $href, $pagelength = 15, $index = 0) {
        parent::init($name, NULL, NULL, $href, $pagelength, $index);
        if (!is_null($criteria)) {
            $this->criteria = $criteria;
            $this->setColumns($columns);
        }
    }

    public function onAfterCreate() {
        parent::onAfterCreate();
        $db = $this->criteria->getClassMap()->getDB();
        $sql = $this->criteria->getSqlStatement(false);
        $countSQL = clone $sql;
        $countSQL->columns = array('count(*) as CNT');
        $queryCNT = $db->getQuery($countSQL);
        $this->totalRecords = $queryCNT->fields('CNT');

        $range = new MRange($this->pageNumber, $this->pageLength, $this->totalRecords);
        $sql->setRange($range);
        $query = $db->getQuery($sql);
        $cursor = new Cursor($query, $this->criteria->getClassMap(), false, $this->criteria->getManager());

        $this->objArray = $cursor->getObjects();
        $this->rowCount = count($this->objArray);
    }

    public function generateData() {
        global $page, $state;

        if ($this->objArray == NULL)
            return;

        foreach ($this->objArray as $i => $row) {
            foreach ($this->columns as $k => $col) {
                if (strpos(strtolower(get_class($col)), 'mobject')) {

                    $this->data[$i][$k] = $v;
                    // if $col->attribute is a Association and retrieveAutomatic = false
                    // it is necessary to get the value explicity
                    if (!$v) {
                        $attribute = str_replace('->', '.', $col->attribute);
                        $v = $row->getValue($attribute);
                    }
                    $this->data[$i][$k] = $v;
                }
            }
        }

        $this->orderby = $page->request('orderby');

        if ($this->ordered = isset($this->orderby)) {
            $this->applyOrder($this->orderby);
            $state->set('orderby', $this->orderby, $this->name);
        }
        if ($this->pageLength) {
            //$this->->setGridParameters($this->pageLength, $this->totalRecords, $this->getURL($this->filtered, $this->ordered), $this);
            $this->navigator = new MGridNavigator($this->pageLength, $this->rowCount, $this->getURL(), $this);
            $this->navigator->setGridParameters($this->pageLength, $this->rowCount, $this->getURL(), $this);
            $this->data = $this->getDataPage();
        } else {
            $this->pn = null;
        }
    }

}

?>