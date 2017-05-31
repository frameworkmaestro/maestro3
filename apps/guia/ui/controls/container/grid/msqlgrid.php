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

class MSQLGrid extends MDataGrid {

    public $totalRecords;
    public $database;
    public $sql;

    public function __construct($database, $sql, $columns, $href, $pageLength, $index, $name, $useSelecteds = true) {
        parent::__construct(NULL, NULL, $href, $pageLength, $index, $name, $useSelecteds);
        if ($database) {
            $this->database = $database;
            $this->sql = $sql;
            $this->setColumns($columns);
            $this->onCreate();
        }
    }

    public function onCreate() {
        parent::onCreate();
        $this->db = $this->manager->getDatabase($this->database);
        $countSQL = clone $this->sql;
        $countSQL->columns = array('count(*) as CNT');
        $queryCNT = $this->db->getQuery($countSQL);
        $this->totalRecords = $queryCNT->fields('CNT');

        $range = new MRange($this->pageNumber, $this->pageLength, $this->totalRecords);
        $this->sql->setRange($range);
        $this->query = $this->db->getQuery($this->sql);
    }

    public function generateData() {
        global $state;

        $this->data = $this->query->result;

        $this->orderby = $this->page->request('orderby');

        if ($this->ordered = isset($this->orderby)) {
            $this->query->setOrder($this->orderby);
            $state->set('orderby', $this->orderby, $this->name);
        }

        if ($this->getFiltered()) {
            $this->applyFilter();
        }

        $this->rowCount = $this->query->getRowCount();

        if ($this->pageLength) {
            $this->pn->setGridParameters($this->pageLength, $this->totalRecords, $this->getURL($this->filtered, $this->ordered), $this);
            $this->query->setpageLength($this->pageLength);
        } else {
            $this->pn = null;
        }
    }

}

?>