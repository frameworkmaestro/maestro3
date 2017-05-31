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

class MTableRaw extends MSimpleTable {

    public $array;
    public $colTitle;
    public $zebra = false;
    public $table;

    public function __construct($title = '', $array, $colTitle = null, $name = '', $zebra = true) {
        parent::__construct($name);
        $this->setText($title);
        $this->array = $array;
        $this->colTitle = $colTitle;
        $this->setClass("mTableRaw");
        $this->zebra = $zebra;
    }

    public function setData($data) {
        $this->array = $data;
    }

    public function setAlternate($zebra = false) {
        $this->zebra = $zebra;
    }

    public function generate() {
        $array = $this->array;
        $colTitle = $this->colTitle;
        $t = $this;
        $k = 0;
        if ($this->text) {
            $ncols = count($array[0]);
            $t->setCell($k++, 0, $this->text, " class=\"mTableRawTitle\" colspan=$ncols ");
        }
        if (is_array($colTitle)) {
            $n = count($colTitle);
            for ($i = 0; $i < $n; $i++)
                $t->setCell($k, $i, $colTitle[$i], " class=\"mTableRawColumnTitle\" ");
            $k++;
        }
        if (is_array($array)) {
            $nrows = count($array);
            for ($i = 0; $i < $nrows; $i++) {
                $rowClass = "mTableRawRow" . ($this->zebra ? ($i % 2) : '');
                $t->setRowClass($k, $rowClass);
                if (is_array($array[$i])) {
                    $ncols = count($array[$i]);
                    for ($j = 0; $j < $ncols; $j++) {
                        $attr = $this->attr['cell'][$k][$j];
                        if ($attr == '')
                            $attr = "width=0 align=\"left\" valign=\"top\"";
                        $t->setCell($k, $j, $array[$i][$j], $attr);
                    }
                }
                else {
                    $attr = $this->attr['cell'][$k][0];
                    if ($attr == '')
                        $attr = "width=0 align=\"left\" valign=\"top\"";
                    $t->setCell($k, 0, $array[$i], $attr);
                }
                $k++;
            }
        }
        return parent::generate();
    }

}

?>