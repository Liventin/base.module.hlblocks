<?php

namespace Base\Module\Service\HlBlocks;

interface HlBlocksService
{
    public const SERVICE_CODE = 'base.module.hlblocks.service';
    public function getByName(string $hlName): object;
}