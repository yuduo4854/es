<?php

namespace App\Service;

use Elastic\Elasticsearch\ClientBuilder;

class EsService
{
    //ES客户端链接
    private $client;

    /**
     * 构造函数
     * MyElasticsearch constructor.
     */
    public function __construct()
    {
        $this->client = ClientBuilder::create()->setHosts(['127.0.0.1:9200'])->build();
    }


    /**
     * 添加ES数据
     * @param $id
     * @param $doc
     * @param string $indexName
     * @return mixed
     */
    public function addDoc($id, $doc, $indexName = 'goods')
    {
        $params = [
            'index' => $indexName,  //索引名称 类似于数据库名称
            'type' => "_doc",
            'id' => $id, //数据id
            'body' => $doc //数据内容 (上传时需注意 是否是数组形式，否则添加的数据容易混乱)
        ];
        $response = $this->client->index($params);
        return $response;
    }

    /**
     * 更新文档
     * @param $id
     * @param string $indexName
     * @param array $body
     * @return mixed
     */
    public  function updateDoc($id, $indexName = 'test_ik', $body = [])
    {
// 可以灵活添加新字段,最好不要乱添加
        $params = [
            'index' => $indexName,  //索引名称 类似于数据库名称
            'type' => "_doc",
            'id' => $id,        //需要更改的id
            'body' => $body   //更改的内容 （注：与添加格式一样）
        ];
        $response = $this->client->update($params);

        return $response;
    }


    /**
     * 高亮查询
     * @param string $indexName
     * @param string $searchKey
     * @param array $body
     */
    public function searchDoc($indexName = "goods", $searchKey = "goods", $body = [])
    {
        $params = [
            'index' => $indexName,  //索引名称 类似于数据库名称
            'type' => '_doc',
            'body' => [
                'query' => [
                    'match' => [
                        'name' => $searchKey  //前面是字段名称  后面是要搜索的数据 (字段名称 要跟查询 的字段名称一致)
                    ]
                ],
                'highlight'=>[
                    'fields'=>[
                        'name' => [   //要搜索的字段名称
                            'pre_tags'=>"<em style='color: red'>",
                            'post_tags'=>"</em>",
                        ]
                    ]
                ]
            ]
        ];
        //通过这里面的 hits 找到数据
        $result = $this->client->search($params)['hits']['hits'];

        //将高亮显示的数据  与 搜索到的内容进行替换
        foreach ($result as &$val) {
            $val['_source']['highlight'] = $val['highlight']['name'][0];
            $val = $val['_source'];
        }
        //返回给 前端
        return $result;
    }

    /**
     * 数据删除
     * @param int $id
     * @param string $indexName
     * @return mixed
     */
    public function deleteDoc($id = 1, $indexName = 'test_ik')
    {
        $params = [
            'index' => $indexName,
            'type' => "_doc",
            'id' => $id
        ];
        $response = $this->client->delete($params);
        return $response;
    }
}