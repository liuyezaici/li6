<?php
namespace fast;

class Worker extends Zookeeper {

    const CONTAINER = '/cluster';

    protected $acl = array(
        array(
            'perms' => Zookeeper::PERM_ALL,
            'scheme' => 'world',
            'id' => 'anyone' ) );

    private $isLeader = false;

    private $znode;

    public function __construct( $host = '', $watcher_cb = null, $recv_timeout = 10000 ) {
        parent::__construct( $host, $watcher_cb, $recv_timeout );
    }

    public function register() {
        if( ! $this->exists( self::CONTAINER ) ) {
            $this->create( self::CONTAINER, null, $this->acl );
        }

        $this->znode = $this->create( self::CONTAINER . '/w-',
            null,
            $this->acl,
            Zookeeper::EPHEMERAL | Zookeeper::SEQUENCE );
        $this->znode = str_replace( self::CONTAINER .'/', '', $this->znode );
        printf( "I'm registred as: %s\n", $this->znode );
        $watching = $this->watchPrevious();
        if( $watching == $this->znode ) {
            printf( "Nobody here, I'm the leader\n" );
            $this->setLeader( true );
        }
        else {
            printf( "I'm watching %s\n", $watching );
        }
    }

    public function watchPrevious() {
        $workers = $this->getChildren( self::CONTAINER );
        sort( $workers );
        $size = sizeof( $workers );
        for( $i = 0 ; $i < $size ; $i++ ) {
            if( $this->znode == $workers[ $i ] ) {
                if( $i > 0 ) {
                    $this->get( self::CONTAINER . '/' . $workers[ $i - 1 ], array( $this, 'watchNode' ) );
                    return $workers[ $i - 1 ];
                }

                return $workers[ $i ];
            }
        }

        throw new Exception(  sprintf( "Something went very wrong! I can't find myself: %s/%s",
            self::CONTAINER,
            $this->znode ) );
    }

    public function watchNode( $i, $type, $name ) {
        $watching = $this->watchPrevious();
        if( $watching == $this->znode ) {
            printf( "I'm the new leader!\n" );
            $this->setLeader( true );
        }
        else {
            printf( "Now I'm watching %s\n", $watching );
        }
    }

    public function isLeader() {
        return $this->isLeader;
    }

    public function setLeader($flag) {
        $this->isLeader = $flag;
    }

    public function run() {
        $this->register();

        while( true ) {
            if( $this->isLeader() ) {
                $this->doLeaderJob();
            }
            else {
                $this->doWorkerJob();
            }

            sleep( 2 );
        }
    }

    public function doLeaderJob() {
        echo "Leading\n";
    }

    public function doWorkerJob() {
        echo "Working\n";
    }

}

// $worker = new Worker( '127.0.0.1:2181' );
// $worker->run();

// 打开至少3个终端，在每个终端中运行以下脚本：
// php worker.php

//现在模拟Leader崩溃的情形。使用Ctrl+c或其他方法退出第一个脚本。刚开始不会有任何变化，worker可以继续工作。后来，ZooKeeper会发现超时，并选举出新的leader。
//
//虽然这些脚本很容易理解，但是还是有必要对已使用的Zookeeper标志作注释：
//$this->znode = $this->create( self::CONTAINER . '/w-',
//    null,
//    $this->acl,
//    Zookeeper::EPHEMERAL | Zookeeper::SEQUENCE );
//每个znode都是EPHEMERAL和SEQUENCE的。
//
//EPHEMRAL代表当客户端失去连接时移除该znode。这就是为何PHP脚本会知道超时。SEQUENCE代表在每个znode名称后添加顺序标识。我们通过这些唯一标识来标记worker。
//
//在PHP部分还有些问题要注意。该扩展目前还是beta版，如果使用不当很容易发生segmentation fault。比如，不能传入普通函数作为回调函数，传入的必须为方法。我希望更多PHP社区的同仁可以看到Apache ZooKeeper的好，同时该扩展也会获得更多的支持。
//
//ZooKeeper是一个强大的软件，拥有简洁和简单的API。由于文档和示例都做的很好，任何人都可以很容易的编写分布式软件。让我们开始吧，这会很有趣的。