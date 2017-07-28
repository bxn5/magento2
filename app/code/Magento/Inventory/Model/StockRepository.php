<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Inventory\Model;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Inventory\Model\Stock\Command\DeleteByIdInterface;
use Magento\Inventory\Model\Stock\Command\GetInterface;
use Magento\Inventory\Model\Stock\Command\GetListInterface;
use Magento\Inventory\Model\Stock\Command\SaveInterface;
use Magento\InventoryApi\Api\Data\StockInterface;
use Magento\InventoryApi\Api\StockRepositoryInterface;

/**
 * @inheritdoc
 */
class StockRepository implements StockRepositoryInterface
{
    /**
     * @var SaveInterface
     */
    private $commandSave;

    /**
     * @var GetInterface
     */
    private $commandGet;

    /**
     * @var DeleteByIdInterface
     */
    private $commandDeleteById;

    /**
     * @var GetListInterface
     */
    private $commandGetList;

    /**
     * @param SaveInterface $commandSave
     * @param GetInterface $commandGet
     * @param DeleteByIdInterface $commandDeleteById
     * @param GetListInterface $commandGetList
     */
    public function __construct(
        SaveInterface $commandSave,
        GetInterface $commandGet,
        DeleteByIdInterface $commandDeleteById,
        GetListInterface $commandGetList
    ) {
        $this->commandSave = $commandSave;
        $this->commandGet = $commandGet;
        $this->commandDeleteById = $commandDeleteById;
        $this->commandGetList = $commandGetList;
    }

    /**
     * @inheritdoc
     */
    public function save(StockInterface $stock)
    {
        return $this->commandSave->execute($stock);
    }

    /**
     * @inheritdoc
     */
    public function get($stockId)
    {
        return $this->commandGet->execute($stockId);
    }

    /**
     * @inheritdoc
     */
    public function deleteById($stockId)
    {
        $this->commandDeleteById->execute($stockId);
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null)
    {
        return $this->commandGetList->execute($searchCriteria);
    }
}
