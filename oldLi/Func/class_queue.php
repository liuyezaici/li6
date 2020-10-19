<?php
/**
 *  php队列算法
 *
 *  Create On 2014-8-21
 *  Author rui
 * 作用：防止对一个数据同时读取和修改。
 * 条件：必须使用全局的缓存：memcache 来控制 [不能用session] 所以必须要保证memcache的正常使用。
 **/
class queue{
    protected $front;//队头
    protected $rear;//队尾
    protected $queue=array();//存储队列
    protected $maxsize;//最大数
    protected $cacheName;//缓存名字，针对某个数据单独处理

    public function __construct($size, $cacheName =''){
        if(!$cacheName) {
            echo 'no cache name';
            exit;
        }
        $this->initQ($size, $cacheName);
    }
    //初始化队列
    private function initQ($size, $cacheName){
        $cache = new Cache();
        //获取memcache里的旧数据：已有队列数量
        $oldqueen = $cache->Get($cacheName);
        if(!$oldqueen) {
            $oldqueen = array();
            $len = 0;
        } else {
            $len = count($oldqueen);
        }
        $this->queue = $oldqueen;
        $this->front = $len;
        $this->rear=0;
        $this->maxsize = $size;
        $this->cacheName = $cacheName;
    }
    //判断队空
    public function QIsEmpty(){
        return $this->front==$this->rear;
    }
    //判断队满
    public function QIsFull(){
        return ($this->front - $this->rear) >= $this->maxsize;
    }
    //获取队首数据
    public function getFrontDate(){
        return $this->queue[$this->front];
    }
    //获取所有队员
    public function getAllItem(){
        return $this->queue;
    }
    //入队
    public function InQ($data){
        if($this->QIsFull()) {
            return false; //这坑已经有人了 禁止再进入
        } else {
            $this->front++;
            for($i = $this->front; $i > $this->rear; $i--){
                if($this->queue[$i]) {
                    unset($this->queue[$i]);
                }
                $this->queue[$i] = $this->queue[$i-1];
            }
            $this->queue[$this->rear+1] = $data;
            $cache = new Cache();
            $cache->add($this->cacheName, $this->queue, 100000);//占坑，10秒内禁止任何人在去处理这个缓存的数据
            return true;
        }
    }
    //出队
    public function OutQ(){
        if($this->QIsEmpty()) {
            echo "cache is empty,can not clear";
        } else{
            unset($this->queue[$this->front]);
            $this->front--;
            $cache = new Cache();
            $cache->add($this->cacheName, $this->queue, 1);
        }
    }
}
//使用方法：
//$cfg_cache = $GLOBALS['cfg']['queue_data']['1'];
//$queue= new queue($cfg_cache['max'], $cfg_cache['tag']);
//echo "当前队员：";
//print_r($queue->getAllItem());
//echo "<hr>";
//$queue->OutQ(); //退出队列