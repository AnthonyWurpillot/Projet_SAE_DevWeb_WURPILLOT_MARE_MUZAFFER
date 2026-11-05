<?php

declare(strict_types=1);

namespace iutnc\touiteur\action;

abstract class Action
{
    abstract public function execute(): string;
}
