<?php

namespace sovngare\validators;

use yii\base\DynamicModel;
use yii\helpers\ArrayHelper;
use yii\validators\Validator;

class ArrayValidator extends Validator
{
    /** @var string|integer */
    public $key;
    /** @var string */
    public $label = 'attribute';
    /** @var array */
    public $rules;

    public function validateAttribute($model, $attribute)
    {
        /** @var array $field */
        $field = $model->$attribute;

        if (!is_array($field)) {
            $this->addError($model, $attribute, '{attribute} must be an array.');
            return;
        }

        if (isset($this->key) && isset($this->rules)) {
            $value = ArrayHelper::getValue($field, $this->key);

            $data = [];
            $data[$this->label] = $value;

            $dynamicModel = DynamicModel::validateData($data, $this->createRules());
            if (!$dynamicModel->validate()) {
                /** @var array $errors */
                $errors = $dynamicModel->getErrorSummary(false);
                $this->addError($model, $attribute, $errors[0]);
            } else {
                // Synchronizing data
                $value = $dynamicModel->attributes[$this->label];
                ArrayHelper::setValue($model->$attribute, $this->key, $value);
            }
        }
    }

    /** @return array */
    private function createRules()
    {
        $rules = array_map(function ($rule) {
            array_unshift($rule, $this->label);
            return $rule;
        }, $this->rules);
        return $rules;
    }
}
