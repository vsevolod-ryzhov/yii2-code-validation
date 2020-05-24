<?php

declare(strict_types=1);

namespace vsevolodryzhov\yii2CodeValidation;

use ArrayAccess;
use PHPUnit_Framework_TestCase;
use yii\base\ArrayAccessTrait;
use yii\base\Model;

class FakeSession implements ArrayAccess {
    use ArrayAccessTrait;
}

class Form extends Model
{
    public $attr1;
    public $attr2;
}

class ServiceTest extends PHPUnit_Framework_TestCase
{
    const ACCESS_KEY = 'testService';
    private $session;
    private $service;

    private function createFilledForm(): Model
    {
        $form = new Form;
        $form->attr1 = 1;
        $form->attr2 = 2;

        return $form;
    }

    public function setUp()
    {
        $this->session = new FakeSession();
        $this->service = new Service($this->session, self::ACCESS_KEY);
    }

    public function testCodeGenerateSuccess()
    {
        $form = $this->createFilledForm();
        $code = $this->service->set($form);
        $this->assertNotEmpty($code);
        $this->assertRegExp('/['.StringHelper::CODE_DICTIONARY.']+/', $code);
        $this->assertTrue(($code == $this->service->getCode()));
    }

    public function testStoreSuccess()
    {
        $form = $this->createFilledForm();
        $this->assertTrue(empty($this->session->data));
        $this->service->set($form);
        $this->assertNotEmpty($this->session);
        $data = $this->service->getData();
        $this->assertTrue(($form->attr1 == $data['attr1']));
        $this->assertTrue(($form->attr2 == $data['attr2']));
        $this->assertTrue(($form->attr1 == $this->session->data[self::ACCESS_KEY][Service::DATA_KEY]['attr1']));
        $this->assertTrue(($form->attr2 == $this->session->data[self::ACCESS_KEY][Service::DATA_KEY]['attr2']));
    }

}