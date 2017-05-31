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

class PersistentOperand {

    public $operand;
    public $type;

    public function __construct($operand) {
        $this->operand = $operand;
        $this->type = '';
    }

    public function getSql() {
        return '';
    }

    public function getSqlWhere() {
        return $this->getSql();
    }

}

class OperandValue extends PersistentOperand {

    public function __construct($operand) {
        parent::__construct($operand);
        $this->type = 'value';
    }

    public function getSql() {
        $value = $this->operand;
        if ($value{0} != '?') {
            if ($value{0} == ':') {
                $value = substr($value, 1);
            } elseif (($value === '') || (strtolower($value) == 'null') || is_null($value)) {
                $value = 'null';
            } elseif ($value{0} != "'") {
                $value = "'" . addslashes($value) . "'";
            }
        }
        return $value;
    }

}

class OperandArray extends PersistentOperand {

    public function __construct($operand) {
        parent::__construct($operand);
        $this->type = 'array';
    }

    public function getSql() {
        $sql = "(";
        $i = 0;
        if (is_array($this->operand)){
            $list = '';
            foreach ($this->operand as $o) {
                $list .= ( $i++ > 0) ? ", " : "";
                $list .= "'$o'";
            }
            $sql .= (($list == '') ? "''" : $list);
        }else{
            $sql .= "'$this->operand'";
        }
        $sql .= ")";
        return $sql;
    }

}

class OperandAttributeMap extends PersistentOperand {

    public $attributeMap;
    public $alias = '';
    public $criteria;
    public $as;

    public function __construct($operand, $name) {
        parent::__construct($operand);
        $this->type = 'attributemap';
        if ($p = strpos($name, '.')) {
            $this->alias = substr($name, 0, $p);
        }
        $this->attributeMap = $operand;
    }

    public function getSql() {
        return $this->attributeMap->getColumnNameToDb($this->alias);
    }

    public function getSqlName() {
        return $this->attributeMap->getName();
    }

    public function getSqlOrder() {
        return $this->attributeMap->getFullyQualifiedName($this->alias);
    }

    public function getSqlWhere() {
        return $this->attributeMap->getFullyQualifiedName($this->alias);
        //return $this->attributeMap->getColumnWhereName($this->alias);
    }

    public function getSqlGroup() {
        return $this->attributeMap->getFullyQualifiedName($this->alias);
    }

}

class OperandCriteria extends PersistentOperand {

    public function __construct($operand, $criteria) {
        parent::__construct($operand);
        $this->criteria = $criteria;
        $this->type = 'criteria';
    }

    public function getSql() {
        /*
          $sql = $this->operand->getSqlStatement();
          $sql->setDb($this->operand->getManager()->getConnection($this->operand->getClassMap()->getDatabase()));
          return "(" . $sql->select() . ")";
         *
         */
        $this->operand->mergeAliases($this->criteria);
        return "(" . $this->operand->getSql() . ")";
    }

}

class OperandFunction extends PersistentOperand {

    public $argument;
    public $argOperand;

    public function __construct($operand, $criteria) {
        parent::__construct($operand);
        $this->type = 'public function';
        $str = $this->argument = $this->operand;
        $separator = "+-/*,()";
        $tok = strtok($str, $separator);
        while ($tok) {
            $t[$tok] = $tok;
            $tok = strtok($separator);
        }
        foreach ($t as $token) {
            $op = criteria::getOperand($token, $criteria);
            if (get_class($op) == 'OperandValue') {
                $op = criteria::getOperand(':' . $token, $criteria);
            }
            $this->argument = str_replace($token, $op->getSql(), $this->argument);
        }
    }

    public function getSql() {
        return $this->argument;
    }

    public function getSqlGroup() {
        return $this->argument;
    }

    public function getSqlOrder() {
        return $this->argument;
    }

}

class OperandNull extends PersistentOperand {

    public function __construct($operand) {
        parent::__construct($operand);
        $this->type = 'null';
    }

}

class OperandObject extends PersistentOperand {

    private $criteria;
    
    public function __construct($operand, $criteria) {
        parent::__construct($operand);
        $this->type = 'object';
        $this->criteria = $criteria;
    }

    public function getSql() {
        if (method_exists($this->operand, 'getSql')) {
            return $this->operand->getSql();
        } else { // se não existe o método getSql, acrescenta como parâmetro nomeado
            $name = uniqid('param_');
            $this->criteria->addParameter($this->operand,$name);
            return ':' . $name;
        }
    }

    public function getSqlWhere() {
        $platform = $this->criteria->getClassMap()->getPlatform();
        return $platform->convertWhere($this->operand);
    }
}

class OperandExpression extends PersistentOperand {

    public $argument;
    public $argOperand;

    public function __construct($criteria, $operand) {
        parent::__construct($criteria, $operand);
        $this->type = 'expression';
        $str = $this->argument = $this->operand;
        $separator = " ";
        $tok = strtok($str, $separator);
        while ($tok) {
            $t[$tok] = $tok;
            $tok = strtok($separator);
        }
        foreach ($t as $token) {
            $op = $criteria->getOperand($token);
            if (get_class($op) == 'OperandValue') {
                $op = $criteria->getOperand(':' . $token);
            }
            $this->argument = str_replace($token, $op->getSql(), $this->argument);
        }
    }

    public function getSql() {
        return $this->argument;
    }

    public function getSqlGroup() {
        return $this->argument;
    }

    public function getSqlOrder() {
        return $this->argument;
    }

}

class OperandString extends PersistentOperand {

    public function __construct($operand, $criteria) {
        parent::__construct($operand);
        $this->criteria = $criteria;
        $this->type = 'string';
    }

    public function getSql() {
        $value = $this->operand;
        $sql = '';
        $tokens = preg_split('/([\s()=]+)/', $value, -1, PREG_SPLIT_DELIM_CAPTURE);
        if (count($tokens)) {
            foreach ($tokens as $token) {
                $tk = $token;
                $am = $this->criteria->getAttributeMap($tk);
                if ($am instanceof AttributeMap) {
                    $o = new OperandAttributeMap($am, $tk, $this->criteria);
                    $newToken = $o->getSql();
                } else {
                    $tk = $token;
                    if (strrpos($tk, '\\') === false) {
                        $tk = $this->criteria->getClassMap()->getNamespace() . '\\' . $tk;
                    }
                    $cm = $this->criteria->getClassMap($tk);
                    if ($cm instanceof ClassMap) {
                        $newToken = $cm->getTableName();
                    } else {
                        $newToken = $token;
                    }
                }
                $sql .= $newToken;
            }
        } else {
            $sql = $value;
        }
        return $sql;
    }

    public function getSqlWhere() {
        $value = $this->operand;
        $sql = '';
        $tokens = preg_split('/([\s()=]+)/', $value, -1, PREG_SPLIT_DELIM_CAPTURE);
        if (count($tokens)) {
            foreach ($tokens as $token) {
                $tk = $token;
                if ((preg_match("/'(.*)/", $tk) == 0) && (preg_match("/(.*)'/", $tk) == 0)) {
                    $am = $this->criteria->getAttributeMap($tk);
                    if ($am instanceof AttributeMap) {
                        $o = new OperandAttributeMap($am, $tk);
                        $token = $o->getSqlWhere();
                    }
                }
                $sql .= $token;
            }
        } else {
            $sql = $value;
        }
        return $sql;
    }

    public function getSqlGroup() {
        $value = $this->operand;
        $sql = '';
        $tokens = preg_split('/([\s()=]+)/', $value, -1, PREG_SPLIT_DELIM_CAPTURE);
        if (count($tokens)) {
            foreach ($tokens as $token) {
                $tk = $token;
                $am = $this->criteria->getAttributeMap($tk);
                if ($am instanceof AttributeMap) {
                    $o = new OperandAttributeMap($am, $tk);
                    $token = $o->getSqlGroup();
                }
                $sql .= $token;
            }
        } else {
            $sql = $value;
        }
        return $sql;
    }

    public function getSqlOrder() {
        $value = $this->operand;
        $sql = '';
        $tokens = preg_split('/([\s()=]+)/', $value, -1, PREG_SPLIT_DELIM_CAPTURE);
        if (count($tokens)) {
            foreach ($tokens as $token) {
                $tk = $token;
                $am = $this->criteria->getAttributeMap($tk);
                if ($am instanceof AttributeMap) {
                    $o = new OperandAttributeMap($am, $tk);
                    $token = $o->getSqlOrder();
                }
                $sql .= $token;
            }
        } else {
            $sql = $value;
        }
        return $sql;
    }

}

class OperandStringAI extends OperandString {
    public function getSqlWhere() {
        return $this->getSql();
    }

    public function getSql() {
        $sql = parent::getSql();

        return "TRANSLATE( $sql, 'ÁÇÉÍÓÚÀÈÌÒÙÂÊÎÔÛÃÕËÜáçéíóúàèìòùâêîôûãõëü', 'ACEIOUAEIOUAEIOUAOEUaceiouaeiouaeiouaoeu')";

    }
}

