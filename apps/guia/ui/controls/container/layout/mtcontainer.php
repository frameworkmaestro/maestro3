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


class MTContainer extends MContainer {

    public function onCreate() {
        parent::onCreate();
        $this->setLabelMode(MFieldLabel::LABEL_ABOVE);
        $this->setDisposition(MContainer::DISPOSITION_VERTICAL);
    }

    public function init($name = NULL, $controls = NULL, $labelMode = MFieldLabel::LABEL_ABOVE) {
        parent::init($name, $controls, MContainer::DISPOSITION_VERTICAL, $labelMode);
    }

    public function generateLayout() {
        list($layout, $hidden) = $this->getLayout();
        $array = array();
        $table = new MTable('table_' . $this->getId(), $this->getAttributes());
        $table->style = clone $this->style;
        $i = 0;
        $tr = array();
        foreach ($layout as $control) {
            if ($control instanceof MHContainer) {
                if ($control->checkAccess()) {
                    $mtr = new MTR();
                    $mtr->style = clone $control->style;
                    $controls = $control->getControls();
                    $j = 0;
                    $td = array();
                    $n = count($controls) - 1;
                    foreach ($controls as $content) {
                        $pos = 'cell' . $j . (($j == 0) ? ' firstCell' : (($j == $n) ? ' lastCell' : ''));
                        $mtd = new MTD();
                        $mtd->setClass($pos);
                        $mtd->addControl($content);
                        $width = $content->getColumnWidth();
                        if ($width) {
                            $mtd->width = $width;
                        }
                        $td[] = $mtd;
                        $j++;
                    }
                    $mtr->addControl($td);
                    $tr[] = $mtr;
                    $i++;
                }
            }
        }
        $table->addControl($tr);
        return array($table, $hidden);
    }

}

?>