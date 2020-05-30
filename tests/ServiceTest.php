<?php

declare(strict_types=1);

namespace vsevolodryzhov\yii2CodeValidation;

use ArrayAccess;
use PHPUnit_Framework_TestCase;
use ReflectionClass;
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
        $reflection = new ReflectionClass(StringHelper::class);
        if (isset($reflection)) {
            $constants = $reflection->getConstants();
        }
        $form = $this->createFilledForm();
        $code = $this->service->set($form);
        $this->assertNotEmpty($code);
        $this->assertRegExp('/['.$constants['CODE_DICTIONARY'].']+/', $code);
        $this->assertSame($code, $this->service->getCode());
    }

    public function testStoreSuccess()
    {
        $reflection = new ReflectionClass(Service::class);
        $constants = $reflection->getConstants();

        $form = $this->createFilledForm();
        $this->assertTrue(empty($this->session->data));
        $this->service->set($form);
        $this->assertNotEmpty($this->session);
        $data = $this->service->getData();
        $this->assertSame($form->attr1, $data['attr1']);
        $this->assertSame($form->attr2, $data['attr2']);
        $this->assertSame($form->attr1, $this->session->data[self::ACCESS_KEY][$constants['DATA_KEY']]['attr1']);
        $this->assertSame($form->attr2, $this->session->data[self::ACCESS_KEY][$constants['DATA_KEY']]['attr2']);
    }

    public function testCodeLength()
    {
        $form = $this->createFilledForm();

        for ($i = 3; $i < 10; $i++) {
            $service = new Service($this->session, self::ACCESS_KEY, 30, $i);
            $code = $service->set($form);
            $this->assertSame($i, strlen($code));
        }
    }
}