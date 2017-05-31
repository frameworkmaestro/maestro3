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

class MDataGridColumn extends MGridColumn {

    public function init($field = '', $title = '', $align = 'left', $nowrap = false, $width = 0, $visible = true, $options = null, $order = false, $filter = false) {
        parent::init($title, $align, $nowrap, $width, $visible, $options, $order, $filter);
        if ($field) {
            $this->setField($field);
        }
    }
}

class MDataGridHyperlink extends MGridHyperlink {

    public function init($field = '', $title = '', $href = '', $width = 0, $visible = true, $options = null, $order = false, $filter = false) {
        parent::init($title, $href, $width, $visible, $options, $order, $filter);
        if ($field) {
            $this->setField($field);
        }
    }
}

class MDataGridControl extends MGridControl {

    public function init($control = '', $field = '', $title = '', $alinhamento = null, $nowrap = false, $width = 0, $visible = true) {
        parent::init($control, $title, $alinhamento, $nowrap, $width, $visible);
        if ($field) {
            $this->setField($field);
        }
    }
}

class MDataGridAction extends MGridAction {

    public function init($type, $alt, $value, $href, $index = null, $enabled = true) {
        parent::init($type, $alt, $value, $href, $enabled, $index);
    }

}

class MDataGrid extends MGrid {

    public $query; // base object query
    public $database; // database conf where execute the query
    public $sql; // sql object
    public $sqlcmd; // sql command text (select ...)
    public $db; // database object where execute the query

    /**
      DataGrid2 constructor
      $query - a query object
      $columns - array of columns objects
      $href - base url of this datagrid
      $pageLength - max number of rows to show (0 to show all)
     */

    public function init($name = '', $query = '', $columns = '', $action = '', $pageLength = 15, $index = 0, $useSelecteds = true) {
        $this->query = $query;
        parent::init($name, NULL, $columns, $action, $pageLength, $index, $useSelecteds);
    }

    public function onCreate() {
        parent::onCreate();
        //if ($this->pageLength) {
        //    $this->navigator = new MGridNavigator($this->pageLength, $this->rowCount, $this->getURL(), $this);
        //}
    }

    public function addColumn($column, $index = '') {
        $column->grid = $this;
        $class = strtolower(get_class($column));
        if (strpos($class, 'mdatagrid') !== false) {
            $this->columns[$column->field] = $column;
        } else {
            parent::addColumn($column);
        }
    }

    public function generateData() {

        if ($this->query instanceof RetrieveCriteria) {
            $this->query = $this->query->asQuery();
        }

        $this->orderBy = mrequest('orderby');
        
        $this->rowCount = $this->query->count();

        if ($this->ordered = isset($this->orderBy)) {
            $this->query->msql->setOrderBy($this->orderBy);
            $this->state->set('orderby', $this->orderBy, $this->name);
        }

        if ($this->pageLength) {
            $this->navigator = new MGridNavigator($this->pageLength, $this->rowCount, $this->getURL(), $this);
            $this->navigator->setGridParameters($this->pageLength, $this->rowCount, $this->getURL(), $this);
            $this->firstIndex = $this->navigator->idxFirst;
            //  $this->data = $this->query->getPage($this->navigator->getPageNumber());
            $range = new MRange($this->pageNumber, $this->pageLength, $this->rowCount);
            ////mdump($range);
            $this->query->setRange($range);
            $this->data = $this->query->getResult();
        } else {
            $this->data = $this->query->getResult();
            $this->navigator = null;
        }
    }

    public function generateColumnName() {
        $this->columnName = array();
        foreach ($this->columns as $column) {
            $class = strtolower(get_class($column));
            if (strpos($class, 'mdatagrid') !== false) {
                $column->index = $this->query->getColumnNumber($column->field);
                $this->columnName[$column->index] = $column->field;
            }
        }
    }
    
    public function generateJsManager(){
        $id = $this->getId();
        $id = str_replace('::','',$id);
        $this->getPage()->addJsCode($id . " = Manager.Grid('{$id}',{$this->pageNumber});");
    }

    public function generateJsData() {
        $id = $this->getId();
        $id = str_replace('::','',$id);
        $columnNames = array();
        foreach ($this->columns as $column) {
            $class = strtolower(get_class($column));
            if (strpos($class, 'mdatagrid') !== false) {
                $column->index = $this->query->getColumnNumber($column->field);
                if (!is_null($column->index)){
                    $columnNames[$column->index] = $column->field;
                }
            }
        }
        //mdump($columnNames);
        $jsCode = $id . ".setData( [\n";
        if (count($this->data)) {

            $firstRowAdded = false;

            foreach ($this->data as $i => $row) {

                if ($firstRowAdded)
                    $jsCode .= ",";

                $jsCode .= "{";
                $firstColumnAdded = false;
                foreach ($row as $j => $column) {
                    // $jsCode .= ( $name = $columnNames[$j]) ? ($j ? ',' : '') . "{$name}:\"{$column}\"" : '';
                    if (is_string($column)){
                        $column = trim($column);
                        $chars = array("\r\n", "\n", "\r");
                        $column = str_replace($chars, " ", $column);
                        $column = str_replace('"', '\"', $column);
                    }
                    if ($columnNames[$j] != null) {
                        if ($firstColumnAdded)
                            $jsCode .= ",";

                        $jsCode .= "{$columnNames[$j]}:\"{$column}\"";
                        $firstColumnAdded = true;
                    }
                }
                $firstRowAdded = true;
                $jsCode .= "}\n";
            }
        }
        $jsCode .= "]);\n";
        $this->page->addJsCode($jsCode);
    }

    public function callRowMethod() {
        if (isset($this->rowMethod)) {
            if ($this->rowMethod[0] == 'form') {
                $this->rowMethod[0] = get_class($this->form);
            }
            $i = $this->currentRow;
            $row = $this->data[$i];
            call_user_func($this->rowMethod, $i, $row, $this->actions, $this->columns, $this->query, $this);
        }
    }

}

?>
