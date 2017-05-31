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

class MNotFound extends MResult {

    public function apply($request, $response) {
        $response->status = MStatusCode::NOT_FOUND;
        $format = $request->format;
        $response->setContentType($response->getMimeType("xx." + $format));
        try {
            $template = new MTemplate();
            $template->context('result', $this);
            $errorHtml = MTemplateLocator::fetch($template, 'errors', '404.html');//$template->fetch("errors/{$language}/404.html");
            if ($request->isAjax()) {
                $this->ajax->setResponse('html',$errorHtml);
                $response->out = $this->ajax->returnData(); 
            } else {
                $response->out = $errorHtml;
            }
        } catch (EMException $e) {
            
        }
    }
}

?>