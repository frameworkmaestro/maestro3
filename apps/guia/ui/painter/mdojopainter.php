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

class MDojoPainter extends MBasePainter {

    /**
     * Panes
     */
    public function div($control) {
        list ($id, $name, $value, $class, $attributes) = $this->get($control);
        return <<<EOT
<div {$id}{$class}{$attributes}>
    {$control->getInnerToString()}
</div>
EOT;
    }

    public function contentpane($control) {
        $control->setDojoType('Manager.ElementPane');
        $control->addStyle('padding', '0px');
        $href = $control->getHREF();
        if ($href) {
            $control->addDojoProp('href', $href);
        }
        $control->addDojoProp('doLayout', $control->getDoLayout());
        list ($id, $name, $value, $class, $attributes) = $this->get($control);
        return $this->div($control);
    }

    public function box($control) {
        $content = array();
        $control->setId('box' . uniqid());
        $content[] = $control->toolBar;
        if ($control->hasItems()) {
            $content[] = new MDiv(NULL, $control->getControls(), 'mBoxPaneContentArea ' . $control->extendClass['content']);
        }
        if ($control->actionBar->hasItems()) {
            $content[] = new MDiv(NULL, $control->actionBar->getControls(), 'mBoxPaneActionBar ' . $control->extendClass['action']);
        }
        $box = new MDiv($control->id, $content);
        $box->setDojoType('Manager.BoxPane');
        $box->addDojoProp('classTitle', $control->extendClass['title']);
        $box->addDojoProp('buttonCancel', 'Fechar');
        $box->addDojoProp('title', $control->caption);
        $box->addDojoProp('toolBar', $control->toolBar->getId());
        $div = new MDiv($control->id . 'Outer', $box);
        $div->cloneStyle($control);
        return $this->div($div);
    }

    public function custombox($control) {
        $content = array();
        $content[] = $control->toolBar;
        if ($control->caption) {
            $caption = new MLabel($control->caption);
            $caption->setClass('mCustomBoxCaptionLabel');
            $content[] = new MDiv(NULL, $caption, 'mCustomBoxCaption ' . $control->extendClass['title']);
        }
        if ($control->hasItems()) {
            $content[] = new MDiv(NULL, $control->getControls(), 'mCustomBoxContentArea ' . $control->extendClass['content']);
        }
        if ($control->actionBar->hasItems()) {
            $content[] = new MDiv(NULL, $control->actionBar->getControls(), 'mCustomBoxActionBar ' . $control->extendClass['action']);
        }
        $box = new MDiv($control->id, $content);
        $box->setDojoType('Manager.BoxPane');
        $box->addDojoProp('classTitle', $control->extendClass['title']);
        $box->addDojoProp('buttonCancel', 'Fechar');
        $box->addDojoProp('title', $control->caption);
        $box->addDojoProp('toolBar', $control->toolBar->getId());
        $div = new MDiv($control->id . 'Outer', $box);
        $div->cloneStyle($control);
        return $this->div($div);
    }

    /**
     * Menus
     */
    public function menuseparator($control) {
        $this->page->addDojoRequire('dijit/MenuSeparator');
        $control->setDojoType("dijit/MenuSeparator");
        return $this->div($control);
    }

    public function dropdownmenu($control) {
        $this->page->addDojoRequire('dijit/DropDownMenu');
        $control->setDojoType('dijit/DropDownMenu');
        return $this->div($control);
    }

    public function menu($control) {
        $this->page->addDojoRequire('dijit/Menu');
        $control->setDojoType('dijit/Menu');
        return $this->div($control);
    }

    public function menubar($control) {
        $this->page->addDojoRequire('dijit/MenuBar');
        $control->setDojoType('dijit/MenuBar');
        return $this->div($control);
    }

    public function menubaritem($control) {
        $this->page->addDojoRequire('dijit/MenuBarItem');
        $control->setDojoType('dijit/MenuBarItem');
        return $this->div($control);
    }

    public function menuitem($control) {
        $this->page->addDojoRequire('dijit/MenuItem');
        $control->setDojoType('dijit/MenuItem');
        $icon = ucfirst($control->icon);
        $control->addDojoProp("iconClass", "dijitIcon " . ($icon ? "dijitIcon{$icon}" : ""));
        return $this->div($control);
    }

    public function popupmenubaritem($control) {
        $this->page->addDojoRequire('dijit/PopupMenuBarItem');
        $control->setDojoType("dijit/PopupMenuBarItem");
        $icon = ucfirst($control->icon);
        $control->addDojoProp("iconClass", "dijitIcon " . ($icon ? "dijitIcon{$icon}" : ""));
        if ($control->openOnOver) {
            $code = <<< HERE
            dijit._MenuBase.prototype.onItemHover = function(item){
                if(this.passive_hover_timer){
                    this.passive_hover_timer.remove();
		}
		this.focusChild(item);
		if(item.disabled){
        		return false;
		}
		if(item.popup){
                    this.set("selected", item);
                    this.set("activated", true);
                    this._openItemPopup(item, false);
                }
            };

HERE;
            $this->page->addJsCode($code);
        }
        return $this->div($control);
    }

    public function popupmenuitem($control) {
        $this->page->addDojoRequire('dijit/PopupMenuItem');
        $control->setDojoType("dijit/PopupMenuItem");
        $icon = ucfirst($control->icon);
        $control->addDojoProp("iconClass", "dijitIcon " . ($icon ? "dijitIcon{$icon}" : ""));
        if ($control->openOnOver) {
            $code = <<< HERE
            dijit._MenuBase.prototype.onItemHover = function(item){
                if(this.passive_hover_timer){
                    this.passive_hover_timer.remove();
		}
		this.focusChild(item);
		if(item.disabled){
        		return false;
		}
		if(item.popup){
                    this.set("selected", item);
                    this.set("activated", true);
                    this._openItemPopup(item, false);
                }
            };

HERE;
            $this->page->addJsCode($code);
        }
        return $this->div($control);
    }

    public function toolbar($control) {
        $this->page->addDojoRequire('dijit/Toolbar');
        $control->setDojoType('dijit/Toolbar');
        return $this->div($control);
    }

    public function toolbaritem($control) {
        $icon = ucfirst($control->icon);
        $control->setClass("dijitInline toolIcon toolIcon{$icon}");
        return $this->div($control);
    }

    public function breadcrumb($control) {
        $control->inner->setDojoType("dojox/layout/ContentPane");
        return $this->div($control);
    }

    /**/

    public function span($control) {
        list ($id, $name, $value, $class, $attributes) = $this->get($control);
        return <<<EOT
<span {$id}{$class}{$attributes}>{$control->getInnerToString()}</span>
EOT;
    }

    public function text(MControl $control) {
        list ($id, $name, $value, $class, $attributes) = $this->get($control);
        $text = $control->value ? : $control->getText();
        $text = $control->sanitize($text);
        return <<<EOT
<span {$id}{$class}{$attributes}>{$text}</span>
EOT;
    }

    /**
     *  Input
     */
    public function inputHidden($control) {
        list ($id, $name, $value, $class, $attributes) = $this->get($control);
        return <<<EOT
<input type="hidden" {$id}{$name}{$value}{$attributes}>
EOT;
    }

    public function inputButton($control) {
        list ($id, $name, $value, $class, $attributes) = $this->get($control);
        $onclick = $this->getAttributeValue('onclick', $control->onclick);
        return <<<EOT
<input type="button" {$id}{$name}{$class}{$value}{$onclick}{$attributes}>
EOT;
    }

    public function inputField($control) {
        list ($id, $name, $value, $class, $attributes) = $this->get($control);
        $type = $control->getType();
        $value = ($type != 'file' ? $value : '');
        return <<<EOT
<input type="{$type}" {$id}{$name}{$class}{$value}{$attributes}>
EOT;
    }

    public function inputText($control) {
        $this->page->addDojoRequire('dijit/form/TextBox');
        $control->setDojoType('dijit/form/TextBox');
        if ($control->getMask()) {
            $this->page->addScript('jquery/jquery.maskedinput.js');
            $this->page->onLoad("$(dojo.byId('" . $control->getId() . "')).mask('" . $control->getMask() . "');");
        }
        return $this->inputfield($control);
    }

    public function inputCalendar($control) {
        $this->page->addDojoRequire('manager/DateTextBox');
        $control->addDojoProp('promptMessage', "Formato: DD/MM/AAAA");
        $control->setDojoType('manager/DateTextBox');
        if ($control->property->rangeDate) {
            $minDate = $control->property->rangeDate[0];
            $maxDate = $control->property->rangeDate[1];
            if ($minDate) {
                $date = new MDate($minDate);
                $min = $date->format('Y') . ',' . ($date->format('m') - 1) . ',' . $date->format('d');
                $this->page->onload("manager.byId('{$control->id}').constraints.min = new Date({$min});");
            }
            if ($maxDate) {
                $date = new MDate($maxDate);
                $max = $date->format('Y') . ',' . ($date->format('m') - 1) . ',' . $date->format('d');
                $this->page->onload("manager.byId('{$control->id}').constraints.max = new Date({$max});");
            }
        }
        return $this->inputfield($control);
    }

    public function inputTime($control) {
        $this->page->addDojoRequire('dijit/form/TimeTextBox');
        $control->setDojoType('dijit/form/TimeTextBox');
        $id = $control->getId();
        if ($control->property->rangeDate) {
            $minDate = explode(':', $control->property->rangeDate[0]);
            $maxDate = explode(':', $control->property->rangeDate[1]);
            
            $this->page->addDojoRequire("Manager.TimePickerFiltered");
            $this->page->onload("manager.byId('{$id}').popupClass = 'Manager.TimePickerFiltered';");
            if ($minDate) {
                $this->page->onload("manager.byId('{$id}').constraints.min = new Date(0,0,0,{$minDate[0]},{$minDate[1]},{$minDate[2]});");
            }
            if ($maxDate) {
                $this->page->onload("manager.byId('{$id}').constraints.max = new Date(0,0,0,{$maxDate[0]},{$maxDate[1]},{$maxDate[2]});");
            }
        }
        if ($control->property->increment) {
            $this->page->onload("manager.byId('{$id}').constraints.clickableIncrement  = 'T{$control->property->increment}';");
        }

        if ($control->property->lockUserInput) {
            $this->page->onload("dojo.connect(dijit.byId('{$id}'), 'onKeyDown', function(evt){dojo.stopEvent(evt);});");
        }

        return $this->inputfield($control);
    }

    public function inputTimeStamp($control) {
        if ($control->property->rangeDate) {
            $minDate = $control->property->rangeDate[0];
            $maxDate = $control->property->rangeDate[1];
            $id = $control->getId();
            $this->page->addDojoRequire("Manager.TimePickerFiltered");
            $this->page->onload("manager.byId('{$id}Time').popupClass = 'Manager.TimePickerFiltered';");
            if ($minDate) {
                $date = new MTimestamp($minDate, 'd/m/Y H:i:s');
                $min = $date->format('Y') . ',' . ($date->format('m') - 1) . ',' . $date->format('d') . ',' . $date->format('H') . ',' . $date->format('i') . ',' . $date->format('s');
                $this->page->onload("manager.byId('{$id}Time').constraints.min = new Date({$min});");
                $this->page->onload("manager.byId('{$id}Date').constraints.min = new Date({$min});");
            }
            if ($maxDate) {
                $date = new MTimestamp($maxDate, 'd/m/Y H:i:s');
                $max = $date->format('Y') . ',' . ($date->format('m') - 1) . ',' . $date->format('d') . ',' . $date->format('H') . ',' . $date->format('i') . ',' . $date->format('s');
                $this->page->onload("manager.byId('{$id}Time').constraints.max = new Date({$max});");
                $this->page->onload("manager.byId('{$id}Date').constraints.max = new Date({$max});");
            }
        }
        if ($control->property->increment) {
            $this->page->onload("manager.byId('{$this->getId()}Time').constraints.clickableIncrement  = 'T{$control->property->increment}';");
        }
        return $this->inputfield($control);
    }

    public function inputCurrency($control) {
        $this->page->addDojoRequire('dijit/form/CurrencyTextBox');
        $control->setDojoType('dijit/form/CurrencyTextBox');
        $control->addDojoProp('constraints', "@{fractional:true}");
        $control->addDojoProp('currency', $control->ISOCode);
        return $this->inputfield($control);
    }

    public function inputNumber($control) {
        $this->page->addDojoRequire('dijit/form/NumberTextBox');
        $control->setDojoType('dijit/form/NumberTextBox');
        return $this->inputfield($control);
    }

    public function inputNumberSpinner($control) {
        $this->page->addDojoRequire('dijit/form/NumberSpinner');
        $control->setDojoType('dijit/form/NumberSpinner');
        return $this->inputfield($control);
    }

    public function inputFile($control) {
        $this->page->addDojoRequire('dojox.form.Uploader');
        $control->addDojoProp('multiple', $control->multiple ? true : false);
        $this->page->onLoad("manager.page.fileUpload = 'yes';");
        $this->page->onLoad("manager.byId('{$control->getId()}').reset();");
        $this->page->addDojoRequire("dojox.form.uploader.plugins.IFrame");
        $this->page->addDojoRequire("dojox.form.Uploader");
        $this->page->addDojoRequire("dojox.form.uploader.FileList");
        $control->setDojoType('dojox.form.Uploader');
        $control->addDojoProp('label', $control->text);
        $inner = $this->inputfield($control);
        $hidden = new MHiddenField("__ISFILEUPLOADPOST[{$control->getId()}]", 'yes');
        $btnClearList = new MButton($control->getId() . "ClearFiles", 'Apagar');
        $btnClearList->setClass('mFileFieldReset');
        $btnClearList->addEvent('click', "manager.byId('{$control->getId()}').reset();");
        if ($control->multiple) {
            $divFiles = new MDiv($control->getId() . "Files");
            $divFiles->setDojoType("dojox.form.uploader.FileList");
            $divFiles->addDojoProp("uploaderId", $control->getId());
            $divFiles->setWidth('445px');
            $divFiles->addDojoProp("headerFilename", "Arquivo");
            $divFiles->addDojoProp("headerType", "Tipo");
            $divFiles->addDojoProp("headerFilesize", "Tamanho");
            $fieldset = new MBaseGroup();
            $fieldset->addControl(new MHContainer('', array($inner, $btnClearList)));
            $fieldset->addControl($divFiles);
            $fieldset->setWidth('450px');
        } else {
            $input = new MDiv($control->getId(), new MLabel($control->text));
            $input->setDojoType('dojox.form.Uploader');
            $input->addDojoProp('showInput', 'after');
            $input->addDojoProp('name', $control->getId());
            $input->addDojoProp('force', 'iframe');
            $fieldset = new MHContainer('', array($input, $btnClearList));
        }
        return $fieldset->generate() . $hidden->generate();
    }

    public function inputCheck($control) {
        if ($control->type == 'radio') {
            $this->page->addDojoRequire('dijit/form/RadioButton');
            $control->setDojoType('dijit/form/RadioButton');
        } elseif ($control->type == 'checkbox') {
            $this->page->addDojoRequire('dijit/form/CheckBox');
            $control->setDojoType('dijit/form/CheckBox');
        }
        list ($id, $name, $value, $class, $attributes) = $this->get($control);
        $type = $control->getType();
        $value = ($type != 'file' ? $value : '');
        $checked = ($control->checked ? " checked" : "");
        $label = ($control->text != '') ? "<label {$this->getAttribute('for', $control->id)}>{$control->getText()}</label>" : '';
        $input = <<<EOT
<input type="{$type}" {$id}{$name}{$value}{$attributes}{$checked}>{$label}
EOT;
        return ($class != '') ? "<div {$class}>{$input}</div>" : $input;
    }

    public function inputTextArea($control) {
        $this->page->addDojoRequire('dijit/form/SimpleTextarea');
        $control->setDojoType('dijit/form/SimpleTextarea');
        list ($id, $name, $value, $class, $attributes) = $this->get($control);
        $rows = $this->getAttribute('rows', $control->rows);
        $cols = $this->getAttribute('cols', $control->cols);
        $value = $control->getValue();
        return <<<EOT
<textarea {$id}{$name}{$class}{$rows}{$cols}{$attributes}>{$value}</textarea>
EOT;
    }

    /**
     *  Buttons
     */
    public function button($control) {
        $this->page->addDojoRequire('dijit.form.Button');
        $control->setDojoType('dijit.form.Button');
        if ($control->image) {
            $icon = ucfirst($control->image);
            $control->addDojoProp('iconClass', "dijitIcon dijitIcon{$icon}");
        }

        list ($id, $name, $value, $class, $attributes) = $this->get($control);
        $type = $this->getAttribute('type', $control->type);
        $text = (($control->value != '') ? "{$control->value}" : (($control->text != '') ? "{$control->text}" : "{$control->caption}"));
        return <<<EOT
<button {$id}{$type}{$name}{$class}{$attributes}>{$text}</button>
EOT;
    }

    public function buttondropdown($control) {
        $this->page->addDojoRequire('dijit.form.DropDownButton');
        $control->setDojoType('dijit.form.DropDownButton');
        if ($control->image) {
            $icon = ucfirst($control->image);
            $control->addDojoProp('iconClass', "dijitIcon dijitIcon{$icon}");
        }
        $menu = $control->menu->generate();
        list ($id, $name, $value, $class, $attributes) = $this->get($control);
        $type = $this->getAttribute('type', $control->type);
        $text = (($control->value != '') ? "{$control->value}" : (($control->text != '') ? "{$control->text}" : "{$control->caption}"));
        return <<<EOT
<button {$id}{$type}{$name}{$class}{$attributes}><span>{$text}</span>{$menu}</button>
EOT;
    }

    /**/

    public function label($control) {
        $this->control = $control;
        $id = $this->getId();
        $class = $this->getClass();
        $attributes = $control->getAttributes();
        $for = $this->getAttribute('for', $control->getId());
        $text = $control->getText();
        return <<<EOT
<label {$class}{$attributes}>{$text}</label>
EOT;
    }

    public function fieldSet($control) {
        list ($id, $name, $value, $class, $attributes) = $this->get($control);
        $legend = (($control->getCaption() != '') ? "<legend>{$control->getCaption()}</legend>" : "");
        $inner = $control->getInnerToString();
        return <<<EOT
<fieldset {$id}{$class}{$attributes}>
{$legend}{$inner}
</fieldset>
EOT;
    }

    public function select($control) {
        list ($id, $name, $value, $class, $attributes) = $this->get($control);
        $inner = $this->generateToString($control->content);
        return <<<EOT
<select {$id}{$class}{$name}{$attributes}>
{$inner}
</select>
EOT;
    }

    public function optionGroup($control) {
        list ($id, $name, $value, $class, $attributes) = $this->get($control);
        $label = $this->getAttribute('label', $control->getText());
        $inner = $this->generateToString($control->content);
        return <<<EOT
<optgroup {$id}{$class}{$label}>
{$inner}
</optgroup>
EOT;
    }

    public function option($control) {
        list ($id, $name, $value, $class, $attributes) = $this->get($control);
        $label = ( $control->showValues ? $control->value . ' - ' : '') . $control->getLabel();
        $checked = ($control->checked != '') ? " selected" : "";
        return <<<EOT
<option {$value}{$checked}>{$label}</option>
EOT;
    }

    public function anchor($control) {
        list ($id, $name, $value, $class, $attributes) = $this->get($control);
        $href = $control->action ? $this->getAttribute('href', $control->action) : '';
        $text = $control->getText();
        return <<<EOT
<a {$id}{$class}{$href}{$attributes}>{$text}</a>
EOT;
    }

    public function comment($control) {
        return "\n<!-- {$control->value} -->\n";
    }

    public function header($control) {
        list ($id, $name, $value, $class, $attributes) = $this->get($control);
        $text = $control->getText();
        return <<<EOT
<h{$control->level} {$class}{$attributes}>{$text}</h{$control->level}>
EOT;
    }

    public function image($control) {
        list ($id, $name, $value, $class, $attributes) = $this->get($control);
        $src = $control->source ? $this->getAttribute('src', $control->source) : '';
        $alt = $this->getAttribute('alt', $control->label);
        return <<<EOT
<img {$src}{$id}{$class}{$attributes}>
EOT;
    }

    /*
     * Table
     */

    public function simpletable($control) {
        $colgroup = $head = $foot = $body = '';

        list ($id, $name, $value, $class, $attributes) = $this->get($control);
        $coontrolBody = $control->body;
        $caption = ($control->caption != '') ? "<caption>{$control->caption}</caption>" : '';
        if ($n = count($control->colgroup)) {
            for ($i = 0; $i < $n; $i++) {
                $colgroup .= "<colgroup " . $control->colgroup[$i]['attr'] . ">";
                $k = count($control->colgroup[$i]['col']);
                for ($j = 0; $j < $k; $j++) {
                    $colgroup .= "<col " . $control->colgroup[$i]['col'][$j] . ">";
                }
                $colgroup .= "\n</colgroup>";
            }
        }
        if ($n = count($control->head)) {
            $head .= "\n<thead><tr>";
            for ($i = 0; $i < $n; $i++) {
                $head .= "\n<th " . $control->attr['head'][$i] . ">" . $control->head[$i] . "</th>";
            }
            $head .= "\n</tr></thead>";
        }
        if ($n = count($control->foot)) {
            $foot .= "\n<tfoot><tr>";
            for ($i = 0; $i < $n; $i++) {
                $foot .= "\n<td " . $control->attr['foot'][$i] . ">" . $control->foot[$i] . "</td>";
            }
            $foot .= "\n</tr></tfoot>";
        }
        $body .= "\n<tbody>";
        $n = count($coontrolBody);
        for ($i = 0; $i < $n; $i++) {
            $body .= "\n<tr " . $control->attr['row'][$i] . ">";
            $k = count($coontrolBody[$i]);

            for ($j = 0; $j < $k; $j++) {
                $body .= "<td " . $control->attr['cell'][$i][$j] . ">";
                $body .= $coontrolBody[$i][$j];
                $body .= "</td>";
            }

            $body .= "\n</tr>";
        }
        $body .= "\n</tbody>";

        return <<<EOT
<table {$id}{$class}{$attributes}>{$caption}
{$colgroup}
{$head}
{$body}
{$foot}
</table>
EOT;
    }

    private function tableElement($control, $element) {
        list ($id, $name, $value, $class, $attributes) = $this->get($control);
        $inner = $control->getInnerToString();
        return <<<EOT
<{$element} {$class}{$attributes}>
{$inner}
</{$element}>
EOT;
    }

    public function colgroup($control) {
        return $this->tableElement($control, 'colgroup');
    }

    public function th($control) {
        return $this->tableElement($control, 'th');
    }

    public function td($control) {
        return $this->tableElement($control, 'td');
    }

    public function tr($control) {
        return $this->tableElement($control, 'tr');
    }

    public function thead($control) {
        return $this->tableElement($control, 'thead');
    }

    public function tfoot($control) {
        return $this->tableElement($control, 'tfoot');
    }

    public function tbody($control) {
        return $this->tableElement($control, 'tbody');
    }

    public function table($control) {
        list ($id, $name, $value, $class, $attributes) = $this->get($control);
        $caption = ($control->caption != '') ? "<caption>{$control->caption}</caption>" : '';
        $inner = $control->getInnerToString();
        return <<<EOT
<table {$id}{$class}{$attributes}>{$caption}
{$inner}
</table>
EOT;
    }

    /* */

    /*
     * Lists
     */

    public function unorderedList($control) {
        if ($control->content) {
            list ($id, $name, $value, $class, $attributes) = $this->get($control);
            $html = <<<EOT
<ul {$id}>{$control->content}</ul>
EOT;
        }
        return $html;
    }

    public function unorderedListItem($control) {
        $itemType = $control->getType();
        $type = ($itemType != '') ? "type=\"{$itemType}\"" : '';
            $html = <<<EOT
<li {$type}>{$control->value}</li>
EOT;
        return $html;
    }

    public function orderedList($control) {
        if ($control->content) {
            list ($id, $name, $value, $class, $attributes) = $this->get($control);
            $html = <<<EOT
<ol {$id}>{$control->content}</ol>
EOT;
        }
        return $html;
    }

    public function orderedListItem($control) {
        if ($type = $control->getType()) {
            $html = <<<EOT
<li>{$control->value}</li>
EOT;
        }
        return $html;
    }

    /* */

    public function iFrame($control) {
        list ($id, $name, $value, $class, $attributes) = $this->get($control);
        $src = $this->getAttribute('src', $control->source);
        return <<<EOT
<iframe {$id}{$name}{$src}{$attributes}></iframe>
EOT;
    }

    public function hr($control) {
        return "<hr " . $control->getAttributes() . ">";
    }

    /*
     * Form 
     */

    public function concreteform($control) {
        $this->page->addDojoRequire('dijit.form.Form');
        $control->setDojoType('dijit.form.Form');
        $control->addDojoProp('jsId', $this->id);
        return $this->div($control);
    }

    public function form($control) {
        $data = $control->concreteForm->generate();
        $div = new MDiv($control->id . 'Outer', $data);
        return $this->div($div);
    }

    /*
     * Layout
     */

    public function accordion($control) {
        $this->page->addDojoRequire('dijit/layout/AccordionContainer');
        $control->setDojoType("dijit/layout/AccordionContainer");
        $divs = $control->getControls();
        if (count($divs)) {
            foreach ($divs as $div) {
                $div->addDojoProp("title", $div->property->title);
                $div->setDojoType("dijit/layout/AccordionPane");
            }
        }
        return $this->div($control);
    }

    public function stackcontainer($control) {
        if ($control->property->subscribe) {
            $this->page->onLoad("dojo.subscribe('{$control->property->subscribe}',function(id) {manager.byId('{$control->getId()}').selectChild('stack_'+id);} );");
        }
        $this->page->addDojoRequire('dijit/layout/StackContainer');
        $control->setDojoType("dijit/layout/StackContainer");
        return $this->div($control);
    }

    public function tabcontainer($control) {
        $this->page->addDojoRequire('dijit/layout/TabContainer');
        $control->setDojoType("dijit/layout/TabContainer");
        if ($control->selected) {
            $this->page->onLoad("manager.byId('{$control->getId()}').selectChild(manager.byId('tab_{$control->selected}'))");
        }
        if ($control->position == 'bottom') {
            $control->addDojoProp("tabPosition", 'bottom');
        } elseif ($control->position == 'left') {
            $control->addDojoProp("tabPosition", 'left-h');
        } elseif ($control->position == 'right') {
            $control->addDojoProp("tabPosition", 'right-h');
        }

        $divs = $control->getControls();
        if (count($divs)) {
            foreach ($divs as $div) {
                $div->addDojoProp("title", $div->property->title);
                $div->setDojoType("dojox/layout/ContentPane");
            }
        }
        return $this->div($control);
    }

    /* */
}

?>