<?php
/**
*  swoole 数据库连接池 BY 凌晨
'worker_num' => 20, //worker进程数量
    'task_worker_num' => 10, //task进程数量 即为维持的MySQL连接的数量
    'daemonize'=> 1,          //设置守护进程
    'max_request' => 10000, //最大请求数,超过了进程重启
     'dispatch_mode' => 2,/
*/
class server_db_pool 
{    //swoole set params
    protected $task_worker_num;
    protected $work_num;
    protected $max_request;
    protected $dispatch_mode;  
    protected $daemonize;      
    protected $server_port;    
    protected $log_file;       
    //db params
    protected $db_host;
    protected $db_user;
    protected $db_pwd;
    protected $db_name;
    protected $db_port; 
    
    public function __construct()
    {
        $this->host =  "127.0.0.1"; // server监听的端口
        $this->server_port =  9508; // server监听的端口
        $this->worker_num = 5;
        $this->task_worker_num = 2;    
        $this->dispatch_mode = 2;    
        $this->daemonize = 0;    
        $this->max_request = 10000;    
        $filename=date("Y-m-d",time());
        $this->log_file = "/var/www/html/swoole_test/swoole.log";
        $this->serv = new swoole_server("127.0.0.1", $this->server_port);
        $this->serv->set( array(
            'worker_num'=>$this->worker_num,
            'task_worker_num' => $this->task_worker_num,
            'max_request' => $this->max_request,
            'daemonize' => $this->daemonize,
            'log_file' => $this->log_file,
            'dispatch_mode' => $this->dispatch_mode,
        ));
    }
    public function run(){
        $this->serv->on('Receive', array($this, 'onReceive'));
        // Task 回调的2个必须函数
        $this->serv->on('Task', array($this, 'onTask'));
        $this->serv->on('Finish', array($this, 'onFinish'));        
        $this->serv->start();
    }
    public function onReceive($serv, $fd, $from_id, $data){
         $result = $this->serv->taskwait($data);
            if ($result !== false) {
                $result=json_decode($result,true);
                if ($result['status'] == 'OK') {
                    $this->serv->send($fd, json_encode($result['data']) . "\n");
                } else {
                    $this->serv->send($fd, $result);
                }
                return;
            } else {
                $this->serv->send($fd, "Error. Task timeout\n");
            }
    }
    public function onTask($serv, $task_id, $from_id, $sql){
         static $link = null;
            HELL:
                if ($link == null) {
                    $link = @mysqli_connect("127.0.0.1", "root", "123456zy", "test");
                    if (!$link) {
                        $link = null;
                        $this->serv->finish("ER:" . mysqli_error($link));
                        return;
                    }   
                }   
            $result = $link->query($sql);
            if (!$result) { //如果查询失败了
                if(in_array(mysqli_errno($link), [2013, 2006])){//错误码为2013，或者2006，则重连数据库，重新执行sql
                        $link = null;
                        goto HELL;
                }else{
                    $this->serv->finish("ER:" . mysqli_error($link));
                    return;
                }
            }
            if(preg_match("/^select/i", $sql)){//如果是select操作，就返回关联数组
                 $data = array();
                    while ($fetchResult = mysqli_fetch_assoc($result) ){
                         $data['data'][]=$fetchResult;
                    }                
            }else{//否则直接返回结果
                $data['data'] = $result;
            }
            $data['status']="OK";
            $this->serv->finish(json_encode($data));
    }
    public function onFinish($serv, $task_id, $data){
             echo "任务完成";//taskwait  不触发这个函数。。
    }
}
$serv=new server_db_pool();
$serv->run();
