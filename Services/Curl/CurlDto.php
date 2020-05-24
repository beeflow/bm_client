<?php

/**
 * Created by PhpStorm.
 * User: rprzetakowski
 * Date: 07.02.18
 * Time: 20:00
 */

namespace BMClientBundle\Client\Services\Curl;

class CurlDto
{

    /**
     * @var string
     */
    private $url;

    /**
     * @var array
     */
    private $data;

    /**
     * @var string
     */
    private $contentType = 'application/json';

    /**
     * @var string
     */
    private $method;

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return CurlDto
     */
    public function setUrl(string $url): CurlDto
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     *
     * @return CurlDto
     */
    public function setData(array $data): CurlDto
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @param string $contentType
     *
     * @return CurlDto
     */
    public function setContentType(string $contentType): CurlDto
    {
        $this->contentType = $contentType;

        return $this;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param string $method
     * @return CurlDto
     */
    public function setMethod(string $method): CurlDto
    {
        $this->method = $method;
        return $this;
    }
}
