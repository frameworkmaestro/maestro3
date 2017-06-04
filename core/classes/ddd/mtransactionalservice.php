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

abstract class MTransactionalService extends \MBaseService
{
    private $database;
    protected $databaseName;
    protected $transaction;

    public function __construct($databaseName)
    {
        parent::__construct();
        $this->databaseName = $databaseName;
    }

    public function getDatabase()
    {
        if (is_null($this->database)) {
            $this->database = Manager::getDatabase($this->databaseName);
        }
        return $this->database;
    }

    /**
     * Coloca a conexão indicada em estado de transação.
     */
    public function beginTransaction()
    {
        $this->transaction = $this->getDatabase()->beginTransaction();
    }

    public function commit()
    {
        $this->transaction->commit();
    }

    public function rollback()
    {
        $this->transaction->rollback();
    }

    public function execute($parameters)
    {
        $that = $this;
        return $this->transactional(function () use ($that, $parameters) {
            return $that->run($parameters);
        });
    }

    public function transactional(callable $operation)
    {
        $this->beginTransaction();
        try {
            $result = call_user_func($operation);
            $this->commit();
            return $result;
        } catch (Exception $e) {
            $this->rollBack();
            throw $e;
        }
    }

}
