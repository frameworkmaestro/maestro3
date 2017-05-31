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

class MGrid extends MBaseGrid {

    public function generateTable() {
        $width = $this->scrollable ? (($this->scrollWidth - 25) . 'px') : $this->width;
        $table = new MSimpleTable($this->id, array("width" => $width, "class" => "mGrid"));
        $p = 0;
        $last = count($this->columns) - 1;
        foreach ($this->columns as $k => $column) {
            if ($column instanceof MGridAction) {
                if ($column->type == 'select') {
                    $rowCount = count($this->data);
                    $this->page->onLoad("{$this->name}.idSelect = '{$this->idSelect}';");
                    $this->page->onLoad("{$this->name}.firstIndex = {$this->firstIndex};");
                    $check = new MCheckBox($this->name . "chkAll", 'chkAction', '');
                    $check->addEvent('click', $this->name . ".checkAll({$rowCount});");
                    $table->setHead($p, $check);
                    $table->setHeadClass($p++, 'select');
                } elseif ($column->type == 'icon') {
                    $table->setHead($p, new MDiv('', '', 'managerIcon'));
                    $table->setHeadClass($p++, 'action');
                } elseif ($column->type == 'text') {
                    $table->setHead($p++, new MDiv('', '', ''));
                }
            } elseif ($column instanceof MGridColumn) {
                if (!$column->visible) {
                    continue;
                }
                if ($column->order) {
                    $this->orderBy = $k;
                    $link = new MLinkButton('', $column->title, $this->getURL($this->filtered, true));
                    $link->setClass('order');
                    $colTitle = $link;
                    $table->setHeadClass($p, 'order');
                } else {
                    $colTitle = $column->title;
                }
                if (($column->width)) {
                    $attr = ($k != $last) ? " width=\"$column->width\"" : ''; //" width=\"100%\"";
                }
                $table->setHead($p++, $colTitle);
            }
        }
        if ($this->data) {
            // generate data rows
            $i = 0;
            $firstRow = true;
            foreach ($this->data as $row) { // foreach row
                $this->currentRow = $i;
                $this->currentIndex = $this->firstIndex + $i;
                $rowId = ($i % 2);
                $rowClass = $this->alternateColors ? "row{$rowId}" : "row";
                if ($this->dnd) {
                    $rowClass .= '  dojoDndItem';
                }
                if ($this->lookupName != '') {
                    $rowClass .= '  rowLookup';
                }
                $c = $this->hasDetail ? $i + $this->currentRow : $i;
                $i++;
                $table->setRowAttribute($c, 'id', $this->name ."-row-" . $this->currentIndex);
                $table->setRowClass($c, $rowClass);
                $this->generateColumnsControls($this->currentRow, $row);
                $this->callRowMethod();
                // generate Columns
                $row = $this->currentRow;
                $p = 0;
                foreach ($this->columns as $k => $column) {
                    $control = $column->control[$row];
                    if ($column instanceof MGridAction) {
                        if ($column->type == 'select') {
                            $table->setCellClass($row, $p, 'select');
                        } elseif ($column->type == 'icon') {
                            $table->setCellClass($row, $p, 'actionIcon');
                        }
                        $this->onCellProcessing($control, $row, $p);
                        $table->setCell($row, $p++, $control);
                    } elseif ($column instanceof MGridColumn) {
                        if (!$column->visible) {
                            continue;
                        }
                        if (($column->width) && $firstRow) {
                            $attr = " width=\"$column->width\"";
                            $table->setCellAttribute($row, $p, $attr);
                        }
                        if ($column->nowrap) {
                            $table->setCellAttribute($row, $p, "nowrap");
                        }
                        if (trim($column->align)) {
                            $table->setCellAttribute($row, $p, "style='text-align:$column->align'");
                        }
                        $class = $column->getClass();
                        if ($class) {
                            $table->setCellClass($row, $p, $class);
                        }
                        $this->onCellProcessing($control, $row, $p);
                        $table->setCell($row, $p++, $control);
                    }
                }
                $firstRow = false;
                // end generate columns
            } // end foreach row
        }// end if
        return $table;
    }

    public function onCellProcessing(MControl $control, $row, $column) {

    }

    public function generateBody() {
        if ($this->hasErrors()) {
            $this->generateErrors();
        }

        if ($this->scrollHeight != '') {
            if ($this->pageLength != 0) {
                throw new EControlException("PageLength must be 0 to scrollable grid.");
            }
            $this->scrollable = true;
            $this->scrollWidth = str_replace('px', '', $this->width);
        }

        if (count($this->columns) == 0) {
            $this->createColumns();
        }
        $body = $this->generateTable();
        return $body;
    }

}

?>