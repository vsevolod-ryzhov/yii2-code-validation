<?php

declare(strict_types=1);

namespace vsevolodryzhov\yii2CodeValidation;

use ArrayAccess;
use yii\base\Model;

class Service
{
    const DATA_KEY = 'data';
    const CODE_KEY = 'code';
    const TIME_KEY = 'time';

    //step in seconds
    const TIME_STEP = 30;
    private $key;

    private $storage;

    private $data = null;

    private function getStorageData(): ?array
    {
        if ($this->data === null) {
            $this->data = $this->storage[$this->key];
        }

        return $this->data;
    }

    public function __construct(ArrayAccess $session, string $key = 'verify')
    {
        $this->storage = $session;
        $this->key = $key;
    }

    /**
     * Set form attributes to store in session and notify user about verify code
     * @param Model $form
     * @return string
     */
    public function set(Model $form): string
    {
        $code = StringHelper::generateCode();

        $this->storage[$this->key] = [
            self::DATA_KEY => $form->attributes,
            self::CODE_KEY => $code,
            self::TIME_KEY => time()
        ];
        return $code;
    }

    /**
     * Update verify code
     * @return Response
     */
    public function renewCode(): Response
    {
        if (!$this->exists()) {
            return new Response(false, 0, null);
        }
        $time = $this->getTime();
        if ($time + self::TIME_STEP > time()) {
            return new Response(false, ($time + self::TIME_STEP - time()), null);
        }

        $code = StringHelper::generateCode();
        $this->storage[$this->key] = [
            self::DATA_KEY => $this->getData(),
            self::CODE_KEY => $code,
            self::TIME_KEY => time()
        ];

        return new Response(true, self::TIME_STEP, $code);
    }

    public function exists(): bool
    {
        return isset($this->storage[$this->key]);
    }

    public function getCode(): ?string
    {
        $data = $this->getStorageData();
        return $data[self::CODE_KEY];
    }

    public function getData()
    {
        $data = $this->getStorageData();
        return $data[self::DATA_KEY];
    }

    public function clear(): void
    {
        unset($this->storage[$this->key]);
    }

    private function getTime(): ?int
    {
        $data = $this->getStorageData();
        return $data[self::TIME_KEY];
    }
}