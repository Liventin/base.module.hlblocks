<?php

namespace Base\Module\Src\HlBlocks;


use Base\Module\Exception\ModuleException;
use Base\Module\Service\Container;
use Base\Module\Service\HlBlocks\HlBlocksService as IHlBlocksService;
use Base\Module\Service\LazyService;
use Base\Module\Service\Tool\TagCacheService;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\ORM\Query\Query;
use Bitrix\Main\SystemException;
use Exception;

#[LazyService(serviceCode: IHlBlocksService::SERVICE_CODE, constructorParams: [])]
class HlBlocksService implements IHlBlocksService
{
    private static ?array $hlEntities = null;

    /**
     * @param string $hlName
     * @return object
     * @throws ModuleException
     */
    public function getByName(string $hlName): object
    {
        try {
            $this->prepareHlEntities();

            if (!array_key_exists($hlName, self::$hlEntities)) {
                throw new ModuleException('High load Block not found by name ' . $hlName);
            }

            if (!array_key_exists('OBJ', self::$hlEntities[$hlName])) {
                self::$hlEntities[$hlName]['OBJ'] = new HlBlocksEntity(self::$hlEntities[$hlName]);
                unset(
                    self::$hlEntities[$hlName]['ID'],
                    self::$hlEntities[$hlName]['NAME'],
                    self::$hlEntities[$hlName]['TABLE_NAME']
                );
            }

            return self::$hlEntities[$hlName]['OBJ'];
        } catch (Exception $e) {
            throw new ModuleException($e->getMessage());
        }
    }

    /**
     * @return void
     * @throws ArgumentException
     * @throws LoaderException
     * @throws SystemException
     * @throws ModuleException
     * @throws ObjectPropertyException
     */
    private function prepareHlEntities(): void
    {
        if (self::$hlEntities !== null) {
            return;
        }

        /** @var TagCacheService $cache */
        $cache = Container::get(TagCacheService::SERVICE_CODE);

        $hlList = $cache->getFromCache(86400, 'hlblocks_list');

        if ($hlList === null) {
            Loader::requireModule('highloadblock');

            /** @var Query $query */
            $query = HighloadBlockTable::query();

            $rows = $query
                ->addSelect('ID')
                ->addSelect('NAME')
                ->addSelect('TABLE_NAME')
                ->fetchAll();

            foreach ($rows as $row) {
                self::$hlEntities[$row['NAME']] = $row;
            }

            $cache->saveInCache(self::$hlEntities);
        } else {
            self::$hlEntities = $hlList;
        }
    }
}