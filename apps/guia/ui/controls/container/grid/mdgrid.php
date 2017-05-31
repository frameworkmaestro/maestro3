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

class MDGrid extends MBaseGrid {
    /*
     * A MBaseGrid object
     */

    protected $grid;
    protected $dcolumns;
    protected $value;
    protected $cssCode;
    public $type;
    public $actionData;

    public function onCreate() {
        parent::onCreate();
        $this->dcolumns = array();
        $this->value = array();
        $this->cssCode = '';
    }

    public function onAfterCreate() {
        parent::onAfterCreate();
    }

    public function generateColumns() {
        $p = 0;
        $last = count($this->columns) - 1;
        foreach ($this->columns as $k => $column) {
            $this->dcolumns[$p] = new StdClass();
            $this->dcolumns[$p]->id = $p;
            $this->dcolumns[$p]->field = $column->field ?: 'field' . $p;
            if ($column instanceof MGridAction) {
                if ($column->type == 'select') {
                    $rowCount = count($this->data);
                    $control = new MCheckBox($this->id . "chkAll", 'chkAction', '');
                    $control->addEvent('click', $this->id . ".checkAll({$rowCount});", false);
                    $this->dcolumns[$p]->className = 'select';
                } elseif ($column->type == 'icon') {
                    $control = new MDiv('', '', 'managerIcon');
                    $this->dcolumns[$p]->className = 'actionIcon';
                } elseif ($column->type == 'text') {
                    $control = new MDiv('', '', '');
                }
                $this->dcolumns[$p]->sortable = false;
            } elseif ($column instanceof MGridColumn) {
                if (!$column->visible) {
                    continue;
                }
                $control = new MDiv('', $column->title, '');
                $class = $column->getClass();
                if ($class) {
                    $this->dcolumns[$p]->className = $class;
                }
                $this->dcolumns[$p]->sortable = !($column instanceof MGridControl);
            }
            $this->dcolumns[$p++]->label = $control->generate();
        }
    }

    public function generateValue() {
        $p = 0;
        foreach ($this->columns as $k => $column) {
            $style = '';
            if ($column instanceof MGridColumn) {
                if (!$column->visible) {
                    continue;
                }
                if ($column->width) {
                    $style .= 'width: ' . $column->width . ';';
                }
                if (trim($column->align)) {
                    $style .= 'text-align: ' . $column->align . ';';
                }
            }
            $this->cssCode .= '.dgrid-column-' . $p++ . '{ ' . $style . '}';
        }
        if ($this->data) {
            $i = 0;
            foreach ($this->data as $row) { // foreach row
                $this->currentRow = $i;
                $this->currentIndex = $this->firstIndex + $i;
                $i++;
                $this->generateColumnsControls($this->currentRow, $row);
                $this->callRowMethod();
                // generate Columns
                $row = $this->currentRow;
                $p = 0;
                foreach ($this->columns as $k => $column) {
                    $control = $column->control[$row];
                    if ($column instanceof MGridColumn) {
                        if (!$column->visible) {
                            continue;
                        }
                    }
                    $this->value[$row][$p++] = $control;
                }
            }
        }
    }

    public function generate() {
        $this->page->addDojoRequire('manager.DGrid');

        $this->generateColumns();
        
        $columns = json_encode($this->dcolumns);

        $this->page->onLoad("{$this->id} = new Manager.DGrid('{$this->id}',{$this->firstIndex},'{$this->type}');");
        $this->page->onLoad("{$this->id}.idSelect = '{$this->idSelect}';");
        $this->page->onLoad("{$this->id}.firstIndex = {$this->firstIndex};");
        $value = array();
        foreach ($this->value as $i => $row) {
            foreach ($row as $j => $col) {
                $value[$i][$j] = $col->generate();
            }
        }
        $data = json_encode($value);
        $this->page->onLoad("{$this->id}.columns = {$columns};");
        $this->page->onLoad("{$this->id}.actionData = '{$this->actionData}';");
        $this->page->onLoad("{$this->id}.startup();");
        $div = new MContentpane($this->id);
        $div->setClass('mGrid');
        $div->setWidth($this->width);
        $this->scrollHeight = '20em';
        if ($this->scrollHeight) {
            $div->setHeight($this->scrollHeight);
        } else { // estilos para height:auto
            $this->cssCode .= <<<HERE
                #{$this->id} {
                    height: auto;
		}
		#{$this->id} .dgrid-scroller {
                    position: relative;
                    overflow-y: hidden;
		}
		.has-ie-6 #{$this->id} .dgrid-scroller {
                    /* IE6 doesn't react properly to hidden on this page for some reason */
                    overflow-y: visible;
		}
		#{$this->id} .dgrid-header-scroll {
                    display: none;
		}
		#{$this->id} .dgrid-header {
                    right: 0;
		}
HERE;
        }

        Manager::getPage()->addStyleSheetCode($this->cssCode);
        return $div->generate();
    }

}

?>
