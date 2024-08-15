<?php
namespace Plantation\Clover\Queue;
use Plantation\Clover\Queue\Adapter\Redis;
use Plantation\Clover\Queue\Jobs;

class Queue
{
    public $queueName;
    public $procNum = 8; // 进程总数
    public $param;
    public $container;

    public function __construct($param,$container){
        $temp = [];
        foreach($param as $val){
            $tempVal = $val;
            $tempArr = explode('=',$tempVal);
            if(isset($tempArr[0]) && isset($tempArr[1])){
                $tempArr[0] = str_replace('--','',$tempArr[0]);
                $tempArr[0] = str_replace('-','',$tempArr[0]);
                $temp[$tempArr[0]] = $tempArr[1];
            }
        }

        $this->param = $temp;
        $temp = [];
        $this->container = $container;
    }

    // 启动进程
    public function run()
    {
        $redisInstance = new Redis($this->container->get('redisSingle'));
        if(isset($this->param['threads']) && $this->param['threads']){
            $this->procNum = $this->param['threads'] * 1;
        }

        if(isset($this->param['name']) && $this->param['name']){
            $this->queueName = 'CloverQueue-'.$this->param['name'];
        }else{
            die();
        }

        if($this->param['listen']=='stop'){
            fwrite(STDOUT, 'stop'."\n");
            $redisInstance->setQueueStatus($this->queueName);
            die();
        }

        if($this->param['listen']=='start'){
            fwrite(STDOUT, 'start'."\n");
            $redisInstance->setQueueStatus($this->queueName,1);
        }

        if($this->param['listen']=='restart'){
            fwrite(STDOUT, 'restart'."\n");
            $redisInstance->setQueueStatus($this->queueName,1);
        }
        $this->work($this->queueName);
        /*
        for ($i = 0; $i < $this->procNum; $i++) {
            $nPID = \pcntl_fork();//创建子进程
            if ($nPID == 0) {
                //子进程
                $this->work($this->queueName);
                exit(0);
            }
        }

        // 等待子进程执行完毕，避免僵尸进程
        $n = 0;
        while ($n < $this->procNum) {
            $nStatus = -1;
            $nPID = \pcntl_wait($nStatus);
            if ($nPID > 0) {
                ++$n;
            }
        }*/

    }
    // 删除队列

    //业务代码
    public function work($queueName)
    {
        //fwrite(STDOUT, $queueName."\n");
        $redisInstance = new Redis($this->container->get('redisSingle'));
        while (true) {
            $stop = $redisInstance->getQueueStatus($this->queueName);
            //fwrite(STDOUT, $stop."\n");
            if($stop==0){
                continue;
            }

            $data = $redisInstance->dequeue($this->queueName);
            if($data){
                $cpdata = $data = json_decode($data,true);

                $attemp = $redisInstance->getAttemp($this->queueName.$cpdata['token']);
                if($attemp>0){
                    if($attemp>5){
                        continue;
                    }

                    $state = $redisInstance->getStatus($this->queueName.$cpdata['token']);
                    if($state=='finish'){
                        $redisInstance->deleteState($this->queueName.$cpdata['token']);
                        $redisInstance->deleteAttemp($this->queueName.$cpdata['token']);
                        fwrite(STDOUT,'retry finish'."\n");
                        continue;
                    }

                    fwrite(STDOUT, 'retry'."\n");
                    $redisInstance->setAttemp($this->queueName.$cpdata['token']);
                }

                if(isset($data['class']) && $data['class']){
                    if(!class_exists($data['class'])){
                        echo 'classs '.$data['class'].'不存在！'."\n";
                    }

                    $classInstance = new $data['class']($this->container);
                    if(!method_exists($classInstance, $data['function'])){
                        echo 'classs '.$data['class'].' 方法 '.$data['function'].'() 不存在！'."\n";
                    }

                    $functionName = $data['function'];

                    $jobInstance = new Jobs($redisInstance->getInstance(),$this->queueName,$cpdata['token'],$cpdata['data']);
                    $return = $classInstance->$functionName($jobInstance,$data['data']);
                    if(isset($return['ok'])&&$return['ok']===true){
                        $redisInstance->setStatus($this->queueName.$cpdata['token'],'finish');
                        $msg ='';
                        if(isset($return['msg'])){
                            $msg = ' [msg='.$return['msg'].']';
                        }
                        echo 'token:{'.$cpdata['token'].'} status - '. $redisInstance->getStatus($this->queueName.$cpdata['token']).$msg."\n";
                        $redisInstance->deleteState($this->queueName.$cpdata['token']);
                        $redisInstance->deleteAttemp($this->queueName.$cpdata['token']);
                    }else{
                        if($redisInstance->getStatus($this->queueName.$cpdata['token'])=='cancel'){
                            continue;
                        }

                        $redisInstance->setStatus($this->queueName.$cpdata['token'],'NoReturnValue',24*60*60*15);
                        $redisInstance->setAttemp($this->queueName.$cpdata['token'],24*60*60*15);
                        $redisInstance->reAdd($this->queueName,json_encode($data));
                    }
                }

                //fwrite(STDOUT, var_dump($data)."\n");
            }
        }
    }
}