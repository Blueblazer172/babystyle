<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-search-sphinx
 * @version   1.0.26
 * @copyright Copyright (C) 2016 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\SearchSphinx\Model\Adapter;

use Mirasvit\SearchSphinx\SphinxQL\Expression as QLExpression;
use Mirasvit\SearchSphinx\SphinxQL\SphinxQL;
use Magento\Framework\Indexer\ScopeResolver\IndexScopeResolver;
use Magento\Framework\Search\Adapter\Mysql\DocumentFactory as MysqlDocumentFactory;
use Magento\Framework\Search\Adapter\Mysql\TemporaryStorageFactory;
use Magento\Framework\Search\Request\Query\BoolExpression as BoolQuery;
use Magento\Framework\Search\Request\QueryInterface as RequestQueryInterface;
use Magento\Framework\Search\RequestInterface;
use Mirasvit\Search\Model\IndexFactory;
use Mirasvit\SearchSphinx\Model\Adapter\Query\Builder\Match;
use Mirasvit\SearchSphinx\Model\Adapter\Query\QueryContainer;
use Mirasvit\SearchSphinx\Model\Adapter\Query\QueryContainerFactory;
use Mirasvit\SearchSphinx\Model\Engine;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class MapperQL
{
    /**
     * @param QueryContainerFactory   $queryContainerFactory
     * @param Match                   $matchBuilder
     * @param TemporaryStorageFactory $temporaryStorageFactory
     * @param IndexFactory            $indexFactory
     * @param IndexScopeResolver      $scopeResolver
     * @param Engine                  $engine
     * @param MysqlDocumentFactory    $documentFactory
     */
    public function __construct(
        QueryContainerFactory $queryContainerFactory,
        Match $matchBuilder,
        TemporaryStorageFactory $temporaryStorageFactory,
        IndexFactory $indexFactory,
        IndexScopeResolver $scopeResolver,
        Engine $engine,
        MysqlDocumentFactory $documentFactory
    ) {
        $this->queryContainerFactory = $queryContainerFactory;
        $this->matchBuilder = $matchBuilder;
        $this->temporaryStorage = $temporaryStorageFactory->create();
        $this->indexFactory = $indexFactory;
        $this->scopeResolver = $scopeResolver;
        $this->engine = $engine;
        $this->documentFactory = $documentFactory;
    }

    /**
     * @param RequestInterface $request
     * @return \Magento\Framework\DB\Ddl\Table
     * @throws \Exception
     */
    public function buildQuery(RequestInterface $request)
    {
        $searchIndex = $this->indexFactory->create()->load($request->getIndex());

        $weights = [];
        foreach ($searchIndex->getIndexInstance()->getAttributeWeights() as $attr => $weight) {
            $weights[$attr] = pow(2, $weight);
        }

        $indexName = $this->scopeResolver->resolve($request->getIndex(), $request->getDimensions());

        $sphinxQuery = $this->engine->getQuery()
            ->select(['id', new QLExpression('weight()')])
            ->from($indexName)
            ->limit(0, 10000)
            ->option('max_matches', 10000)
            ->option('field_weights', $weights)
            ->option('ranker', new QLExpression("expr('sum(word_count*user_weight) + bm25')"));
        //@todo http://habrahabr.ru/company/sphinx/blog/133790/

        $queryContainer = $this->queryContainerFactory->create(['request' => $request]);

        $sphinxQuery = $this->processQuery(
            $request->getQuery(),
            $sphinxQuery,
            BoolQuery::QUERY_CONDITION_MUST,
            $queryContainer
        );

        $sphinxQuery = $this->addDerivedQueries(
            $queryContainer,
            $sphinxQuery
        );

        $result = $sphinxQuery->execute();

        if (isset($_GET) && isset($_GET['debug'])) {
            echo $sphinxQuery->getCompiled();
            echo '<pre>' . print_r($result, true) . '</pre>';
        }

        $documents = [];

        foreach ($result as $item) {
            $raw = [
                [
                    'name'  => 'entity_id',
                    'value' => $item['id']
                ],
                [
                    'name'  => 'score',
                    'value' => $item['weight()']
                ]
            ];
            $documents[] = $this->documentFactory->create($raw);
        }

        $table = $this->temporaryStorage->storeDocuments($documents);

        return $table;
    }

    /**
     * @param RequestQueryInterface $query
     * @param SphinxQL              $select
     * @param  string               $conditionType
     * @param QueryContainer        $queryContainer
     * @return SphinxQL
     */
    protected function processQuery(
        RequestQueryInterface $query,
        SphinxQL $select,
        $conditionType,
        QueryContainer $queryContainer
    ) {
        switch ($query->getType()) {
            case RequestQueryInterface::TYPE_MATCH:
                $select = $queryContainer->addMatchQuery(
                    $select,
                    $query,
                    $conditionType
                );
                break;
            case RequestQueryInterface::TYPE_BOOL:
                $select = $this->processBoolQuery($query, $select, $queryContainer);
                break;
        }

        return $select;
    }

    /**
     * @param BoolQuery      $query
     * @param SphinxQL       $select
     * @param QueryContainer $queryContainer
     * @return SphinxQL
     */
    private function processBoolQuery(
        BoolQuery $query,
        SphinxQL $select,
        QueryContainer $queryContainer
    ) {

        $select = $this->processBoolQueryCondition(
            $query->getMust(),
            $select,
            BoolQuery::QUERY_CONDITION_MUST,
            $queryContainer
        );

        $select = $this->processBoolQueryCondition(
            $query->getShould(),
            $select,
            BoolQuery::QUERY_CONDITION_SHOULD,
            $queryContainer
        );

        $select = $this->processBoolQueryCondition(
            $query->getMustNot(),
            $select,
            BoolQuery::QUERY_CONDITION_NOT,
            $queryContainer
        );


        return $select;
    }

    /**
     * @param array          $subQueryList
     * @param SphinxQL       $select
     * @param string         $conditionType
     * @param QueryContainer $queryContainer
     * @return SphinxQL
     */
    private function processBoolQueryCondition(
        array $subQueryList,
        SphinxQL $select,
        $conditionType,
        QueryContainer $queryContainer
    ) {
        foreach ($subQueryList as $subQuery) {
            $select = $this->processQuery($subQuery, $select, $conditionType, $queryContainer);
        }

        return $select;
    }

    /**
     * @param QueryContainer $queryContainer
     * @param SphinxQL       $select
     * @return SphinxQL
     */
    private function addDerivedQueries(
        QueryContainer $queryContainer,
        SphinxQL $select
    ) {
        $matchQueries = $queryContainer->getMatchQueries();

        if ($matchQueries) {
            $matchContainer = array_shift($matchQueries);
            $select = $this->matchBuilder->build(
                $select,
                $matchContainer->getRequest(),
                $matchContainer->getConditionType()
            );
        }

        return $select;
    }
}
