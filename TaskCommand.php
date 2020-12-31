<?php
/**
 * swoole异步任务
 */
namespace app\schedule\controller;

use platform\common\LoggerClient;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\console\input\Argument;
use think\console\input\Option;
use think\Db;

class TaskCommand extends Command
{
    /** @var config */
    private $config;
    /** @var db config */
    private $dbConfig;
    //monolog object
    private $_instance;
    //monolog instance
    private $logger;
    //db object
    private $db;

    //处理重试次数
    private $handle_num = 0;
    //最大处理重试次数
    private $max_handle_num = 3;

    private $serv;

    /**
     * 配置命令
     */
    protected function configure()
    {
        $this->setName('task:run')
             ->setDescription('asynchronous task command');
    }

    /**
     * swoole协程
     * @param  Input  $input  输入
     * @param  Output $output 输出
     * @return
     */
    protected function execute(Input $input, Output $output)
    {
        //日志配置
        if (!($this->_instance instanceof LoggerClient)) {
            $this->_instance = new LoggerClient();
        }
        $this->logger = $this->_instance->getMonolog('update', BASE_ROOT . 'log/schedule/task_'.date('Y-m-d').'.log', 0);

        /** mysql数据库的配置 */
        $this->dbConfig = require BASE_ROOT . '/config/database.php'; //数据库配置

        $this->db = $this->getDbConnection($this->dbConfig);

        //开启异步任务服务
        $this->start();

        return 0;
    }

    /**
     * 连接数据库（重连机制）
     * @return object
     */
    private function getDbConnection($dbConfig)
    {
        $try_times = 0; //重连次数
        $conn = null;

        while (true) {
            ++$try_times;

            try {
                $conn = Db::connect($dbConfig);

                break;
            } catch (\Exception $e) {
                $this->logger->error(sprintf('[%s]第[%s]次连接数据库失败，错误为[%s]', $this->_instance->uniqid, $try_times, $e->getMessage()));
                sleep(2);
                continue;
            }
        }

        return $conn;
    }

    /**
     * 开启异步任务服务
     * @return
     */
    private function start()
    {
        $this->logger->info(sprintf('[%s]开启异步任务服务...', $this->_instance->uniqid));

        $this->serv = new \swoole_server("0.0.0.0", 9501);  
        $this->serv->set(array(  
            'worker_num' => 1, //一般设置为服务器CPU数的1-4倍  
            'daemonize' => 0, //以守护进程执行  
            'max_request' => 10000,  
            'dispatch_mode' => 2,  
            'task_worker_num' => 8, //task进程的数量  
            "task_ipc_mode " => 3, //使用消息队列通信，并设置为争抢模式  
            "log_file" => "/var/www/html/swoole_test/task_test.log" ,//日志  
        ));  
        $this->serv->on('Receive', array($this, 'onReceive'));  
        // bind callback  
        $this->serv->on('Task', array($this, 'onTask'));  
        $this->serv->on('Finish', array($this, 'onFinish'));  
        $this->serv->start();
    }

    public function onReceive(\swoole_server $serv, $fd, $from_id, $data)  
    {  
        $this->logger->info(sprintf('[%s]Get Message From Client %s:%s', $this->_instance->uniqid, $fd, $data));
        // send a task to task worker.  
        $serv->task($data);  
    }  
  
    public function onTask($serv, $task_id, $from_id, $data)  
    {  
        $array = json_decode($data, true);  
        if ($array['url']) {  
            return $this->httpGet($array['url'], $array['param']);  
        }  
    }  
  
    public function onFinish($serv, $task_id, $data)  
    {  
        $this->logger->info(sprintf('[%s]Task %s finish', $this->_instance->uniqid, $task_id));
        $this->logger->info(sprintf('[%s]Result: %s', $this->_instance->uniqid, $data));

        $insert_data = [
            'result' => $data,
            'time' => time(),
        ];

        try {
            $this->db->name('http_result')->insert($insert_data);    
        } catch (\Exception $e) {
            $this->logger->error(sprintf('[%s]操作数据库异常[%s]', $this->_instance->uniqid, $e->getMessage()));
        }
    }

    protected function httpGet($url, $data)  
    {  
        if ($data) {  
            $url .= '?' . http_build_query($data);  
        }  
        $curlObj = curl_init(); //初始化curl，  
        curl_setopt($curlObj, CURLOPT_URL, $url); //设置网址  
        curl_setopt($curlObj, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($curlObj, CURLOPT_TIMEOUT, 5);
        curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, 1); //将curl_exec的结果返回  
        curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, FALSE);  
        curl_setopt($curlObj, CURLOPT_SSL_VERIFYHOST, FALSE);  
        curl_setopt($curlObj, CURLOPT_HEADER, 0); //是否输出返回头信息  
        $response = curl_exec($curlObj); //执行  
        if (curl_errno($curlObj)) {
            echo sprintf('curl请求异常[%s]', curl_error($curlObj));
            
        }
        curl_close($curlObj); //关闭会话  
        return $response;  
    }
}
