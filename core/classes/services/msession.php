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

use Zend\Session;

class MSession extends Zend\Session\SessionManager {
    
    private $default;

    public function __get($var) {
        return $this->default->$var;
    }

    public function __set($var, $value) {
        $this->default->$var = $value;
    }
    
    public function init($sid = '') {
        try {
            if ($sid != '') {
                parent::setId($sid);
            } 
            parent::start();
            $this->default = $this->container('Manager');
            if (!$this->default->timestamp){
                $this->default->timestamp = time();
            }
        } catch (EMException $e) {
            throw $e;
        }
    }

    public function checkTimeout($exception = false) {
        $timeout = Manager::getConf('session.timeout');
        // If 0, we are not controling session
        if ($timeout != 0) {
            $timestamp = time();
            $difftime = $timestamp - $this->default->timestamp;
            $this->timeout = ($difftime > ($timeout * 60));
            $this->default->timestamp = $timestamp;
            if ($this->timeout) {
                $this->destroy();
                if ($exception) {
                    throw new ETimeOutException();
                } else {
                    return true;
                }    
            }
        }
        return false;
    }
    
    public function container($namespace){
        return new MSessionContainer($namespace);
    }

    public function get($var) {
        return $this->default->$var;
    }

    public function set($var, $value) {
        $this->default->$var = $value;
    }

    public function freeze() {
        $this->writeClose();
    }
    
    public function getValue($var){
        return $this->get($var);
    }
    
    public function setValue($var, $value){
        $this->set($var, $value);
    }

}

class MSessionContainer extends Zend\Session\Container {

    public function exists($value){
        return $this->offsetExists($value);
    }
}


?>