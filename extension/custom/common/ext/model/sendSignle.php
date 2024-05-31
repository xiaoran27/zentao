<?php

/**
 * Send message
 *
 * @param string $userList
 * @param string $message
 * @access public
 * @return bool|string
 */
public
function sendSignle($accessToken, $robotCode, $userList, $message)
{

    $url = 'https://api.dingtalk.com/v1.0/robot/oToMessages/batchSend';

    $headers = array(
        'Host: api.dingtalk.com',
        'x-acs-dingtalk-access-token: ' . $accessToken,
        'Content-Type: application/json'
    );

    $data = array(
        'robotCode' => $robotCode,
        'userIds' => explode(",", $userList),
        'msgKey' => 'officialMarkdownMsg',
        'msgParam' => $message
    );
    $this->log(json_encode(array('url'=>$url,'headers'=>$headers,'data'=>$data),JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);

    $resp = common::http($url, $data, array(), $headers, 'json');
    $errors   = commonModel::$requestErrors;

    $this->log(json_encode(array('resp'=>$resp, 'errors'=>$errors),JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);


    if(empty($errors)) return array('result' => 'success','resp'=>$resp);
    return array('result' => 'fail', 'message' => $errors);

}
