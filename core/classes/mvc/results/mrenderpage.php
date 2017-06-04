<?php

class MRenderPage extends MResult
{

    public function __construct($content = '')
    {
        parent::__construct();
        if ($content != '') {
            $this->page->setContent($content);
        }
        $this->content = $this->page->render();
    }

    public function apply($request, $response)
    {
        $response->out = $this->content;
        $response->setHeader('Content-type', 'Content-type: text/html; charset=UTF-8');
    }

}
