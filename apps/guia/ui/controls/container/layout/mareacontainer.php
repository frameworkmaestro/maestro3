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

class MAreaContainer extends MControl {

    public $top = array();
    public $left = array();
    public $center = array();
    public $right = array();
    public $bottom = array();

    public function __construct($name = null) {
        parent::__construct($name);
    }

    #+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    # Adds an element at the specified position
    #---------------------------------------------------------------------

    public function addElement($element, $where = 'center') {

        if ($where == 'top') {
            $this->top[] = $element;
        } else if ($where == 'left') {
            $this->left[] = $element;
        } else if ($where == 'center') {
            $this->center[] = $element;
        } else if ($where == 'right') {
            $this->right[] = $element;
        } else if ($where == 'bottom') {
            $this->bottom[] = $element;
        } else {
            Manager::error(_M("Container: Illegal positioning '$where' parameter!"));
        }
    }

    #+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    # Obtains the number of table rows
    #---------------------------------------------------------------------

    public function getRowCount() {
        $num_rows = 0;

        if ($this->top) {
            $num_rows++;
        }

        if ($this->left || $this->center || $this->right) {
            $num_rows++;
        }

        if ($this->bottom) {
            $num_rows++;
        }

        return $num_rows;
    }

    #+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    # Obtains the number of table columns
    #---------------------------------------------------------------------

    public function getColumnCount() {
        $num_cols = 0;

        if ($this->left) {
            $num_cols++;
        }

        if ($this->center) {
            $num_cols++;
        }

        if ($this->right) {
            $num_cols++;
        }

        if (!$num_cols) {
            if ($this->top) {
                $num_cols++;
            } else if ($this->bottom) {
                $num_cols++;
            }
        }

        return $num_cols;
    }

    #++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    # Generate HTML
    #----------------------------------------------------------------------

    public function generateInner() {
        $num_rows = $this->getRowCount();
        $num_cols = $this->getColumnCount();

        if ($num_rows && $num_cols) {
            $table = new MSimpleTable($obj->name);
            $table->setAttribute('width', '100%');
            $table->setCell(0, 0, $this->top, "align=\"center\" colspan=$num_cols");
            $table->setCell(1, 0, $this->left, "align=\"center\" valign=\"top\"");
            $table->setCell(1, 1, $this->center, "align=\"center\" valign=\"top\" width=\"100%\"");
            $table->setCell(1, 2, $this->right, "align=\"center\" valign=\"top\"");
            $table->setCell(2, 0, $this->bottom, "align=\"center\" colspan=$num_cols");
            $this->inner = $table;
        }
    }

}

?>