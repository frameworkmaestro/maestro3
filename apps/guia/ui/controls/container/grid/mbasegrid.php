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

class MBaseGrid extends MContainerControl {

    public $orderBy; // base column to sort
    public $ordered; // is ordered?
    public $data; // table data cells
    public $actions; // array with actions controls
    public $select; // a column for select action
    public $showId; // show ids or not?
    public $columns; // array with columns
    public $icons; // action icons
    public $errors; // array of errors
    public $pageLength; // max number of rows to show - 0 to all rows
    public $rowCount; // total number of rows
    public $href; // url to access the grid
    public $navigator; // gridnavigator
    public $navigatorRender; // HTML code from gridnavigator
    public $linkType; // hyperlink or linkbutton (forced post)
    public $width; // table width for the grid
    public $rowMethod; // method to execute (callback) at each row
    public $index; // the column to act as index of grid
    public $gridControls;
    public $emptyMsg;
    public $currentRow; // index of row being renderized
    public $range; // objeto MRange usado para definir a navegacao de páginas
    public $gridBox;
    public $selecteds;  // all rows selecteds
    public $pageNumber; // current page number - starts from 1
    public $prevPage; // previous page number
    public $footer;
    public $hasDetail; // if ajax detail is used
    public $alternateColors;
    public $buttonSelectClass;
    public $state; // MState object to store transient values
    public $form; // A MBaseForm object, if this grid is inside a form
    public $actionDefault;
    public $firstIndex;
    public $currentIndex;
    public $idSelect;
    public $dnd;
    public $scrollable;
    public $scrollHeight;
    public $scrollWidth;
    public $hasForm;
    public $lookupName;
    public $columnName;
    public $toolBar;
    public $jsCode; // codigo Javascript a ser renderizado junto com o codigo da grid

    /*
      Grid constructor
      $data - the data array
      $columns - array of columns objects
      $href - base url of this grid
      $pageLength - max number of rows to show (0 to show all)
     */

    public function init($id = '', $data = NULL, $columns = '', $href = '', $pageLength = 15, $index = 0, $useSelecteds = true, $useNavigator = true) {
        parent::init($id);
        $this->setColumns($columns);
        $this->href = $href;
        $this->pageLength = $pageLength;
        $this->data = $data;
        $this->index = $index;
        $this->rowCount = count($this->data);
    }

    public function onCreate() {
        parent::onCreate();
        $this->width = '100%';
        $this->setLinkType('linkbutton');
        $this->gridBox = new MBox('', '', '');
        $this->rowMethod = null;
        $this->emptyMsg = _M("Nenhum registro encontrado!");
        $this->gridControls = array();
        $this->select = NULL;
        $this->hasDetail = false;
        //$this->actionDefault = new MGridActionDefault($this, '&nbsp;&nbsp;', NULL);
        $this->alternateColors = true;
        $this->buttonSelectClass = 'linkbtn';
        $this->currentRow = 0;
        $this->index = 0;
        $this->firstIndex = 0;
        $this->currentIndex = 0;
        $this->dnd = false;
        $this->scrollHeight = '';
        $this->scrollable = false;
        $this->hasForm = false;
        $this->renderAs = 'table';
    }

    public function onAfterCreate() {
        $id = $this->getId();
        $this->state = new MState($this->getId());
        $this->pageNumber = 1;
        $this->prevPage = 1;
        if (urldecode(mrequest($id . '_PAGING')) == 'yes') {
            $this->pageNumber = mrequest($id . '_GOPAGE');
            $this->prevPage = mrequest($id . '_PAGE');
        }
        $this->lookupName = mrequest('__lookupName');
    }

    public function __set($name, $value) {        
        $property = strtolower($name);
        //mtrace('mgrid property = ' . $name . ' value = ' . $value);
        if(!$value){
            return;
        }
        if ($property == 'actionupdate') {
            $this->addActionUpdate($value);
        } elseif ($property == 'actiondelete') {
            $this->addActionDelete($value);
        } elseif ($property == 'actiontext') {
            $this->addActionText('', $value);
        } elseif ($property == 'actionicon') {
            $this->addActionIcon('', $value);
        } elseif ($property == 'actionselect') {
            $this->addActionSelect($value);
        } else {
            parent::__set($name, $value);
        }
    }

    public function setTitle($title) {
        $this->setCaption($title);
    }

    public function setCaption($title) {
        $this->title = $this->text = $this->caption = $title;
    }

    public function setPageLength($pageLength) {
        $this->pageLength = $pageLength;
    }

    public function getPageLength() {
        return $this->pageLength;
    }

    public function setFooter($footer) {
        $this->footer = $footer;
    }

    public function setHasForm($value) {
        $this->hasForm = $value;
    }

    public function setClose($value) {
        $this->close = $value;
    }

    /* It is used with MXMLControls */

    public function addControl($c) {
        $this->addColumn($c);
    }

    public function addColumn($column, $pos = '') {
        $k = $pos ? : count($this->columns);
        $this->columns[$k] = $column;
        if ($column->index < 0) {
            $this->columns[$k]->index = $k;
        }
        $this->columns[$k]->grid = $this;
    }

    public function setColumns($columns) {
        $this->columns = array();
        if ($columns) {
            if (!is_array($columns)) {
                $columns = array($columns);
            }
            foreach ($columns as $k => $c) {
                $this->addColumn($c);
            }
        }
    }

    public function getColumns() {
        return $this->columns;
    }

    public function getColumn($index = 0) {
        return $this->columns[$index];
    }

    public function getURL() {
        return $this->href;
    }

    public function setLinkType($linkType) {
        $this->linkType = strtolower($linkType);
    }

    public function setControls($gridControls) {
        if (!is_array($gridControls)) {
            $gridControls = array($gridControls);
        }
        $this->gridControls = array_merge($this->gridControls, $gridControls);
    }

    public function setWidth($width) {
        $this->width = $width;
    }

    public function setIndex($index) {
        $this->index = $index;
    }

    /**
     *
     * @param array $classMethod
     * @param <type> $method
     */
    public function setRowMethod($classMethod) {
        $this->rowMethod = $classMethod;
    }

    public function setColumnAttr($col, $attr, $value) {
        $this->columns[$col]->$attr = $value;
    }

    public function setButtonSelectClass($class = '') {
        $this->buttonSelectClass = $class;
    }

    public function setAlternate($status = true) {
        $this->alternateColors = $status;
    }

    public function setRange($range) {
        $this->range = $range;
    }

    public function setData($data) {
        $this->data = $data;
        $this->rowCount = count($this->data);
    }

    public function getData() {
        return $this->data;
    }

    public function getDataValue($row, $col) {
        return $this->data[$row][$col];
    }

    public function getDataPage() {
        if (count($this->data) && is_array($this->data)) {
            $this->firstIndex = $this->navigator->idxFirst;
            return array_slice($this->data, $this->navigator->idxFirst, $this->navigator->gridCount);
        }
    }

    public function getPageNumber() {
        return $this->pageNumber;
    }

    public function getPrevPage() {
        return $this->prevPage;
    }

    public function getCurrentRow() {
        return $this->currentRow;
    }

    function addActionSelect($idSelect = '') {
        if ($idSelect != '') {
            $id = $this->name . '_SELECT';
            $this->idSelect = $idSelect ? : ($id . '_CHECKED');
            $selecteds = mrequest($this->idSelect) ?: Manager::getData()->{$this->idSelect};
            $this->selecteds = explode(':', $selecteds);
            $this->select = new MGridActionSelect($this, $id);
            $this->addColumn($this->select);
        }
    }

    public function addActionIcon($alt, $icon, $href, $index = 0) {
        if ($icon != '') {
            if (strpos($icon, ':')) {
                $s = explode(':', $icon);
                $action = new MGridActionIcon($this, $s[0], $s[1], $s[2]);
            } else {
                $action = new MGridActionIcon($this, $icon, $href, $alt);
            }
            $this->addColumn($action);
        }
    }

    public function addTool($title, $action, $icon) {
        $this->toolBar = $this->toolBar ? : new MToolBar();
        $this->toolBar->addItem($title, $action, $icon);
    }

    public function addActionText($alt, $text, $href, $index = 0) {
        if ($text != '') {
            if (strpos($text, ':')) {
                $s = explode(':', $text);
                $action = new MGridActionText($this, $s[0], $s[1]);
            } else {
                $action = new MGridActionText($this, $text, $href);
            }
            $this->addColumn($action);
        }
    }

    public function addActionUpdate($href) {
        $this->addActionIcon(_M("Edit"), 'edit', $href);
    }

    public function addActionDelete($href) {
        $this->addActionIcon(_M("Delete"), 'delete', $href);
    }

    public function addActionChoose($href) {
        $this->addActionIcon(_M("Select"), 'select', $href);
    }

    public function addActionDetail($href) {
        $this->hasDetail = true;
        $this->addColumn(new MGridActionDetail($this, 'detail', $href));
    }

    public function applyOrder($column) {
        $p = $this->columns[$column]->index;
        $n = count($this->data[0]);

        foreach ($this->data as $key => $row) {
            for ($i = 0; $i < $n; $i++)
                $arr[$i][$key] = $row[$i];
        }

        $sortcols = "\$arr[$p]";

        for ($i = 0; $i < $n; $i++)
            if ($i != $p)
                $sortcols .= ",\$arr[$i]";

        eval("array_multisort({$sortcols}, SORT_ASC);");
        $this->data = array();

        for ($i = 0; $i < $n; $i++) {
            foreach ($arr[$i] as $key => $row)
                $this->data[$key][$i] = $row;
        }
    }

    public function addError($err) {
        if ($err) {
            if (is_array($err)) {
                if ($this->errors) {
                    $this->errors = array_merge($this->errors, $err);
                } else {
                    $this->errors = $err;
                }
            } else {
                $this->errors[] = $err;
            }
        }
    }

    public function addJsCode($jsCode) {
        $this->jsCode .= $jsCode;
    }

    public function showID($state) {
        $this->showId = $state;
    }

    public function setForm(MBaseForm $form) {
        $this->form = $form;
    }

    private function addFormFields() {
        $this->form->addField(new MHiddenField($this->name . '_PAGE', '1'));
        $this->form->addField(new MHiddenField($this->name . '_GOPAGE', '1'));
        if ($this->idSelect != '') {
            $this->form->addField(new MHiddenField($this->idSelect, Manager::getData($this->idSelect)));
        }
        $this->form->getField($this->name . '_PAGE')->setValue($this->pageNumber);
        $this->form->getField($this->name . '_GOPAGE')->setValue($this->pageNumber);
        $this->form->getField($this->name . '_PAGING')->setValue('no');
        $this->state->saveViewState();
        if ($this->state->getId() != '') {
            $this->form->addField(new MHiddenField($this->state->getId(), $this->state->getCode()));
        }
    }

    public function generateNavigationHeader() {
        if ($this->navigator) {
            return $this->navigator->generate();
        }
        return null;
    }

    public function generateNavigationFooter() {
        if ($this->navigator) {
            return $this->navigator->generate();
        }
        return null;
    }

    public function generateControls() {
        if (count($this->gridControls)) {
            $i = 0;
            foreach ($this->gridControls as $c) {
                $array[$i++] = $c->generate();
                $array[$i++] = '&nbsp;&nbsp;';
            }
            return new MDiv('', $array, 'mGridcontrols');
        }
        return null;
    }

    public function hasErrors() {
        return count($this->errors);
    }

    public function generateErrors() {
        
    }

    public function generateHeader() {
        $header = [];
        if ($this->data) {
            $header[] = $this->toolBar;
            $header[] = $this->generateNavigationHeader();
        }
        return $header;
    }

    public function generateColumnsControls($i, $row) {
        foreach ($this->columns as $column) {
            $column->generateControl($i, $row);
        }
    }

    public function generateEmptyMsg() {
        $div = new MDiv('', $this->emptyMsg, 'mGridAttention');
        return $div;
    }

    public function generateColumnName() {
        $this->columnName = array();
        $row = $this->data[0];
        $n = count($row);
        for ($i = 0; $i < $n; $i++) {
            $this->columnName[$i] = 'column_' . $i;
        }
    }

    public function generateJsManager() {
        $id = $this->getId();
        $this->getPage()->addJsCode($id . " = Manager.Grid('{$id}',{$this->pageNumber});");
    }

    public function generateJsDnD() {
        if ($this->dnd) {
            $this->getPage()->addDojoRequire("dojo.dnd.Source");
            $this->getPage()->addJsCode("var {$this->name}DnD;");
            $this->getPage()->onLoad("{$this->name}DnD = new dojo.dnd.Source(manager.getElementById('{$this->name}'));");
        }
    }

    public function generateJsLookup() {
        if ($this->lookupName != '') {
            $this->page->addJsCode("{$this->name}.selectRow(function (index) {dojo.publish('{$this->lookupName}',[{$this->name}.data[index]]);});");
        }
    }

    public function generateJsGeneric() {
        if ($this->jsCode != '') {
            $this->page->addJsCode($this->jsCode);
        }
    }

    public function generateJsData() {
        $jsCode = $this->id . ".setData( [\n";
        if (count($this->data)) {

            $firstRowAdded = false;

            if ($this->data) {
                foreach ($this->data as $i => $row) {

                    if ($firstRowAdded)
                        $jsCode .= ",";

                    $jsCode .= "{";
                    $firstColumnAdded = false;

                    foreach ($row as $j => $column) {
                        $column = trim($column);
                        $chars = array("\r\n", "\n", "\r", "\"");
                        $column = str_replace($chars, " ", $column);
                        if ($firstColumnAdded)
                            $jsCode .= ",";

                        $jsCode .= "column{$j}:\"{$column}\"";
                        $firstColumnAdded = true;
                    }
                    $firstRowAdded = true;
                    $jsCode .= "}\n";
                }
            }
        }
        $jsCode .= "]);\n";
        $this->page->addJsCode($jsCode);
    }

    public function generateJsCode() {
        $this->generateJsManager();
        $this->generateJsDnd();
        $this->generateJsLookup();
        $this->generateJsGeneric();
        $this->generateJsData();
    }

    public function callRowMethod() {
        if (isset($this->rowMethod)) {
            if ($this->rowMethod[0] == 'form') {
                $this->rowMethod[0] = $this->form;
            }
            $i = $this->currentRow;
            $dataIndex = array_keys($this->data);            
            $row = $this->data[$dataIndex[$i]];
            call_user_func($this->rowMethod, $i, $row, $this->actions, $this->columns);
        }
    }

    public function createColumns() {
        if ($this->data) {
            $n = count($this->data[0]);
            for ($i = 0; $i < $n; $i++) {
                $columns[] = new MGridColumn('Column' . $i);
            }
            $this->setColumns($columns);
        }
    }

    public function generateTable() {
        return '';
    }

    public function generateBody() {
        return '';
    }

    public function generateFields() {
        $fields = array();
        //$fields[] = new MHiddenField('_GRIDNAME', '_gridName');
        $fields[] = new MHiddenField($this->name . '_PAGING', 'no');
        if ($this->idSelect != '') {
            $fields[] = new MHiddenField($this->idSelect, Manager::getData($this->idSelect));
        }
        //if (is_null(mrequest('_GRIDNAME'))) {
        //    $fields[] = new MHiddenField($this->name . '_PAGE', '1');
        //    $fields[] = new MHiddenField($this->name . '_GOPAGE', '1');
        //} else {
            $fields[] = new MHiddenField($this->name . '_PAGE', $this->pageNumber);
            $fields[] = new MHiddenField($this->name . '_GOPAGE', $this->pageNumber);
        //}
        $this->state->saveViewState();
        $fields[] = new MHiddenField($this->state->getId(), $this->state->getCode());
        return $fields;
    }

    public function generateFooter() {
        $footer = is_array($this->footer) ? $this->footer : array($this->footer);
        if (!$this->data) {
            if (!$this->dnd) {
                $footer[] = $this->generateEmptyMsg();
            }
        } else {
            $footer[] = $this->generateNavigationFooter();
        }
        $footer[] = $this->generateControls();
        return $footer;
    }

    public function generate() {
        $this->onBeforeGenerate();
        $this->generateData();
        $this->generateJsCode();
        $this->onGenerate();
        $header = $this->painter->generateToString($this->generateHeader());
        $body = $this->painter->generateToString($this->generateBody());
        $fields = $this->painter->generateToString($this->generateFields());
        $footer = $this->painter->generateToString($this->generateFooter());
        $class = 'mGrid' . ($this->scrollable ? '' : ' mGridNoScroll');
        $grid = new MDiv($this->name . 'Div', array($header, $body, $fields, $footer), $class);
        if ($this->width != '') {
            $grid->addStyle('width', $this->width);
        }
        if ($this->scrollHeight != '') {
            $grid->addStyle('height', $this->scrollHeight);
            $grid->addStyle('overflow', 'auto');
        }
        $this->generateEvent();
        $hasForm = ($this->form instanceof MBaseForm) || $this->hasForm;

        if (!$hasForm) {
            $this->setForm(new MForm());
            $this->form->setName('form' . ucfirst($this->name));
            $this->form->addField($grid);
            $result = $this->form->generate();
        } else {
            if ($this->caption != '') {
                $box = new MBox($this->caption, $this->close, '');
                $box->setControls($grid);
            } else {
                $box = $grid;
            }
            $result = $box->generate();
        }

        $this->onAfterGenerate();
        return $result;
    }

    public function generateData() {
        if ($this->data) {
            $this->orderBy = $this->state->get('orderby', $this->name);
            if ($this->ordered = isset($this->orderBy)) {
                $this->applyOrder($this->orderBy);
                $this->state->setViewState('orderby', $this->orderBy, $this->name);
            }
            if ($this->range) { // a navegação é definida pelo objeto MRange
                $this->navigator = new MGridNavigator($this->range->rows, $this->range->total, $this->getURL(), $this);
            } else if ($this->pageLength) {
                $this->navigator = new MGridNavigator($this->pageLength, $this->rowCount, $this->getURL(), $this);
                $this->data = $this->getDataPage();
            } else {
                $this->navigator = null;
            }
        }
    }

}

?>