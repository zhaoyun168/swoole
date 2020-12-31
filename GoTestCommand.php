<?php
/**
 * swoole协程
 */
namespace app\schedule\controller;

use platform\common\LoggerClient;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\console\input\Argument;
use think\console\input\Option;
use think\Db;

class GoTestCommand extends Command
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

    /**
     * 配置命令
     */
    protected function configure()
    {
        $this->setName('go:test:run')
             ->setDescription('update invalid status command');
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
        $this->logger = $this->_instance->getMonolog('update', BASE_ROOT . 'log/schedule/go_'.date('Y-m-d').'.log', 0);

        /** mysql数据库的配置 */
        $this->dbConfig = require BASE_ROOT . '/config/database.php'; //数据库配置

        $this->db = $this->getDbConnection($this->dbConfig);

        //更新状态
        $this->updateInvalidStatus();

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
     * 更新状态
     * @return
     */
    private function updateInvalidStatus()
    {
        $this->logger->info(sprintf('[%s]开始更新特慢病申报状态...', $this->_instance->uniqid));

        $handle_start_time = date('Y-m-d H:i:s');
        $execute_start_time = microtime(true);
        try {
            startHandle:

            $current_time = time();

            $url = [
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
                'https://www.jianshu.com/p/3ca61b47149a',
            ];

            foreach ($url as $key => $value) {
                $result_info = $this->httpGet($value, []);

                $insert_data = [
                    'result' => $result_info,
                    'time' => time(),
                ];
                try {
                    $this->db->name('http_result')->insert($insert_data);    
                } catch (\Exception $e) {
                    $this->logger->error(sprintf('[%s]操作数据库异常[%s]', $this->_instance->uniqid, $e->getMessage()));
                }
            }

            $this->handle_num = 0;

            $handle_end_time = date('Y-m-d H:i:s');
            $execute_end_time = microtime(true);
            $execute_time = $execute_end_time - $execute_start_time;

            $this->logger->info(sprintf('[%s]更新特慢病申报状态结束...', $this->_instance->uniqid));
            echo 'success|start_time:'.$handle_start_time.'|end_time:'.$handle_end_time.'|execute_time:'.$execute_time. "\r\n";                
        } catch (\Exception $e) {
            $this->handle_num++;

            //发生异常时，重试更新状态
            if ($this->handle_num <= $this->max_handle_num) {
                $this->logger->error(sprintf('[%s]第[%s]次更新状态异常，异常信息为[%s]，[%s]秒后继续重试...', $this->_instance->uniqid, $this->handle_num, $e->getMessage(), $this->handle_num * 5));
                sleep($this->handle_num * 5);
                goto startHandle;
            } else {
                $this->logger->error(sprintf('[%s]第[%s]次更新状态异常，异常信息为[%s]，不再重试', $this->_instance->uniqid, $this->handle_num, $e->getMessage()));
            }

            $handle_end_time = date('Y-m-d H:i:s');
            $execute_end_time = microtime(true);
            $execute_time = $execute_end_time - $execute_start_time;

            echo 'error|start_time:'.$handle_start_time.'|end_time:'.$handle_end_time.'|execute_time:'.$execute_time. "\r\n";
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
