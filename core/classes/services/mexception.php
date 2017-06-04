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

interface IException
{
    /* Protected methods inherited from Exception class */
    public function getMessage();                 // Exception message

    public function getCode();                    // User-defined Exception code

    public function getFile();                    // Source filename

    public function getLine();                    // Source line

    public function getTrace();                   // An array of the backtrace()

    public function getTraceAsString();           // Formated string of trace

    /* Overrideable methods inherited from Exception class */
    public function __toString();                 // formated string for display
}

abstract class MException extends Exception implements IException
{

    protected $message = 'Exceção desconhecida';     // Exception message
    private $string;                            // Unknown
    protected $code = 0;                       // User-defined exception code
    protected $file;                              // Source filename of exception
    protected $line;                              // Source line of exception
    protected $trace;                             // TraceStack

    public function __construct($message = NULL, $code = 0)
    {
        if (!$message) {
            $message = $this->message . get_class($this);
        }
        parent::__construct($message, $code);
    }

    public function __toString()
    {
        return get_class($this) . " '{$this->message}' em {$this->file}({$this->line})\n"
            . "{$this->getTraceAsString()}";
    }

}

class EMException extends MException
{

    public $goTo;

    public function __construct($msg = null, $code = 0, $goTo = '')
    {
        parent::__construct($msg, $code);
        $this->goTo = $goTo;
    }

    public function getGoTo()
    {
        return $this->goTo;
    }

    public function log()
    {
        Manager::logError($this->message);
    }

}

class ERuntimeException extends EMException
{
    public function __construct($msg = null, $code = 0, $goTo = '')
    {
        parent::__construct($msg, $code);
        $this->goTo = $goTo;
        $this->message = $msg;
    }

}

class ENotFoundException extends EMException
{

    public function __construct($msg = null, $code = 0)
    {
        parent::__construct($msg, $code);
        $this->message = $msg;
    }

}


class EInOutException extends EMException
{

}

class EDataNotFoundException extends ENotFoundException
{

    public function __construct($msg = null, $code = 0)
    {
        parent::__construct(_M('No Data Found!') . ($msg ? $msg : ''));
    }

}

class EControlException extends EMException
{

    public function __construct($msg = null, $code = 0)
    {
        parent::__construct($msg, $code);
        $this->message = $msg;
        $this->trace = mtracestack();
    }

}

class EFileNotFoundException extends ENotFoundException
{

    public function __construct($fileName, $msg = '')
    {
        parent::__construct(_M('@1 File not found: @2', 'manager', $msg, $fileName));
        $this->log();
    }

}

class ESessionException extends EMException
{

    public function __construct($msg = null, $code = 0)
    {
        parent::__construct($msg, $code);
        $this->message = _M('Error in Session: ') . $msg;
        $this->log();
    }

}

class EBusinessException extends EMException
{

    public function __construct($msg = null, $code = 0)
    {
        parent::__construct($msg, $code);
        $this->message = _M('Error in getBusiness: ') . $msg;
        $this->log();
    }

}

class EModelException extends EMException
{

    public function __construct($msg = null, $code = 0)
    {
        parent::__construct($msg, $code);
        //$this->message = _M('Erro no Modelo: ') . $msg;
        $this->log();
    }

}

class EControllerException extends EMException
{

    public function __construct($msg = null, $code = 0)
    {
        parent::__construct($msg, $code);
        //$this->message = _M('Erro na execução da ação: ') . $msg;
        $this->log();
    }

}

class ETimeOutException extends EMException
{

    public function __construct($msg = null, $code = 0)
    {
        parent::__construct($msg, $code);
        $this->message = _M('Session finished by timeout.') . $msg;
        $this->log();
    }

}

class ELoginException extends ERuntimeException
{

    public function __construct($msg = null, $code = 0)
    {
        parent::__construct(_M($msg), $code, Manager::getAppURL('', 'main'));
        $this->log();
    }

}

class ESecurityException extends EMException
{

    public function __construct($msg = null, $code = 0)
    {
        parent::__construct($msg, $code);
        $this->message = $msg;
    }

}

class ERepositoryException extends EMException
{

    public function __construct($msg = null, $code = 0)
    {
        parent::__construct($msg, $code);
        $this->message = $msg;
    }

}

class EDataValidationException extends EMException
{
    public function __construct($msg, $code = 0)
    {
        parent::__construct($msg, $code);
    }
}
