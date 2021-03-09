<?php
namespace fast;

/**
 *
 */

class ZookeeperApi
{
    /**
     * @var Zookeeper
     */
    private $zookeeper;
    /**
     * @var Callback container
     */
    private $callback = array();
    /**
     * Constructor
     *
     * @param string $address CSV list of host:port values (e.g. "host1:2181,host2:2181")
     */
    public function __construct($address) {
        $this->zookeeper = new Zookeeper($address);
    }
    /**
     * Set a node to a value. If the node doesn't exist yet, it is created.
     * Existing values of the node are overwritten
     *
     * @param string $path  The path to the node
     * @param mixed  $value The new value for the node
     *
     * @return mixed previous value if set, or null
     */
    public function set($path, $value) {
        if (!$this->zookeeper->exists($path)) {
            $this->makePath($path);
            $this->makeNode($path, $value);
        } else {
            $this->zookeeper->set($path, $value);
        }
    }
    /**
     * Equivalent of "mkdir -p" on ZooKeeper
     *
     * @param string $path  The path to the node
     * @param string $value The value to assign to each new node along the path
     *
     * @return bool
     */
    public function makePath($path, $value = '') {
        $parts = explode('/', $path);
        $parts = array_filter($parts);
        $subpath = '';
        while (count($parts) > 1) {
            $subpath .= '/' . array_shift($parts);
            if (!$this->zookeeper->exists($subpath)) {
                $this->makeNode($subpath, $value);
            }
        }
    }
    /**
     * Create a node on ZooKeeper at the given path
     *
     * @param string $path   The path to the node
     * @param string $value  The value to assign to the new node
     * @param array  $params Optional parameters for the Zookeeper node.
     *                       By default, a public node is created
     *
     * @return string the path to the newly created node or null on failure
     */
    public function makeNode($path, $value, array $params = array()) {
        if (empty($params)) {
            $params = array(
                array(
                    'perms'  => Zookeeper::PERM_ALL,
                    'scheme' => 'world',
                    'id'     => 'anyone',
                )
            );
        }
        return $this->zookeeper->create($path, $value, $params);
    }
    /**
     * Get the value for the node
     *
     * @param string $path the path to the node
     *
     * @return string|null
     */
    public function get($path) {
        if (!$this->zookeeper->exists($path)) {
            return null;
        }
        return $this->zookeeper->get($path);
    }
    /**
     * List the children of the given path, i.e. the name of the directories
     * within the current node, if any
     *
     * @param string $path the path to the node
     *
     * @return array the subpaths within the given node
     */
    public function getChildren($path) {
        if (strlen($path) > 1 && preg_match('@/$@', $path)) {
            // remove trailing /
            $path = substr($path, 0, -1);
        }
        return $this->zookeeper->getChildren($path);
    }

    /**
     * Delete the node if it does not have any children
     *
     * @param string $path the path to the node
     *
     * @return true if node is deleted else null
     */

    public function deleteNode($path)
    {
        if(!$this->zookeeper->exists($path))
        {
            return null;
        }
        else
        {
            return $this->zookeeper->delete($path);
        }
    }

    /**
     * Wath a given path
     * @param string $path the path to node
     * @param callable $callback callback function
     * @return string|null
     */
    public function watch($path, $callback)
    {
        if (!is_callable($callback)) {
            return null;
        }

        if ($this->zookeeper->exists($path)) {
            if (!isset($this->callback[$path])) {
                $this->callback[$path] = array();
            }
            if (!in_array($callback, $this->callback[$path])) {
                $this->callback[$path][] = $callback;
                return $this->zookeeper->get($path, array($this, 'watchCallback'));
            }
        }
    }

    /**
     * Wath event callback warper
     * @param int $event_type
     * @param int $stat
     * @param string $path
     * @return the return of the callback or null
     */
    public function watchCallback($event_type, $stat, $path)
    {
        if (!isset($this->callback[$path])) {
            return null;
        }

        foreach ($this->callback[$path] as $callback) {
            $this->zookeeper->get($path, array($this, 'watchCallback'));
            return call_user_func($callback);
        }
    }

    /**
     * Delete watch callback on a node, delete all callback when $callback is null
     * @param string $path
     * @param callable $callback
     * @return boolean|NULL
     */
    public function cancelWatch($path, $callback = null)
    {
        if (isset($this->callback[$path])) {
            if (empty($callback)) {
                unset($this->callback[$path]);
                $this->zookeeper->get($path); //reset the callback
                return true;
            } else {
                $key = array_search($callback, $this->callback[$path]);
                if ($key !== false) {
                    unset($this->callback[$path][$key]);
                    return true;
                } else {
                    return null;
                }
            }
        } else {
            return null;
        }
    }
}



//    $zk = new ZookeeperApi('localhost:2181');
    //var_dump($zk->get('/zookeeper'));
//    var_dump($zk->getChildren('/foo'));

//    public function watcher($i, $type, $key) {
//        echo "Insider Watcher\n";
//        Watcher gets consumed so we need to set a new one
//        ZooKeeper提供了可以绑定在znode的监视器。如果监视器发现znode发生变化，该service会立即通知所有相关的客户端。这就是PH//P脚本如何知道变化的。Zookeeper::get方法的第二个参数是回调函数。\\
//        当触发事件时，监视器会被消费掉，所以我们需要在回调函 数中再次设置监视器。
//        $this->get($key, array($this, 'watcher' ) );
//    }
//    $ret = $zk->watch('/test', 'callback');

// var_dump($zk->get('/'));
// var_dump($zk->getChildren('/'));
// var_dump($zk->set('/test', 'abc'));
// var_dump($zk->get('/test'));
// var_dump($zk->getChildren('/'));
// var_dump($zk->set('/foo/001', 'bar1'));
// var_dump($zk->set('/foo/002', 'bar2'));
// var_dump($zk->get('/'));
// var_dump($zk->getChildren('/'));
// var_dump($zk->getChildren('/foo'));

    //$zk->set('/test', 1);
    //$zk->set('/test', 2);//在终端执行
//    while (true) {
//        sleep(1);
//    }
//

//    $zoo = new ZookeeperDemo('127.0.0.1:2181');
//    $zoo->get( '/test', array($zoo, 'watcher' ) );
//
//    while( true ) {
//      echo '.';
//      sleep(2);
//    }
