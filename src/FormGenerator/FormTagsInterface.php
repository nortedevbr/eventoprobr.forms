<?php

namespace nortedevbr\eventoprobr\forms\FormGenerator;

interface FormTagsInterface
{
    public function setIndex(int $index);
    public function getIndex(): int;
    public function getFields(): array;
}