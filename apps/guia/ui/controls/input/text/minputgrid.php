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

class MInputGrid extends MInputControl {

    public $cols; // array of column definitions
    public $numRows; // the number of rows
    public $numCols; // the number of cols
    public $aValue; // array com os valores dos campos
    public $aMask; // array com as máscaras dos campos

    public function init($name, $label = '', $rows = 0) {
        $this->numRows = $rows;
        $this->numCols = 0;

        parent::init($name, array(), $label);
    }

    public function getRowCount() {
        return $this->numRows;
    }

    public function setRowCount($rows) {
        $this->numRows = $rows;
    }

    public function addColumn($label, $name, $colWidth = 0, $value = '', $mask = '') {
        for ($i = 0; $i < $this->numRows; $i++) {
            $this->setFieldValue($i + 1, $this->numCols + 1, $value);
            $this->setFieldMask($i + 1, $this->numCols + 1, $mask);
        }

        $this->cols[$this->numCols++] = new MInputGridColumn($label, $name, $colWidth, $value);
    }

    public function generateInner() {
        $id = $this->getId();
        $mv = new MVContainer();
        for ($i = 0; $i <= $this->numRows; $i++) {
            $t = array();
            $t[0] = new MDiv('',new MSpan('', ($i + 1) . ":&nbsp;", 'mCaption'));
            $t[0]->width = '25px';
            for ($j = 0; $j <= $this->numCols; $j++) {
                $text = new MTextField("{$id}[$i][$j]", $this->aValue[$i][$j], '', $colWidth);
                if ($this->aMask[$i][$j] != '') {
                    $text->addMask($this->aMask[$i][$j]);
                }
                $text->setAttribute('rowNumber', "$i");
                $t[$j + 1] = $text;
            }
            $mh = new MHContainer('',$t);
            $mv->addControl($mh);
        }
        $this->inner = $mv;
    }

    public function setValue($value) {
        if (is_array($value) && ( count($this->cols) > 0 )) {
            foreach ($this->cols as $col => $column) {
                for ($i = 0; $i < $this->numRows; $i++) {
                    if ($value[$i][$col] != NULL) {
                        $this->setFieldValue($i + 1, $col + 1, $value[$i][$col]);
                    }
                }
            }
        }
    }

    public function getValue() {
        return $this->aValue;
    }

    public function getFieldValue($row, $col, $default = '') {
        $value = $this->aValue[$row - 1][$col - 1];

        return isset($value) ? $value : $default;
    }

    public function setFieldValue($row, $col, $value) {
        $this->aValue[$row - 1][$col - 1] = $value;
    }

    public function setFieldMask($row, $col, $mask) {
        $this->aMask[$row - 1][$col - 1] = $mask;
    }

    public function getRow($row) {
        return $this->aValue[$row - 1];
    }

}



?>