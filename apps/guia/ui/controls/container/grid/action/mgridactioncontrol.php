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

class MGridActionControl {

    public static function text($action, $i, $row) {
        $value = $action->value;
        if ($action->enabled) {
            $actionLink = $action->generateLink($row);
            $action->id = uniqid($action->grid->currentRow);
            $control = ($action->grid->linkType == 'hyperlink') ?
                    new MLink($action->id, $value) :
                    new MLinkButton($action->id, $value);
            $actionAjax = $action->getAjax();
            if ($actionAjax) {
                $ajax = clone $actionAjax;
                $ajax->url = $actionLink;
                $control->setAjax($ajax);
            } else {
                $control->setAction($actionLink);
            }
            $control->setClass('actionTextLink');
        } else {
            $control = new MSpan('', $value, 'actionTextDisable');
        }
        return $control;
    }

    public static function icon($action, $i, $row) {
        $value = $action->value;
        $image = 'managerIcon managerIcon' . ucfirst($value) . ($action->enabled ? 'On' : 'Off');
        if ($action->enabled) {
            $actionLink = $action->generateLink($row);
            $action->id = uniqid($action->grid->currentRow);
            $control = ($action->grid->linkType == 'hyperlink') ?
                    new MImageLink($action->id, $action->alt, $image) :
                    new MImageButton($action->id, $action->alt, $image);
            $actionAjax = $action->getAjax();
            if ($actionAjax) {
                $ajax = clone $actionAjax;
                $ajax->url = $actionLink;
                $control->setAjax($ajax);
            } else {
                $control->setAction($actionLink);
            }
        } else {
            $control = new MDiv('', $action->alt, $image);
        }
        $control->setHTMLTitle(ucfirst($value));
        return $control;
    }

    public static function select($action, $i, $row) {
        $index = $row[$action->grid->index];
        $control = new MCheckBox($action->id . "[{$action->grid->currentIndex}]", $index, '', (array_search($index, $action->grid->selecteds) !== false));
        $control->addEvent('click', "{$action->grid->name}.check({$action->grid->currentIndex},{$index});", false);
        return $control;
    }

}

?>