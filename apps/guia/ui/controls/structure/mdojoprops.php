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

/*
 * Uma classe específica para as propriedades de um controle Dojo, porque o
 * atributo é na realidade um objeto JSON (as propriedades são separadas por
 * "," e não por ";" como nos atributos HTML) e os valores NULL devem ser exibidos.
 * ex:
 * data-dojo-props="region: 'left', splitter: true"
 */

class MDojoProps {

    public $dojoProps;

    public function __construct() {
        $this->dojoProps = array();
    }

    public function add($name, $value = NULL) {
        if ($name != '') {
            $this->dojoProps[$name] = htmlspecialchars($value,ENT_QUOTES);
        }
    }

    public function get($name) {
        $value = $this->dojoProps[$name];
        return $value;
    }

    private function encode($value) {
        return (substr($value, 0, 1) == '@' ? substr($value, 1) : MJSON::encode($value));
    }

    public function getProps() {
        $props = '';
        $i = 0;
        if (count($this->dojoProps)) {
            foreach ($this->dojoProps as $name => $value) {
                $props .= ($i++ ? ',' : '') . $name . ':' . $this->encode($value);
            }
        }
        return $props;
    }

}

?>