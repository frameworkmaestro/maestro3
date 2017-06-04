<?php

/* Copyright [2011, 2013, 2017] da Universidade Federal de Juiz de Fora
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

class MModel
{

    /**
     * Constroi o objeto e inicializa seus atributos com $data.
     * @param mixed $data
     */
    public function __construct($data = NULL)
    {
        if (is_object($data)) {
            $this->setData($data);
        } elseif (is_numeric($data)) {
            $this->setId($data);
        }
    }

    /**
     * Recebe um ValueObject com valores planos e inicializa os atributos do Model.
     * @param object $data
     */
    public function setData($data)
    {
        if (is_object($data)) {
            foreach (get_object_vars($data) as $attribute => $value) {
                $setMethod = "set" . ucfirst($attribute);
                if (method_exists($this, $setMethod)) {
                    $this->$setMethod($value);
                }
            }
        }
    }

    private function setId($id)
    {
        if (is_numeric($id)) {
            $idAttribute = $this->getIdAttributeName();
            $setIdMethod = "set" . ucfirst($idAttribute);
            if (method_exists($this, $setIdMethod)) {
                $this->$setIdMethod($id);
            }
        }
    }

    /**
     * @return string
     */
    private function getIdAttributeName()
    {
        $reflect = new ReflectionClass($this);
        return "id" . $reflect->getShortName();
    }

    /**
     * Verifica se dois objetos sao iguais comparando seus ids
     * @param mixed $object
     * @return bool
     */
    public function equals($object)
    {
        $idAttribute = $this->getIdAttributeName();
        $getIdMethod = "get" . ucfirst($idAttribute);

        return is_object($object) && get_class($this) == get_class($object) && $this->$getIdMethod() == $object->$getIdMethod();
    }

    public function validate()
    {

    }
}
