<?php
namespace Plantation\Clover\Queue\Adapter;

class Redis{

    // 对象
    protected $client;

    /**
     * 构造函数
     */
    public function __construct($instance=null){
        $this->connect($instance);
    }

    // 获得实列
    public function getInstance(){
        return $this;
    }

    /**
     * 连接redis
     */
    public function connect($instance=null){
        if($instance){
            $this->client = $instance;
        }else{
            $this->client = new \Predis\Client([
                'scheme' => 'tcp',
                'host'   => '127.0.0.1',
                'port'   => 6379,
                //'password'=>'foobared',
            ]);
        }
    }

    /**
     * 再入队列
     */
    public function reAdd($name,$data){
        if($this->client->lpush($name,$data)){
            $value = json_decode($data,true);
            $token = $value['token'];
            return $token;
        }else{
            return false;
        }
    }

    // 入队列
    public function add($name,$class,$function='perform',$data=[]){
        $data['t'] = uniqid();
        $data = ['class'=>$class,'function'=>$function,'data'=>$data,'state'=>'ready'];
        $token = md5(uniqid().microtime().json_encode($data).mt_rand(1,99999));
        $data['token'] = $token;
        $name = 'CloverQueue-'.$name;
        $data = json_encode($data);
        if($this->client->lpush($name,$data)){
            return $token;
        }else{
            return false;
        }
    }

    /**
     * 设置所有队列状态
     */
    public function setQueueStatus($queueName,$status=0,$expire=true){
        $this->client->set('queue_states'.$queueName,$status);
        if($expire!=true){
            $this->client->expire('queue_states'.$queueName,$expire);
        }
    }

    /**
     * 获得所有队列状态
     */
    public function getQueueStatus($queueName){
        $state = $this->client->get('queue_states'.$queueName);
        if(!$state){
            return 1;
        }
        return $state;
    }

    // 出队列
    public function dequeue($name){
        return $this->client->lpop($name);
    }

    /**
     *
     * 删除状态
     */
    public function deleteState($token){
        $this->client->del('queue_'.$token);
    }

    /**
     * 设置队列状态
     */
    public function setStatus($token,$status,$expire=true){
        $this->client->set('queue_'.$token,$status);
        if($expire!=true){
            $this->client->expire('queue_'.$token,$expire);
        }
    }

    /**
     * 获得队列状态 一般用于暂停队列
     */
    public function getStatus($token){
        $status = $this->client->get('queue_'.$token);
        if(!$status){
            return 'ready';
        }
        return $status;
    }

    /**
     * 自增尝试次数
     */
    public function setAttemp($token,$expire=true){
        $attemp = $this->client->get('queue_attemp_'.$token);
        if(!$attemp){
            $i=1;
        }else{
            $i=$attemp+1;
        }

        $this->client->set('queue_attemp_'.$token,$i);
        if($expire!=true){
            $this->client->expire('queue_attemp_'.$token,$expire);
        }
    }

    /**
     * 获得尝试次数
     */
    public function getAttemp($token){
        $attemp = $this->client->get('queue_attemp_'.$token);
        if(!$attemp){
            return 0;
        }
        return $attemp;
    }

    /**
     * 删除尝试状态
     * */
    public function deleteAttemp($token){
        $this->client->del('queue_attemp_'.$token);
    }

    /**
     * @param $key
     * @return mixed
     * 获得
     */
    public function get($key){
        return $this->client->get($key);
    }
}
