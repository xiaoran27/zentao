<?php

    /**
     * Send message
     *
     * @param  string $userList
     * @param  string $message
     * @access public
     * @return bool|string
     */
    public function sendSignle($accessToken,$robotCode,$userList, $message)
    {

        $url = 'https://api.dingtalk.com/v1.0/robot/oToMessages/batchSend';

        $headers = array(
            'Host: api.dingtalk.com',
            'x-acs-dingtalk-access-token: ' . $accessToken,
            'Content-Type: application/json'
        );

        $data = array(
            'robotCode' => $robotCode,
            'userIds' => [$userList],
            'msgKey' => 'officialMarkdownMsg',
            'msgParam' => $message
        );
        $this->log(json_encode($data), __FILE__, __LINE__);

        $data_string = json_encode($data);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }