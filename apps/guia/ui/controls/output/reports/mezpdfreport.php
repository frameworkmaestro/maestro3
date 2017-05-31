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

class MCpdf extends Cpdf {
}

class MCezpdf extends Cezpdf {
    public $mfontFamilies;
    public $posX;
    public $poxY;
    public $textSize;
    public $width;

    public function __construct($paper = 'a4', $orientation = 'portrait') {
        parent::cezPDF($paper, $orientation);
        $this->pageWidth = $this->ez['pageWidth'];
        $this->pageHeight = $this->ez['pageHeight'];
        $this->initFontFamily();
    }

    public function initFontFamily() {
        $this->fontFamilies['arial.afm'] = array(
            'b' => 'arial-Bold.afm',
            'i' => 'arial-Italic.afm',
            'bi' => 'arial-BoldItalic.afm',
            'ib' => 'arial-BoldItalic.afm'
        );
        $this->fontFamilies['vera.afm'] = array(
            'b' => 'vera-Bold.afm',
            'i' => 'vera-Italic.afm',
            'bi' => 'vera-BoldItalic.afm',
            'ib' => 'vera-BoldItalic.afm'
        );
        $this->fontFamilies['veramono.afm'] = array(
            'b' => 'veramono-Bold.afm',
            'i' => 'veramono-Italic.afm',
            'bi' => 'veramono-BoldItalic.afm',
            'ib' => 'veramono-BoldItalic.afm'
        );
        $this->fontFamilies['verase.afm'] = array(
            'b' => 'verase-Bold.afm',
            'i' => 'verase.afm',
            'bi' => 'verase-Bold.afm',
            'ib' => 'verase-Bold.afm'
        );
        $this->fontFamilies['tahoma.afm'] = array(
            'b' => 'tahoma-Bold.afm',
            'i' => 'tahoma.afm',
            'bi' => 'tahoma-Bold.afm',
            'ib' => 'tahoma-Bold.afm'
        );
        $this->fontFamilies['Times.afm'] = array(
            'b' => 'Times-Bold.afm',
            'i' => 'Times-Italic.afm',
            'bi' => 'Times-BoldItalic.afm',
            'ib' => 'Times-BoldItalic.afm'
        );
        $this->fontFamilies['verdana.afm'] = array(
            'b' => 'verdana-Bold.afm',
            'i' => 'verdana-Italic.afm',
            'bi' => 'verdana-BoldItalic.afm',
            'ib' => 'verdana-BoldItalic.afm'
        );
    }

    public function callTrigger($trigger) {
        $method = array (
            $trigger[0],
            $trigger[1]
        );
        call_user_func($method, $trigger[2]);
    }

    public function setTrigger($trigger, $class, $module, $param) {
        $this->trigger[$trigger] = array (
            $class,
            $module,
            $param
        );
    }

    public function ezSetDy($dy, $mod = '') {
        parent::ezSetDy($dy, $mod);
        return $this->y;
    }

    public function ezNewPage() {
        if ($this->trigger['BeforeNewPage']) {
            $this->callTrigger($this->trigger['BeforeNewPage']);
        }
        parent::ezNewPage();
        if ($this->trigger['AfterNewPage']) {
            $this->callTrigger($this->trigger['AfterNewPage']);
        }
    }

    public function getWidthFromPercent($percent) {
        $total = $this->ez['pageWidth'] - $this->ez['leftMargin'] - $this->ez['rightMargin'];
        return $percent * $total / 100;
    }

    public function ezSetMargins($top, $bottom, $left, $right) {
        parent::ezSetMargins($top, $bottom, $left, $right);
        $this->top = $this->pageHeight - $this->ez['topMargin'];
        $this->bottom = $this->ez['bottomMargin'];
        $this->left = $this->ez['leftMargin'];
        $this->right = $this->pageWidth - $this->ez['rightMargin'];
        $this->width = $this->right - $this->left;
    }

    public function horizontalLine() {
        $this->line($this->left, $this->y, $this->right, $this->y);
    }

    public function posXY($x, $y) {
        $this->posX = $x;
        $this->posY = $y;
    }

    public function setTextSize($size) {
        $this->textSize = $size;
    }

    public function writeText($text, $size='') {
        $size = ($size != '' ? $size : $this->textSize);
        $this->addText($this->posX, $this->posY, $size, $text);
    }

    public function writeTextLn($text, $size='') {
        $size = ($size != '' ? $size : $this->textSize);
        $this->writeText($text, $size);
        $this->posY -= $size;
    }

    public function boxText($text, $size='', $colorForeground = array(1, 1, 1), $colorBackground = array(0, 0, 0)) {
        $size = ($size != '' ? $size : $this->textSize);
        $h = $this->getFontHeight($size);
        $this->SetColor($colorBackground[0], $colorBackground[1], $colorBackground[2]);
        $this->filledRectangle($this->posX, $this->posY - 4, $this->width, $h + 1);
        $this->SetColor($colorForeground[0], $colorForeground[1], $colorForeground[2]);
        $this->writeText($text, $size);
    }

}

class MEzPDFReport extends MReport {
    public $type;
    public $pdf;
    public $font;
    public $diff;
    public $fileOutput;

    public function __construct($type = '2', $orientation = 'portrait', $paper='a4') {
        $this->type = $type;
        $this->pdf = ($this->type == '1') ? new MCpdf() : new MCezpdf($paper, $orientation);
        $this->diff = array(
            ".notdef",
            ".notdef",
            ".notdef",
            ".notdef",
            ".notdef",
            ".notdef",
            ".notdef",
            ".notdef",
            ".notdef",
            ".notdef",
            ".notdef",
            ".notdef",
            ".notdef",
            ".notdef",
            ".notdef",
            ".notdef",
            ".notdef",
            ".notdef",
            ".notdef",
            ".notdef",
            ".notdef",
            ".notdef",
            ".notdef",
            ".notdef",
            ".notdef",
            ".notdef",
            ".notdef",
            ".notdef",
            ".notdef",
            ".notdef",
            ".notdef",
            ".notdef",
            "space",
            "exclam",
            "quotedbl",
            "numbersign",
            "dollar",
            "percent",
            "ampersand",
            "quoteright",
            "parenleft",
            "parenright",
            "asterisk",
            "plus",
            "comma",
            "minus",
            "period",
            "slash",
            "zero",
            "one",
            "two",
            "three",
            "four",
            "five",
            "six",
            "seven",
            "eight",
            "nine",
            "colon",
            "semicolon",
            "less",
            "equal",
            "greater",
            "question",
            "at",
            "A",
            "B",
            "C",
            "D",
            "E",
            "F",
            "G",
            "H",
            "I",
            "J",
            "K",
            "L",
            "M",
            "N",
            "O",
            "P",
            "Q",
            "R",
            "S",
            "T",
            "U",
            "V",
            "W",
            "X",
            "Y",
            "Z",
            "bracketleft",
            "backslash",
            "bracketright",
            "asciicircum",
            "underscore",
            "quoteleft",
            "a",
            "b",
            "c",
            "d",
            "e",
            "f",
            "g",
            "h",
            "i",
            "j",
            "k",
            "l",
            "m",
            "n",
            "o",
            "p",
            "q",
            "r",
            "s",
            "t",
            "u",
            "v",
            "w",
            "x",
            "y",
            "z",
            "braceleft",
            "bar",
            "braceright",
            "asciitilde",
            ".notdef",
            ".notdef",
            ".notdef",
            ".notdef",
            ".notdef",
            ".notdef",
            ".notdef",
            ".notdef",
            ".notdef",
            ".notdef",
            ".notdef",
            ".notdef",
            ".notdef",
            ".notdef",
            ".notdef",
            ".notdef",
            ".notdef",
            "dotlessi",
            "grave",
            "acute",
            "circumflex",
            "tilde",
            "macron",
            "breve",
            "dotaccent",
            "dieresis",
            ".notdef",
            "ring",
            "cedilla",
            ".notdef",
            "hungarumlaut",
            "ogonek",
            "caron",
            "space",
            "exclamdown",
            "cent",
            "sterling",
            "currency",
            "yen",
            "brokenbar",
            "section",
            "dieresis",
            "copyright",
            "ordfeminine",
            "guillemotleft",
            "logicalnot",
            "hyphen",
            "registered",
            "macron",
            "degree",
            "plusminus",
            "twosuperior",
            "threesuperior",
            "acute",
            "mu",
            "paragraph",
            "periodcentered",
            "cedilla",
            "onesuperior",
            "ordmasculine",
            "guillemotright",
            "onequarter",
            "onehalf",
            "threequarters",
            "questiondown",
            "Agrave",
            "Aacute",
            "Acircumflex",
            "Atilde",
            "Adieresis",
            "Aring",
            "AE",
            "Ccedilla",
            "Egrave",
            "Eacute",
            "Ecircumflex",
            "Edieresis",
            "Igrave",
            "Iacute",
            "Icircumflex",
            "Idieresis",
            "Eth",
            "Ntilde",
            "Ograve",
            "Oacute",
            "Ocircumflex",
            "Otilde",
            "Odieresis",
            "multiply",
            "Oslash",
            "Ugrave",
            "Uacute",
            "Ucircumflex",
            "Udieresis",
            "Yacute",
            "Thorn",
            "germandbls",
            "agrave",
            "aacute",
            "acircumflex",
            "atilde",
            "adieresis",
            "aring",
            "ae",
            "ccedilla",
            "egrave",
            "eacute",
            "ecircumflex",
            "edieresis",
            "igrave",
            "iacute",
            "icircumflex",
            "idieresis",
            "eth",
            "ntilde",
            "ograve",
            "oacute",
            "ocircumflex",
            "otilde",
            "odieresis",
            "divide",
            "oslash",
            "ugrave",
            "uacute",
            "ucircumflex",
            "udieresis",
            "yacute",
            "thorn",
            "ydieresis"
        );
        $fontsPath = Manager::getPublicPath('','','fonts/afm');
        $this->pdf->setFontsPath($fontsPath);
        $this->setFont('Helvetica.afm');
        parent::__construct();
    }
    
    public function __call($name, $args) {
        return $this->pdf->$name($args[0],$args[1],$args[2],$args[3],$args[4]);
    }    

    public function setFont($font) {
        $this->font = $font;
        $this->pdf->selectFont($this->font);
    }

    public function getPdf() {
        return $this->pdf;
    }

    public function setOutput($value = '') {
        if ($value != '') {
            $this->output = $value;
        } else {
            $this->output = ($this->type == '1') ? $this->pdf->output() : $this->pdf->ezOutput();
        }
    }

    public function getOutput() {
        if ($this->output == NULL) {
            $this->setOutput();
        }
        return $this->output;
    }

    public function text($text,$size = 0, $options = array(), $test = 0) {
        $text = utf8_decode($text);
        $this->pdf->ezText($text, $size, $options, $test);
    }

    public function newPage() {
        $this->pdf->ezNewPage();
    }

    public function getY() {
        return $this->pdf->y;
    }

    public function execute() {
        $pdfCode = $this->getOutput();
        $fileName = uniqid(md5(uniqid("")))  . '.pdf';
        $this->fileOutput = Manager::getFilesPath($fileName, true);
        $fp = fopen($this->fileOutput, 'x');
        fwrite($fp, $pdfCode);
        fclose($fp);
        $output =\Manager::getDownloadURL('report',  $fileName, true);
        return $output;
    }

}

?>
