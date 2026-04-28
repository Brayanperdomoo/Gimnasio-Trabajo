<?php
namespace App\Models;

abstract class BaseModel
{
    public function __construct(protected array $attributes = []) {}

    public function toArray(): array
    {
        return $this->attributes;
    }
}
