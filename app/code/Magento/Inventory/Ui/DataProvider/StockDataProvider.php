<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Inventory\Ui\DataProvider;

use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\InventoryApi\Api\GetAssignedSourcesForStockInterface;
use Magento\Ui\DataProvider\SearchResultFactory;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider;
use Magento\InventoryApi\Api\Data\StockInterface;
use Magento\InventoryApi\Api\StockRepositoryInterface;

/**
 * Class StockDataProvider
 * @api
 */
class StockDataProvider extends DataProvider
{
    /**
     * @var StockRepositoryInterface
     */
    private $stockRepository;

    /**
     * @var SearchResultFactory
     */
    private $searchResultFactory;

    /**
     * @var GetAssignedSourcesForStockInterface
     */
    private $getAssignedSourcesForStock;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ReportingInterface $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param StockRepositoryInterface $stockRepository
     * @param SearchResultFactory $searchResultFactory
     * @param GetAssignedSourcesForStockInterface $getAssignedSourcesForStock
     * @param array $meta
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList) All parameters are needed for backward compatibility
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        StockRepositoryInterface $stockRepository,
        SearchResultFactory $searchResultFactory,
        GetAssignedSourcesForStockInterface $getAssignedSourcesForStock,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );
        $this->stockRepository = $stockRepository;
        $this->searchResultFactory = $searchResultFactory;
        $this->getAssignedSourcesForStock = $getAssignedSourcesForStock;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $data = parent::getData();
        if ('inventory_stock_form_data_source' === $this->name) {
            // It is need for support of several fieldsets.
            // For details see \Magento\Ui\Component\Form::getDataSourceData
            if ($data['totalRecords'] > 0) {
                $stockId = $data['items'][0][StockInterface::STOCK_ID];
                $stockGeneralData = $data['items'][0];
                $dataForSingle[$stockId] = [
                    'general' => $stockGeneralData,
                    'sources' => [
                        'assigned_sources' => $this->getAssignedSourcesData($stockId),
                    ],
                ];
                $data = $dataForSingle;
            } else {
                $data = [];
            }
        }
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchResult()
    {
        $searchCriteria = $this->getSearchCriteria();
        $result = $this->stockRepository->getList($searchCriteria);

        $searchResult = $this->searchResultFactory->create(
            $result->getItems(),
            $result->getTotalCount(),
            $searchCriteria,
            StockInterface::STOCK_ID
        );
        return $searchResult;
    }

    /**
     * @param int $stockId
     * @return array
     */
    private function getAssignedSourcesData($stockId)
    {
        $assignedSources = $this->getAssignedSourcesForStock->execute($stockId);

        $assignedSourcesData = [];
        foreach ($assignedSources as $assignedSource) {
            $assignedSourcesData[] = [
                SourceInterface::SOURCE_ID => $assignedSource->getSourceId(),
                SourceInterface::NAME => $assignedSource->getName(),
            ];
        }
        return $assignedSourcesData;
    }
}
