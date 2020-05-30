<?php

declare(strict_types=1);


namespace vsevolodryzhov\yii2CodeValidation;


use yii\base\Model;

class CodeValidationForm extends Model
{
    /**
     * User input code to verify
     * @var $code
     */
    public $code;

    /**
     * Session stored code
     * @var $storedCode
     */
    private $storedCode;

    /**
     * @var Model
     */
    private $storedModel;

    public function __construct($storedCode, Model $storedModel, $config = [])
    {
        parent::__construct($config);
        $this->storedCode = $storedCode;
        $this->storedModel = $storedModel;
    }

    public function rules(): array
    {
        $rules = [];
        $rules[] = ['code', 'compare', 'compareValue' => $this->storedCode, 'skipOnEmpty' => false, 'message' => 'Wrong code'];
        return $rules;
    }

    public function attributeLabels(): array
    {
        return [
            'code' => 'Enter code'
        ];
    }
}