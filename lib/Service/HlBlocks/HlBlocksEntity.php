<?php

namespace Base\Module\Service\HlBlocks;


use Bitrix\Main\ORM\Query\Query;

interface HlBlocksEntity
{
    public function __construct(array $entityData);
    public function getQuery(): Query;
    public function getEntityCodeByUf(): string;
    public function getDataManagerClass(): string;
}