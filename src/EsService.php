<?php

namespace App\Service;

use Elastic\Elasticsearch\ClientBuilder;

class EsService
{
    //ES客户端链接
    private static $client;

    /**
     * 构造函数
     * MyElasticsearch constructor.
     */
    public function __construct()
    {
        $this->client = ClientBuilder::create()->setHosts(['127.0.0.1:9200'])->build();
    }

     /**
     * 添加文档
     * @param $id
     * @param $doc ['id'=>100, 'title'=>'phone']
     * @param string $index_name
     * @param string $type_name
     * @小鱼儿 array
     */
    public static function addDoc($id, $doc, $indexName = 'test_ik')
    {
        $params = [
            'index' => $indexName,
            'type' => "_doc",
            'id' => $id,
            'body' => $doc
        ];
        $response = self::$client->index($params);
        return $response;
    }
    /**
     * 更新文档
     * @param int $id
     * @param string $index_name
     * @param string $type_name
     * @param array $body ['doc' => ['title' => '苹果手机iPhoneX']]
     * @小鱼儿 array
     */
    public static function updateDoc($id, $indexName = 'test_ik', $body = [])
    {
// 可以灵活添加新字段,最好不要乱添加
        $params = [
            'index' => $indexName,
            'type' => "_doc",
            'id' => $id,
            'body' => $body
        ];
        $response = self::$client->update($params);
        return $response;
    }


   //根据关键字查询
    public static function searchDoc($indexName = "goods", $searchKey = "goods", $body = [])
    {
        $params = [
            'index' => $indexName,
//'type' => '_doc',
            'body' => [
                'query' => [
                    'match' => [
                        'name' => $searchKey
                    ]
                ],
                'highlight' => [
                    'pre_tags' => ["<em>"],
                    'post_tags' => ["</em>"],
                    'fields' => [
                        "name" => new \stdClass()
                    ]
                ]
            ]
        ];
//偏移
        $params["size"] = 10;
        $params["from"] = 1;
        $results = self::$client->search($params);
        ($results);
    }

    /**
     删除数据
     */
    public static function deleteDoc($id = 1, $indexName = 'test_ik')
    {
        $params = [
            'index' => $indexName,
            'type' => "_doc",
            'id' => $id
        ];
        $response = self::$client->delete($params);
        return $response;
    }
}