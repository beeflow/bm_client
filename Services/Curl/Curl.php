<?php

/**
 * @author        Rafal Przetakowski <rafal.p@beeflow.co.uk>
 * @copyright (c) 2016 Beeflow Ltd
 * Time: 10:54
 */

namespace BMClientBundle\Client\Services\Curl;

class Curl
{

    /**
     * @var String
     */
    private $remoteSessionId = null;

    /**
     * @param String $sessionId
     */
    public function setRemoteSessionId($sessionId)
    {
        $this->remoteSessionId = $sessionId;
    }

    /**
     * @param CurlDto $curlDto
     *
     * @return array|null
     */
    public function send(CurlDto $curlDto): ?array
    {
        $contentType = $curlDto->getContentType();
        $post = $curlDto->getData();
        $url = $curlDto->getUrl();
        $method = $curlDto->getMethod();

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        if (!empty($post)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: {$contentType}"));
            @curl_setopt($ch, CURLOPT_POSTFIELDS, ($contentType === "application/json") ? json_encode($post) : $post);
        }

        if (!empty($this->remoteSessionId)) {
            curl_setopt($ch, CURLOPT_COOKIE, "PHPSESSID=" . $this->remoteSessionId);
        }

        $output = curl_exec($ch);
        curl_close($ch);

        if (!empty($output)) {
            return json_decode($output, true);
        }

        return null;
    }
}
