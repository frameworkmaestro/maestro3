<?php

/* Copyright [2011, 2012, 2013] da Universidade Federal de Juiz de Fora
 * Este arquivo é parte do programa Framework Maestro.
 * O Framework Maestro é um software livre; vocę pode redistribuí-lo e/ou 
 * modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada 
 * pela Fundaçăo do Software Livre (FSF); na versăo 2 da Licença.
 * Este programa é distribuído na esperança que possa ser  útil, 
 * mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇĂO a qualquer
 * MERCADO ou APLICAÇĂO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL 
 * em portuguęs para maiores detalhes.
 * Vocę deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título
 * "LICENCA.txt", junto com este programa, se năo, acesse o Portal do Software
 * Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a 
 * Fundaçăo do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA
 * 02110-1301, USA.
 */

interface IUser {

    public function getId();

    public function getName();

    public function getById($id);

    public function getByLogin($login);

    public function getByLoginPass($login, $pass);

    public function getRights();

    public function getTransactionRights($transaction);

    public function getArrayGroups();
}

?>
