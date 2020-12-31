<?php

class Server
{
    private $serv;

    public function __construct() {
        $this->serv = new swoole_server("0.0.0.0", 9501);
        $this->serv->set(array(
            'worker_num' => 8,
            'daemonize' => true,
            'max_request' => 10000,
            'dispatch_mode' => 2,
            'debug_mode'=> 1,
            'task_worker_num' => 8,
            'log_file' => '/var/www/html/swoole_test/task.log',
        ));

        $this->serv->on('Start', array($this, 'onStart'));
        $this->serv->on('Connect', array($this, 'onConnect'));
        $this->serv->on('Receive', array($this, 'onReceive'));
        $this->serv->on('Close', array($this, 'onClose'));
        // bind callback
        $this->serv->on('Task', array($this, 'onTask'));
        $this->serv->on('Finish', array($this, 'onFinish'));
        $this->serv->start();
    }

    public function onStart( $serv ) {
        echo "开始异步任务\n";
    }

    public function onConnect( $serv, $fd, $from_id ) {
        echo "客户端ID {$fd} 已连接\n";
    }

    public function onReceive( swoole_server $serv, $fd, $from_id, $data ) {
        echo "获取到信息来源于客户端{$fd}信息为：{$data}\n";
        // send a task to task worker.
        $param = array(
        	'fd' => $fd
        );
        $serv->task( json_encode( $param ) );

        echo "开始处理线程\n";
    }

    public function onClose( $serv, $fd, $from_id ) {
        echo "客户端ID {$fd} 连接断开\n";
    }

    public function onTask($serv,$task_id,$from_id, $data) {
    	echo "任务ID {$task_id} 来源于线程ID {$from_id}\n";
    	echo "数据为: {$data}\n";
    	for($i = 0 ; $i < 10 ; $i ++ ) {
    		sleep(1);
    		echo "Taks {$task_id} Handle {$i} times...\n";
    	}
        $fd = json_decode( $data , true )['fd'];
    	$serv->send( $fd , "Data in Task {$task_id}");
    	return "任务ID {$task_id} 的结果";
    }

    public function onFinish($serv,$task_id, $data) {
    	echo "Task {$task_id} finish\n";
    	echo "Result: {$data}\n";
    }
}

$server = new Server();
