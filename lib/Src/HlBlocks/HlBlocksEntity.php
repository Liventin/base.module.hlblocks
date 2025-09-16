<?php

namespace Base\Module\Src\HlBlocks;


use Base\Module\Service\HlBlocks\HlBlocksEntity as IHlBlocksEntity;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Entity\Base;
use Bitrix\Main\ORM\Query\Query;
use Bitrix\Main\SystemException;

class HlBlocksEntity implements IHlBlocksEntity
{
    private ?Base $entity = null;

    public function __construct(private readonly array $entityData)
    {
    }

    /**
     * @throws SystemException
     * @throws ArgumentException
     */
    public function getQuery(): Query
    {
        $this->prepareEntity();
        return new Query($this->entity);
    }

    /**
     * @throws SystemException
     */
    private function prepareEntity(): void
    {
        if ($this->entity !== null) {
            return;
        }

        $this->entity = HighloadBlockTable::compileEntity($this->entityData);
    }

    /**
     * @return string
     */
    public function getEntityCodeByUf(): string
    {
        return 'HLBLOCK_'.$this->entityData['ID'];
    }
}