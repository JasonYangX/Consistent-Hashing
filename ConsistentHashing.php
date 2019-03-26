
<?php
/**
 * 一致性哈希算法实例
 **/
class ConsistentHash
{
    //节点
    public $node = [];

    //虚拟节点数
    public $replicete = 3;

    //缓存点
    public $cachePosition = [];

    public function __construct($replicate = 3)
    {
        $this->replicate = $replicate;
    }
    /**
     * 使用crc32哈希函数生成key
     **/
    public function hashFunc($key)
    {
        return (int)sprintf('%u',crc32($key));
    }

    /**
     * 添加节点
     **/
    public function addNode($node)
    {
        for ($i = 1; $i <= $this->replicate; $i++) {
            $positionKey = $this->hashFunc($node.$i);
            $this->node[$node][] = $positionKey;
            $this->cachePosition[$positionKey] = $node;
        }
        return true;
    }
    /**
     * 删除节点
     **/
    public function delNode($node)
    {
        $invalidPosition = $this->node[$node];
        unset($this->node[$node]);
        foreach ($invalidPosition as $item) {
            unset($this->cachePosition[$item]);
        }
        return true;
    }

    /**
     * 查询键
     **/
    public function lookUp($key)
    {
        $key = $this->hashFunc($key);
        ksort($this->cachePosition);

        foreach ($this->cachePosition as $position => $node) {
            if ($key <= $position) {
                return $node;
            }
        }
        return current($this->cachePosition);
    }

    public function showAll()
    {
        echo 'node: '.PHP_EOL;
        print_r($this->node);
        echo 'cachePosition: '.PHP_EOL;
        print_r($this->cachePosition);
    }
}

$Server = new ConsistentHash(3);

$Server->addNode('192.168.1.1');
$Server->addNode('192.168.1.2');

$Server->showAll();
