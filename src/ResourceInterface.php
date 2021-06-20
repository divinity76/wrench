<?php

namespace Wrench;

use Socket;

interface ResourceInterface
{
    public function getResourceId(): ?int;

    /**
     * @return resource|Socket|null
     */
    public function getResource();
}
