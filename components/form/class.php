<?php

namespace local\components\olof\form;

use Jugger\Form\Handler\MultiHandler;
use Jugger\Form\Form;

/**
 * Виджет формы
 *
 * @author Ilya Rupasov <i.rpsv@live.com>
 */
class Component extends \CBitrixComponent
{
    /**
     * Данные формы
     * @var \Jugger\Form\Form
     */
    public $form;

    public function executeComponent() {
        $this->initForm();
        $this->initValidators();
        $this->processForm();
        $this->initResult();
        $this->includeComponentTemplate();
    }

    public function initForm() {
        $p = $this->arParams;
        $fields = isset($p['fields']) ? $p['fields'] : [];
        $props = isset($p['props']) ? $p['props'] : [];
        $attributes = array_merge($fields, $props);
        $attributes = array_map(function($name) {
            return compact("name");
        }, $attributes);
        //
        $this->form = new Form($attributes);
        if (isset($p['formId'])) {
            $this->form->id = $p['formId'];
        }
    }

    public function initValidators() {
        $validators = isset($this->arParams['validators']) ? $this->arParams['validators'] : [];
        foreach ($validators as $name => $valids) {
            $this->form->attributes[$name]->addValidators($valids);
        }
    }
    
    public function processForm() {
        $data = $_POST;
        if ($this->form->id) {
            $data = $_POST[$this->form->id];
        }
        if ($this->form->load($data) && $this->form->validate()) {
            $this->initHandlers();
            $result = $this->form->handle();
            if ($result) {
                $this->arResult['success'] = isset($this->arParams['successMessage'])
                    ? $this->arParams['successMessage']
                    : "Форма успешно отправлена!";
            }
            else {
                $this->arResult['fail'] = $result;
            }
        }
    }

    public function initResult() {
        $this->arResult['form'] = $this->form;
    }

    public function initHandlers() {
        $handler = new MultiHandler();
        $handlers = $this->arParams['handlers'];
        foreach ($handlers as $name => $params) {
            if ($name === "custom") {
                $className = $params['class'];
                if (isset($params['params'])) {
                    $params = $params['params'];
                }
                else {
                    $params = [];
                }
            }
            else {
                $className = '\Jugger\Form\Handler\\'.$name.'Handler';
            }
            $class = new \ReflectionClass($className);
            $handler->add($class->newInstance($params));
        }
        $this->form->handler = $handler;
    }
}