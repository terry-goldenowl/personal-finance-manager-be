<?php

namespace App\Http\Helpers;

abstract class BaseData
{
    public function __construct(
        protected string $message,
        protected string $status
    ) {
    }

    abstract public function get(): array;
}
