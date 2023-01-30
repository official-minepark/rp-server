<?php

namespace minepark\formsapi\elements;

interface ValidatableElement
{
    public function validateInput(mixed $data): bool;
}