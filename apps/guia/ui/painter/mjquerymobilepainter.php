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

class MHtmlPainter extends MBasePainter {

    private $control;

    private function getAttribute($name, $value) {
        return " {$name}=\"{$value}\"";
    }

    private function getAttributeValue($name, $value) {
        return $value ? " {$name}=\"{$value}\"" : "";
    }

    private function getId() {
        return $this->getAttributeValue('id', $this->control->getId());
    }

    private function getClass() {
        return $this->getAttributeValue('class', $this->control->getClass());
    }

    private function getName() {
        return $this->getAttribute('name', $this->control->getName());
    }

    private function getValue() {
        return $this->getAttribute('value', $this->control->getValue());
    }

    private function get($control){
        $this->control = $control;
        return array(
            $this->getId(),
            $this->getName(),
            $this->getValue(),
            $this->getClass(),
            $control->getAttributes()
        );
    }

    public function div($control) {
        list ($id, $name, $value, $class, $attributes) = $this->get($control);
        return <<<EOT
<div {$id}{$class}{$attributes}>
    {$control->getInnerToString()}
</div>
EOT;
    }

    public function span($control) {
        list ($id, $name, $value, $class, $attributes) = $this->get($control);
        return <<<EOT
<span {$id}{$class}{$attributes}>{$control->getInnerToString()}</span>
EOT;
    }

    public function text($control) {
        list ($id, $name, $value, $class, $attributes) = $this->get($control);
        $text = $control->value ? $value: $control->getText();
        return <<<EOT
<span {$id}{$class}{$attributes}>{$text}</span>
EOT;
    }

    public function inputHidden($control) {
        list ($id, $name, $value, $class, $attributes) = $this->get($control);
        return <<<EOT
<input type="hidden" {$id}{$name}{$value}>
EOT;
    }

    public function inputButton($control) {
        list ($id, $name, $value, $class, $attributes) = $this->get($control);
        $onclick = $this->getAttributeValue('onclick', $control->onclick);
        return <<<EOT
<input type="button" {$id}{$name}{$class}{$value}{$onclick}{$attributes}>
EOT;
    }

    public function inputText($control) {
        list ($id, $name, $value, $class, $attributes) = $this->get($control);
        $type = $control->getType();
        $value = ($type != 'file' ? $value : '');
        return <<<EOT
<input type="{$type}" {$id}{$name}{$class}{$value}{$attributes}>
EOT;
    }

    public function inputCheck($control) {
        list ($id, $name, $value, $class, $attributes) = $this->get($control);
        $type = $control->getType();
        $value = ($type != 'file' ? $value : '');
        $checked = ($control->checked != '') ? " checked" : "";
        $label = ($control->text != '') ? "<label {$this->getAttribute('for', $control->id)}>{$control->getText()}</label>" : '';
        return <<<EOT
<div {$class}><input type="{$type}" {$id}{$name}{$value}{$attributes}{$checked}>{$label}</div>
EOT;
    }

    public function inputTextArea($control) {
        list ($id, $name, $value, $class, $attributes) = $this->get($control);
        $rows = $this->getAttribute('rows', $control->rows);
        $cols = $this->getAttribute('cols', $control->cols);
        return <<<EOT
<textarea {$id}{$name}{$class}{$rows}{$cols}>{$value}</textarea>
EOT;
    }

    public function button($control) {
        list ($id, $name, $value, $class, $attributes) = $this->get($control);
        $type = $control->getType();
        $text = (($control->value != '') ? "{$control->value}" : (($control->text != '') ? "{$control->text}" : "{$control->caption}"));
        return <<<EOT
<button {$id}{$type}{$name}{$class}{$value}{$attributes}>{$text}</button>
EOT;
    }

    public function label($control) {
        list ($id, $name, $value, $class, $attributes) = $this->get($control);
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
        $src = $this->getAttribute('src', $control->location);
        $alt = $this->getAttribute('alt', $control->label);
        return <<<EOT
<img {$src}{$id}{$class}{$attributes}>
EOT;
    }

    public function simpletable($control) {
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
        $html .= "\n<tbody>";
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
        return $this->tableElement($control,'colgroup');
    }

    public function th($control) {
        return $this->tableElement($control,'th');
    }

    public function td($control) {
        return $this->tableElement($control,'td');
    }

    public function tr($control) {
        return $this->tableElement($control,'tr');
    }

    public function thead($control) {
        return $this->tableElement($control,'thead');
    }

    public function tfoot($control) {
        return $this->tableElement($control,'tfoot');
    }

    public function tbody($control) {
        return $this->tableElement($control,'tbody');
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
        if ($type = $control->getType()) {
            $html = <<<EOT
<li type="{$type}">{$control->value}</li>
EOT;
        }
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

    public function iFrame($control) {
        list ($id, $name, $value, $class, $attributes) = $this->get($control);
        $src = $this->getAttribute('src', $control->src);
        return <<<EOT
<iframe {$name}{$src}{$attributes}></iframe>
EOT;
    }

    public function hr($control) {
        return "<hr " . $control->getAttributes() . ">";
    }

    public function form($control) {
        $enctype = ($control->enctype != '' ? " enctype=\"$control->enctype\"" : '');
        $onSubmit = ($control->onsubmit != '' ? " onSubmit=\"$control->onsubmit\"" : '');
        $inner = $this->generateToString($control->content);;
        return <<<EOT
<form id="{$control->id}" name="{$control->id}" method="post" action="{$control->action}" {$enctype}{$onSubmit}>
{$inner}
</form>
EOT;
    }
}

?>