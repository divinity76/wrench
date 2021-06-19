<?php

namespace Wrench;

interface ResourceInterface
{
    public function getResourceId(): int;

    /**
     * @return resource
     */
    public function getResource();
}
