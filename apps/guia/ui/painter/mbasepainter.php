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

class MBasePainter
{

    /** @var MPage */
    protected $page;
    /** @var MControl */
    protected $control;

    public function __construct()
    {
        $this->page = Manager::getPage();
    }

    protected function get($control)
    {
        $this->control = $control;

        return array(
            $this->getId(),
            $this->getName(),
            $this->getValue(),
            $this->getClass(),
            $control->getAttributes()
        );
    }

    protected function getAttributeValue($name, $value)
    {
        return $value ? " {$name}=\"{$value}\"" : "";
    }

    protected function getId()
    {
        return $this->getAttributeValue('id', $this->control->getId());
    }

    protected function getClass()
    {
        return $this->getAttributeValue('class', $this->control->getClass());
    }

    protected function getName()
    {
        return $this->getAttribute('name', $this->control->getName());
    }

    protected function getValue()
    {
        $value = $this->control->getValue();

        return $this->getAttribute('value', $this->control->sanitize($value));
    }

    protected function getAttribute($name, $value)
    {
        return " {$name}=\"{$value}\"";
    }

    public static function generateToString($element, $separator = '')
    {
        $html = '';

        if (is_array($element)) {
            foreach ($element as $e) {
                $html .= self::generateToString($e, $separator);
            }
        } elseif (is_object($element)) {
            if (method_exists($element, 'generate')) {
                $html = $element->generate() . $separator;
            } else {
                $html = "BasePainter Error: Method Generate not defined to " . get_class($element);
            }
        } else {
            $html = (string)$element;
        }

        return $html;
    }

}

?>