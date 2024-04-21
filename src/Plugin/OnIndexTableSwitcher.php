<?php declare(strict_types=1);

namespace MateuszMesek\AvoidRenameTableOnInventoryMultiDimensionalIndexer\Plugin;

use Magento\InventoryMultiDimensionalIndexerApi\Model\IndexName;
use Magento\InventoryMultiDimensionalIndexerApi\Model\IndexNameResolverInterface;
use Magento\InventoryMultiDimensionalIndexerApi\Model\IndexTableSwitcher;
use MateuszMesek\DatabaseDataTransfer\Api\Command\TransferDataInterface;
use MateuszMesek\DatabaseDataTransfer\Api\Data\TableFactoryInterface;

class OnIndexTableSwitcher
{
    public function __construct(
        private readonly IndexNameResolverInterface $indexNameResolver,
        private readonly TableFactoryInterface $tableFactory,
        private readonly TransferDataInterface $transferData
    ) {
    }

    public function aroundSwitch(
        IndexTableSwitcher $indexTableSwitcher,
        callable $proceed,
        IndexName $indexName,
        string $connectionName
    ): void {
        $tableName = $this->indexNameResolver->resolveName($indexName);

        $this->transferData->execute(
            $this->tableFactory->create($tableName),
            $this->tableFactory->create($tableName.'_replica')
        );
    }
}
