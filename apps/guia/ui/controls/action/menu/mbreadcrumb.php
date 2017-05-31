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

class MBreadCrumb extends MMenu {

    const SEPARATOR = '&raquo;&raquo;';

    public $labelHome = 'Home';
    public $home = '';
    public $separator = '';

    public function __construct($name = '') {
        parent::__construct($name);
    }

    public function onCreate() {
        parent::onCreate();
        $this->setRender('breadcrumb');
    }

    public function setLabelHome($label) {
        $this->labelHome = $label;
    }

    public function generateInner() {
        $ul = new MUnorderedList();

        if ($this->caption != '') {
            $ul->addOption($this->caption);
        }
        
        $separator = $this->separator ?: self::SEPARATOR;

        $options = $this->getControls();
        if ($this->home != '') {
            $link = new MLink('', $this->labelHome, $this->home);
            $ul->addOption($link->generate());
            $ul->addOption($separator);
        }

        $count = count($options);
        $i = 1;
        foreach ($options as $o) {
            if ($i++ < $count) {
                $ul->addOption($o->generate());
                $ul->addOption($separator);
            } else {
                $o->setClass('active');
                $ul->addOption($o->generate());
            }
        }
        $ul->setClass($this->getClass());
        $this->inner = $ul;
    }

}

?>
