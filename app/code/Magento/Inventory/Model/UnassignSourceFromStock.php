<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Inventory\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\InputException;
use Magento\Inventory\Model\ResourceModel\StockSourceLink as StockSourceLinkResourceModel;
use Magento\Inventory\Model\ResourceModel\StockSourceLink\Collection;
use Magento\Inventory\Model\ResourceModel\StockSourceLink\CollectionFactory;
use Magento\InventoryApi\Api\UnassignSourceFromStockInterface;
use Psr\Log\LoggerInterface;

/**
 * @inheritdoc
 */
class UnassignSourceFromStock implements UnassignSourceFromStockInterface
{
    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var CollectionFactory
     */
    private $stockLinkCollectionFactory;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var StockSourceLinkResourceModel
     */
    private $stockSourceLinkResource;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param CollectionProcessorInterface $collectionProcessor
     * @param CollectionFactory $stockLinkCollectionFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param StockSourceLinkResourceModel $stockSourceLinkResource
     * @param LoggerInterface $logger
     */
    public function __construct(
        CollectionProcessorInterface $collectionProcessor,
        CollectionFactory $stockLinkCollectionFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        StockSourceLinkResourceModel $stockSourceLinkResource,
        LoggerInterface $logger
    ) {
        $this->collectionProcessor = $collectionProcessor;
        $this->stockLinkCollectionFactory = $stockLinkCollectionFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->stockSourceLinkResource = $stockSourceLinkResource;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function execute($stockId, $sourceId)
    {
        if (!is_numeric($stockId) || !is_numeric($sourceId)) {
            throw new InputException(__('Input data is invalid'));
        }

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(StockSourceLink::STOCK_ID, (int)$stockId)
            ->addFilter(StockSourceLink::SOURCE_ID, $sourceId)
            ->create();

        /** @var Collection $collection */
        $collection = $this->stockLinkCollectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);
        $items = $collection->getItems();

        try {
            $stockSourceLink = reset($items);
            $this->stockSourceLinkResource->delete($stockSourceLink);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new CouldNotDeleteException(__('Could not delete Source Stock Link'), $e);
        }
    }
}
