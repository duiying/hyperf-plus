<?php

namespace HyperfPlus\Elasticsearch;

use Hyperf\Elasticsearch\ClientBuilderFactory;
use Hyperf\Utils\ApplicationContext;

/**
 * Elasticsearch 封装类
 *
 * @author duiying <wangyaxiandev@gmail.com>
 * @package HyperfPlus\Elasticsearch
 */
class ElasticSearch
{
    public $esClient;

    public function __construct()
    {
        $builder = ApplicationContext::getContainer()->get(ClientBuilderFactory::class)->create();
        $this->esClient = $builder->setHosts(config('databases.elasticsearch.hosts'))->build();
    }
}