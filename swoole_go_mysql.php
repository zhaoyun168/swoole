<?php
 
class AysMysql{
 
    public $dbSource = "";
    public $dbConfig = [];
 
    public function __construct()
    {
        $this->dbSource = new Swoole\Coroutine\MySQL();
        $this->dbConfig = [
            'host' => '127.0.0.1',
            'port' => '3306',
            'user' => 'root',
            'password' => '123456zy',
            'database' => 'test',
            'charset' => 'utf8'
        ];
 
    }
 
    /**
     * @Notes:mysql执行
     * @Interface execute
     * @param $id
     * @param $username
     * @return bool
     * @Time: 2020/4/3   下午5:48
     */
    public function execute($id,$username){
        go(function () use($id){
            //connect
            $this->dbSource->connect($this->dbConfig);
            $sql = "select * from yb_users where id = ".$id;
            $res = $this->dbSource->query($sql);
            if($res === false){
                var_dump("error");
            }
            var_dump($res);
            $this->dbSource->close();
        });
    }
}
 
$obj = new AysMysql();
$obj->execute(1,'张三');

