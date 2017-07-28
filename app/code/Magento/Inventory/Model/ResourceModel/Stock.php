<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Inventory\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\InventoryApi\Api\Data\StockInterface;

/**
 * Implementation of basic operations for Stock entity for specific db layer
 */
class Stock extends AbstractDb
{
    /**#@+
     * Constants related to specific db layer
     */
    const TABLE_NAME_STOCK = 'inventory_stock';
    /**#@-*/

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME_STOCK, StockInterface::STOCK_ID);
    }
}
