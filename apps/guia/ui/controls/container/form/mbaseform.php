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

/**
 * Base class for form controls.
 * This class implements basic funcionality of all forms (registering/rendering of fields/buttons/validators
 * without a box and rendering of errors/info prompts).
 */
class MBaseForm extends MContainer {

    public $action;
    protected $method;
    protected $buttons;
    protected $fields;
    protected $return;
    protected $reset;
    protected $showHints = true;
    protected $enctype;
    protected $errors;
    protected $infos;
    protected $layout;
    protected $focus = '';
    protected $fieldLayout = MFieldLabel::LABEL_ABOVE;

    /**
     * obSubmit functions
     */
    protected $onSubmit;

    /**
     * id for HTML Form
     */
    protected $tagId;

    public function onCreate() {
        parent::onCreate();
        $this->setId('form' . uniqid());
        $this->tagId = $this->className . uniqid();
        $this->method = 'post';
        $this->return = false;
        $this->fields = new StdClass();
        $this->buttons = array();
        $this->onSubmit = new MStringList(false);
        $this->layout = array();
        $this->labelWidth = NULL;
    }

    public function onAfterCreate() {
        call_user_func_array(array('parent', 'onAfterCreate'), func_get_args());
        $this->setDisposition(MContainer::DISPOSITION_VERTICAL);
        if ($this->property->load) {
            $this->setData($this->property->load);
        }
    }

    public function onLoad() {
        $this->createFields();
    }

    public function __set($name, $value) {
        if ($name == 'form') {
            return;
        } elseif (strtolower($name) == 'onsubmit') {
            $this->onSubmit($value);
        }
        parent::__set($name, $value);
    }

    public function __get($name) {
        $value = $this->fields->$name;
        if ($value == NULL) {
            $value = $this->buttons[$name];
            if ($value == NULL) {
                $value = parent::__get($name);
            }
        }
        return $value;
    }

    public function createFields() {

    }

    public function getTagId() {
        return $this->tagId;
    }

    /**
     * Load data from object to form fields
     *
     * @param (object) $value
     */
    public function setLoad($value) {
        $this->property->load = $value;
    }

    public function addValidator(MValidator $validator) {
        $name = $validator->getField();
        $field = $this->fields->$name;
        if ($field instanceof MInputControl) {
            if ($validator->isActive()) {
                $field->setValidator($validator);
            }
        }
        return $name;
    }

    public function setValidators($validators) {
        if (is_array($validators)) {
            foreach ($validators as $v) {
                $this->addValidator($v);
            }
        } elseif ($validators instanceof MValidator) {
            $this->addValidator($validators);
        }
    }

    public function setFocus($fieldName) {
        $this->focus = $fieldName;
    }

    /**
     * Detects if a form has been submitted.
     *
     * @return (boolean) true if the form has been submitted otherwise false
     */
    public function isSubmitted() {
        if (isset($this->fields)) {
            $isSubmitted = false;
            $event = mrequest('__EVENTTARGETVALUE');
            foreach ($this->fields as $b) {
                $isSubmitted = $isSubmitted || (($b instanceof MButton) && mrequest($b->name)) || ($b->name == $event);
            }
        }
        return $isSubmitted;
    }

    /**
     * Adds JavaScript code which is to be executed, when the form is submitted.
     * When the form is generated, and any JS code has been registered using
     * this function, an <code>OnSubmit</code> handler is dynamically generated
     * where the code is placed.
     *
     * @param (string) $jscode Javascript code
     */
    public function onSubmit($jscode) {
        $this->onSubmit->add($jscode);
    }

    public function addJsCode($jscode) {
        $this->page->addJsCode($jscode);
    }

    public function addJsFile($file) {
        $path = pathinfo($this->view->viewFile, PATHINFO_DIRNAME) . '/' . $file;
        $this->page->addJsFile($path);
    }

    /**
     * Sets the action URL for this form. This is the URL to which the
     * form data will be submitted. .
     *
     * @param (string) $action URL of the action
     */
    public function setAction($action) {
        $this->action = $action;
    }

    /**
     * Obtain the object of form fields.
     *
     * @return (object) the object of form fields
     */
    public function getFields() {
        return $this->fields;
    }

    public function copyFields($fields) {
        $this->fields = $fields;
    }

    /**
     * This function is used to set the fields for the form from an array.
     *
     * @param (array) $fields Fields array
     */
    public function setFields($fields) {
        $this->clearFields();
        if (!is_array($fields)) {
            $fields = array($fields);
        }
        $this->addFields($fields);
    }

    public function addFields($fields) {
        if (is_string($fields)) {
            $fields = $this->getControlFromXml($fields);
        }
        if (!is_array($fields)) {
            $fields = array($fields);
        }
        foreach ($fields as $field) {
            if (is_array($field)) {
                $field = new MHContainer('', $field);
            }
            $this->addField($field);
        }
    }

    /**
     * Adds a single field to the list of form fields.
     *
     * @param (object) $field Form field object
     */
    public function addField($field) {
        if (is_string($field)) {
            $field = $this->getControlFromXml($field);
        }
        if ($this->getLabelWidth()) {
            $field->setLabelWidth($this->getLabelWidth());
        }
        $this->addControl($field);
        $this->_registerField($field);
    }

    /**
     * This function is used internally of the form framework to
     * <i>prepare</i> the form fields for the usage within the form
     * framework.
     *
     * @param $field (reference) to a single field or an array of fields
     *               If an array of fields is passed, the function is called
     *               recursively for each of the contained fields.
     *
     * @return (nothing)
     */
    private function _registerField($field) {
        if (is_array($field)) {
            for ($i = 0; $i < count($field); $i++) {
                $this->_registerField($field[$i]);
            }
        } else {
            $nameField = ($field->name == $field->id) ? $field->name : $field->id;
            ////mdump('register name = ' . $nameField);
            if ($nameField) {
                if (isset($this->fields->$nameField)) {
                    throw new EControlException("Err: field [$nameField] already in use in the form [$this->title]! Choose another name to [$nameField].");
                }
                $this->fields->$nameField = $field;
                if ($field instanceof MFormControl) {
                    //mdump('namefield=' . $nameField);
                    $field->load($this->data);
                    if ($this->property->readonly){
                        $field->setReadOnly($this->property->readonly);
                    }
                }
            }
            if (($field instanceof MFormControl) || ($field instanceof MBaseGrid)) {
                $field->setForm($this);
            }
            if ($field instanceof MFileField) {
                $this->setEnctype('multipart/form-data');
            }
            if ($field instanceof MContainerControl) {
                $this->_registerField($field->getControls());
            }
        }
    }

    public function clearFields() {
        $this->fields = new StdClass();
    }

    public function clearField($name) {
        if (isset($this->fields->$name)) {
            unset($this->fields->$name);
        }
    }

    public function getControlFromXml($xml, $type = 'fields') {
        $controls = new MXMLControls();
        $controls->loadString("<view><{$type}>{$xml}</{$type}></view>");
        return $controls->get($type);
    }

    public function setFieldsFromXML($file = 'fields.xml', $fileXML = '', $data = NULL, $folder = '') {
        if (count(func_get_args()) > 1) { // compatibilidade
            $path = Manager::getAppPath('', $file . '/views/' . ($folder ? $folder . '/' : '') . $fileXML . '.xml');
            $this->data->_xmlParams = $data;
        } else {
            $path = pathinfo($this->view->viewFile, PATHINFO_DIRNAME) . '/' . $file;
        }
        $this->clearFields();
        $this->addControlsFromXML($path);
    }

    public function addFieldsFromXML($file = 'fields.xml') {
        $path = pathinfo($this->view->viewFile, PATHINFO_DIRNAME) . '/' . $file;
        $this->addControlsFromXML($path);
    }

    public function insertFieldsFromXML($file = 'fields.xml') {
        $path = pathinfo($this->view->viewFile, PATHINFO_DIRNAME) . '/' . $file;
        $controls = $this->getControlsFromXML($path);
        $this->addFields($controls);
    }

    public function setFieldsFromXMLString($xmlString) {
        $controls = new MXMLControls();
        $controls->loadString($xmlString, $this->data);
        $this->setFields($controls->get('fields'));
        $this->setButtons($controls->get('buttons'));
        $this->setValidators($controls->get('validators'));
    }

    /**
     * Sets the form buttons.
     * This method adds buttons to the form, but first removes existing ones.
     *
     * @see addButton()
     *
     * @param (mixed) $buttons MButton object or array of MButtons
     */
    public function setButtons($buttons) {
        $this->clearButtons();
        $this->addButtons($buttons);
    }

    public function addButtons($buttons) {
        if (is_string($buttons)) {
            $buttons = $this->getControlFromXml($buttons, 'buttons');
        }
        if (is_array($buttons)) {
            foreach ($buttons as $button) {
                $this->addButton($button);
            }
        } else {
            $this->addButton($buttons);
        }
    }

    /**
     * Add button to the form.
     * This method adds a button to the form. Existing buttons will remaing unchanged.
     *
     * @see setButtons()
     * @see MButton
     *
     * @param (MButton) $btn Button object
     */
    public function addButton($button) {

        if (is_string($button)) {
            $button = $this->getControlFromXml($button, 'buttons');
        }
        if (is_array($button))
            $this->addButtons($button);
        else {
            $button->setForm($this);
            $name = $button->getId();
            $this->buttons[$name] = $button;
        }
    }

    /**
     * Remove existing buttons on the form.
     */
    public function clearButtons() {
        $this->buttons = array();
    }

    /**
     * Returns form fields list.
     * This is a placeholder function to bu the form's field list. It
     * is excpected, that the form returns a scalar list of all defined
     * fields which carry a form field value. Thus, form elements of
     * decorative purpose only should be omitted.
     * <br><br>
     * Derived classes such as <code>TabbedForm</code> override this
     * function to provide the list of fields independently of the form's
     * layout.
     *
     * @returns (array) a scalar array of form fields
     */
    public function getFieldList() {
        return $this->_getFieldList($this->fields);
    }

    /**
     * Returns field list.
     * Internal function which takes a list of form elements possibly
     * consisting of single fields as well as arrays and returns a scalar
     * the list of fields filtering out some known decorative form fields.
     *
     * @param  (array) $allfields An array of form fields
     * @return (array) A scalar array of form fields
     */
    private function _getFieldList($allfields) {
        $fields = array();
        foreach ($allfields as $field) {
            if ($field instanceof MFormControl) {
                if (!($field instanceof MOutputControl)) {
                    $fields[] = $field;
                }
            }
        }
        return $fields;
    }

    /**
     * Adds the related form error
     *
     * @param (mixed) $err Error message string or array of messages
     */
    public function addError($err) {
        if ($err) {
            if (is_array($err)) {
                if ($this->errors) {
                    $this->errors = array_merge($this->errors, $err);
                } else {
                    $this->errors = $err;
                }
            } else {
                $this->errors[] = $err;
            }
        }
    }

    /**
     *  Returns the number of error messages or 0 if no errors exist
     *
     * @return (integer) Error count
     */
    public function hasErrors() {
        return count($this->errors);
    }

    /**
     * Register an information related to the form
     *
     * @param (mixed) $info Information message string or array of messages
     */
    public function addInfo($info) {
        if ($info) {
            if (is_array($info)) {
                if ($this->infos) {
                    $this->infos = array_merge($this->infos, $info);
                } else {
                    $this->infos = $info;
                }
            } else {
                $this->infos[] = $info;
            }
        }
    }

    /**
     * Returns the number of info messages or 0, if no info exist
     *
     * @return (integer) Information messages count
     */
    public function hasInfos() {
        return count($this->infos);
    }

    /**
     * Get form data and put it into the classmembers
     */
    public function collectInput($data) {
        foreach ($this->getFieldList() as $field) {
            $name = $field->getName();
            if ($name != '') {
                $value = $field->getValue();
                $data->$name = $value;
            }
        }
        return $data;
    }

    /**
     * Obtains form fields in a FormData object.
     *
     * @return (Object) Form fields
     */
    public function getData() {
        $data = new StdClass();
        return $this->collectInput($data);
    }

    /**
     * Set data on the form fields.
     *
     * @param $object (MModel Object) object containing the field values
     */
    public function setData($data) {
        foreach ($this->fields as $field) {
            $name = $field->getName();
            if ($name) {
                if (strpos($name, '::') !== false) {
                    list($obj, $name) = explode('::', $name);
                    $rawValue = $data->{$obj}->{$name};
                } else {
                    $rawValue = $data->$name;
                }
                if (isset($rawValue) && ($field instanceof MFormControl)) {
                    if ($rawValue instanceof MCurrency) {
                        $value = $rawValue->getValue();
                    } else if ($rawValue instanceof MCPF || $rawValue instanceof MCNPJ) {
                        $value = $rawValue->getPlainValue();
                    } elseif (($rawValue instanceof MDate) || ($rawValue instanceof MTimestamp)) {
                        $value = $rawValue->format();
                    } else {
                        $value = $rawValue;
                    }

                    if($field instanceof MCheckControl) {
                        $field->setValue($value);
                        $field->check($value);
                    } else {
                        $field->setValue($value);
                    }
                }
            }
        }
    }

    /**
     * Compatibility: obtem um valor do request
     */
    public static function getFormValue($field) {
        return mrequest($field);
    }

    /**
     * Obtains a form field's value
     */
    public function getFieldValue($name) {
        $value = NULL;
        if ($field = $this->fields->$name) {
            $value = $field->getValue();
        }
        return $value;
    }

    /**
     * Set a form field's value
     */
    public function setFieldValue($name, $value) {
        if ($field = $this->fields->$name) {
            $field->setValue($value);
        }
    }

    /**
     * Get a reference for a form field
     */
    public function getField($name) {
        $value = NULL;
        if ($field = $this->fields->$name) {
            $value = $field;
        }
        return $value;
    }

    public function setMethod($method = 'POST') {
        $this->method = $method;
    }

    public function setEncType($enctype) {
        $this->enctype = $enctype;
    }

    public function generateErrors() {
        $prompt = MPrompt::error($this->errors, 'NONE', _M('Erros'));
        return $prompt;
    }

    public function generateInfos() {
        $prompt = MPrompt::information($this->infos, 'NONE', _M('Information'));
        //return $prompt;
        Manager::getFrontController()->getController()->renderPrompt($prompt);
    }

    public function generateBody() {
        $array = array();
        // optionally generate errors
        if ($this->hasErrors()) {
            $array[] = new MDiv('', $this->generateErrors());
        }
        if ($this->hasInfos()) {
            $array[] = new MDiv('', $this->generateInfos());
        }
        if ($this->action == '') {
            $this->action = Manager::getCurrentURL();
        }
        $array[] = $this->generateLayout();
        $array[] = $this->generateScript();
        $body = new MDiv('', $array, 'mFormBody');
        return $body;
    }

    public function generateFooter() {

    }

    public function generateButtons() {
        $buttons = [];
        if (count($this->buttons)) {
            foreach ($this->buttons as $button) {
                if ($button->visible) {
                    $buttons[] = $button;
                }
            }
        }
        if ($this->reset) {
            $buttons[] = new MButton('_reset', _M("Clear"), 'RESET');
        }
        if ($this->return) {
            $buttons[] = new MButton('_return', _M("Back"), 'RETURN');
        }
        $div = (count($buttons) ? new MHContainer('', $buttons) : NULL);
        return $div;
    }

    /**
     * Generate form specific script code
     */
    public function generateScript() {
        if ($this->focus != '') {
            $this->page->onLoad("manager.getElementById('{$this->focus}').focus();");
        }
    }

    public function generateInner() {
        $buttons = $this->generateButtons();
        $body = $this->generateBody();
        if (!is_null($this->bgColor)) {
            $body->addStyle('background-color', $this->bgColor);
        }
        $submit = (($o = $this->onSubmit->getValueText('', " &&\n    ")) ? $o : 'true');
        $errMsg = _M('Formulário com valores inválidos.');
        $onSubmit = "   if (form.validate()) {\n        result = {$submit};\n    }\n    else {\n        alert('{$errMsg}');\n        result = false;\n    }\n";
        $this->inner = array("submit" => $onSubmit, "body" => $body, "buttons" => $buttons);
    }

}

?>
