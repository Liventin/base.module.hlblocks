<?php

namespace Base\Module\Src\HlBlocks;


use Base\Module\Exception\ModuleException;
use Base\Module\Service\HlBlocks\HlBlocksEntity as IHlBlocksEntity;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Entity\Base;
use Bitrix\Main\ORM\Query\Query;
use Exception;

class HlBlocksEntity implements IHlBlocksEntity
{
    private ?Base $entity = null;

    public function __construct(private readonly array $entityData)
    {
    }

    /**
     * @return Query
     * @throws ModuleException
     */
    public function getQuery(): Query
    {
        $this->prepareEntity();
        try {
            return new Query($this->entity);
        } catch (Exception $e) {
            throw new ModuleException($e->getMessage());
        }
    }

    /**
     * @return void
     * @throws ModuleException
     */
    private function prepareEntity(): void
    {
        if ($this->entity !== null) {
            return;
        }

        try {
            $this->entity = HighloadBlockTable::compileEntity($this->entityData);
        } catch (Exception $e) {
            throw new ModuleException($e->getMessage());
        }
    }

    /**
     * @return string
     */
    public function getEntityCodeByUf(): string
    {
        return 'HLBLOCK_'.$this->entityData['ID'];
    }

    /**
     * @return string
     * @throws ModuleException
     */
    public function getDataManagerClass(): string
    {
        $this->prepareEntity();

        try {
            return $this->entity->getDataClass();
        } catch (Exception $e) {
            throw new ModuleException($e->getMessage());
        }
    }
}