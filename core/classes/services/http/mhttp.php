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

class MStatusCode {

    const OK = 200;
    const CREATED = 201;
    const ACCEPTED = 202;
    const PARTIAL_INFO = 203;
    const NO_RESPONSE = 204;
    const MOVED = 301;
    const FOUND = 302;
    const METHOD = 303;
    const NOT_MODIFIED = 304;
    const BAD_REQUEST = 400;
    const UNAUTHORIZED = 401;
    const PAYMENT_REQUIERED = 402;
    const FORBIDDEN = 403;
    const NOT_FOUND = 404;
    const INTERNAL_ERROR = 500;
    const NOT_IMPLEMENTED = 501;
    const OVERLOADED = 502;
    const GATEWAY_TIMEOUT = 503;

    public static function success($code) {
        return $code / 100 == 2;
    }

    public static function redirect($code) {
        return $code / 100 == 3;
    }

    public static function error($code) {
        return $code / 100 == 4 || $code / 100 == 5;
    }
}


/**
 * An HTTP Header
 */
class MHeader {

    /**
     * Header name
     */
    public $name;
    /**
     * Header value
     */
    public $values;

    public function __construct($name, $value)
    {
        $this->name = $name;
        if (is_object($value))
        {
            $this->values = $value;
        }
        else
        {
            $this->values = new MStringList();
            $this->values->add($value);
        }
    }
            
    /**
     * First value
     * @return The first value
     */
    public function value() {
        return $this->values->get(0);
    }

    public function __toString() {
        return $this->values->getText();
    }
}

/**
 * An HTTP Cookie
 */
class MCookie {

    /**
     * Cookie name
     */
    public $name;
    /**
     * Cookie domain
     */
    public $domain;
    /**
     * Cookie path
     */
    public $path;
    /**
     * for HTTPS ?
     */
    public $secure = false;
    /**
     * Cookie value
     */
    public $value;
    /**
     * Cookie max-age
     */
    public $maxAge;
    /**
     * Don't use
     */
    public $sendOnError = false;
    /**
     * See http://www.owasp.org/index.php/HttpOnly
     */
    public $httpOnly = false;

    public function __construct()
    {
        $this->path = Manager::getHome() + '/';
    }

}

?>