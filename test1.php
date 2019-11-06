<?php
/**
 * Created by PhpStorm.
 * User: Super
 * Date: 2019-09-05
 * Time: 17:59
 */
include_once "JMessage.php";
include_once "jpush.php";



    //$jim = new JMessage();
    //var_dump($jim->openRegister(array('username' => 'hahahah', 'password' => '111111')));  //注册用户
    //var_dump($jim->getUserDetails('test1'));

    //var_dump($jim->addfriends('testgggg',array("dev1")));     //加好友

    //发送消息
    //var_dump($jim->sendMsg('hahcc', 'Hello, cccc!','single',array()));
    //var_dump($jim->sendMsg('10008151', '下午好', 'group',array(),"hahcc"));




    $appkeys = 'efbdad17a9be59ad08af28a0'; //应用程序(appKey)
    $masterSecret = 'a66414bc1ab0274b7a4535a7'; //应用密钥
    $obj = new jpush($username, $appkeys, $masterSecret);
    $sendno = intval((double)microtime() * 10000);
    $sendno = date('His').str_pad($sendno, 4, '0', STR_PAD_LEFT);
    $receiver_value = '';
    $msg_content =json_encode(array('n_builder_id'=>0, 'n_title'=>'','n_content'=>'你好菜!!!!!!'));
    $platform = 'android';
    $res = $obj->send($sendno, 4, $receiver_value, 1, $msg_content, $platform);
    print_r($res);
    exit();
