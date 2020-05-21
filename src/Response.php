<?php

declare(strict_types=1);


namespace vsevolodryzhov\yii2CodeValidation;


class Response
{
    /**
     * @var bool
     */
    private $done;

    /**
     * @var int
     */
    private $wait;

    /**
     * @var ?string
     */
    private $code;

    public function __construct(bool $done, int $wait, ?string $code)
    {
        $this->done = $done;
        $this->wait = $wait;
        $this->code = $code;
    }

    public function getDone(): bool
    {
        return $this->done;
    }

    public function getWait(): int
    {
        return $this->wait;
    }

    /**
     * @return string
     */
    public function getCode(): ?string
    {
        return $this->code;
    }
}