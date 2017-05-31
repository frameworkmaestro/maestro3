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

class MGridColumnControl {

    public static function label($column, $i, $row) {
        $value = $row[$column->index ?: 0];
        $control = new MLabel($value);
        if ($column->options) {
            $control->setText(($column->grid->showid ? " ({$value})" : '') . $column->options[$value]);
        }
        return $control;
    }

    public static function render($column, $i, $row) {
        $value = $row[$column->index ?: 0];
        if (is_string($column->render)) {
            $method[0] = 'MGridColumnRender';
            $method[1] = 'render' . $column->render;
        } else {
            $method[0] = $column->render[0] == 'form' ? $column->grid->form : $column->render[0];
            $method[1] = $column->render[1];
        }
        return call_user_func($method, $value, $i, $row);
    }

    public static function link($column, $i, $row) {
        $value = $row[$column->index ?: 0];
        $control = new MLink();
        $n = count($row);
        $action = $column->action ? : $column->href;

        if (preg_match_all('/#([^#]*)#/', $action, $matches)) {
            if (is_array($matches[1])) {
                foreach ($matches[1] as $match) {
                    $action = preg_replace('/#' . $match . '#/', trim($row[$match]), $action);
                }
            }
        }

        if ($column->options) {
            $control->setText(($column->grid->showid ? " ({$value})" : '') . $column->options[$value]);
        }

        $action = str_replace('#?#', $value, $action);
        $control->setId(uniqid());
        $control->setAction($action);
        $control->setText($value);
        $control->setClass('mGridLink');
        return $control;
    }

    public static function control($column, $i, $row) {

        $control = clone $column->baseControl; // clonning
        $name = $control->getName();
        $index = $row[$column->grid->index];
        
        // if control is not an array, it adds a '[' to make it an array
        if ((strpos($name, "[") === false) && (strpos($name, "]") === false)) {
            $name .= "[$i]";
        } else {
            // position of identifier to be replaced
            $pos = strpos($name, '%');
            // if the name is according to rules name (with line number between %)
            if (!$pos === false) {
                $rowNumber = substr($name, $pos + 1, -2);
                $name = str_replace("%$rowNumber%", trim($row[$rowNumber]), $name);
            }
        }
        if (strpos($name, "\$") !== false) {            
            $name = preg_replace('/\$id/', $index, $name);
        }
        if ($control->related){
            $control->setRelated(preg_replace('/\$id/', $index, $control->related));
        }
        $action = $control->action;

        if (preg_match_all('/#([^#]*)#/', $action, $matches)) {
            if (is_array($matches[1])) {
                foreach ($matches[1] as $match) {
                    $action = preg_replace('/#' . $match . '#/', trim($row[$match]), $action);
                }
            }
            $control->setAction($action);
        }        

        $control->setName($name);
        $control->setId($name);
        $value = $control->getValue();
        if (($pos = strpos($value, '%')) === false) {
            $value = $row[$column->index];
        } else {
            if (preg_match('/%(.*)%/', $value, $matches)) {
                $grid = $column->grid;
                $column = $grid->getColumn($matches[1]);
                $value = preg_replace('/%(.*)%/', $row[$column->index], $value);
            }
        }
        $control->setValue($value);
        if ($control->getAjax() && $column->baseControl){
            $control->ajax = clone $column->baseControl->ajax;
            $url = $control->getAjax()->url;
            if (preg_match('/%(.*)%/', $url, $matches)) {
                $grid = $column->grid;
                $column = $grid->getColumn($matches[1]);
                $url = preg_replace('/%(.*)%/', $row[$column->index], $url);
            } 
            if (preg_match_all('/#([^#]*)#/', $url, $matches)) {
                if (is_array($matches[1])) {
                    foreach ($matches[1] as $match) {
                        $url = preg_replace('/#' . $match . '#/', trim($row[$match]), $url);
                    }
                }
            } 
            
            $control->getAjax()->url = $url;
        }
        return $control;
    }

}
?>