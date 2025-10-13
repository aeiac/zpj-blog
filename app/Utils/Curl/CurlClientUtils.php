<?php
/**
 * HTTP Client Utility using cURL
 *
 * @package App\Utils\Curl
 * @author Mr.raycake
 * @since 2025-03-25
 */

namespace App\Utils\Curl;

class CurlClientUtils
{
// HTTP Methods
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';
    const METHOD_PATCH = 'PATCH';

    // Content Types
    const CONTENT_FORM = 'application/x-www-form-urlencoded';
    const CONTENT_JSON = 'application/json';
    const CONTENT_MULTIPART = 'multipart/form-data';

    // Default options
    const DEFAULT_OPTIONS = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_CONNECTTIMEOUT => 15,
    ];

    private $url;
    private $method = self::METHOD_GET;
    private $headers = [];
    private $data;
    private $contentType = self::CONTENT_FORM;
    private $options = [];

    public function __construct(array $config = [])
    {
        $this->method = strtoupper($config['method'] ?? self::METHOD_GET);
        $this->setContentType($config['content_type'] ?? self::CONTENT_FORM);
        $this->options = array_replace(self::DEFAULT_OPTIONS, $config['options'] ?? []);
    }

    // set Method
    public function setMethod($method)
    {
        $this->method = strtoupper($method);
        return $this;
    }

    // set ContentType
    public function setContentType($contentType): self
    {
        $this->contentType = $contentType;
        $this->setHeader('Content-Type', $contentType);
        return $this;
    }

    // set Header
    public function setHeader($name,$value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    // set Headers
    public function setHeaders($headers): self
    {
        foreach ($headers as $name => $value) {
            $this->setHeader($name, $value);
        }
        return $this;
    }

    // set Data
    public function setData($data): self
    {
        $this->data = $data;
        return $this;
    }

    // set Option
    public function setOption(int $option, $value): self
    {
        $this->options[$option] = $value;
        return $this;
    }

    // set Options
    public function setOptions(array $options): self
    {
        $this->options = array_replace($this->options, $options);
        return $this;
    }

    public function get(string $url)
    {
        return $this->request($url);
    }

    public function post(string $url)
    {
        return $this->setMethod(self::METHOD_POST)->request($url);
    }

    public function request(string $url)
    {
        $this->url = $url;
        $ch = curl_init();

        $headers = [];
        foreach ($this->headers as $name => $value) {
            $headers[] = "$name: $value";
        }

        $data = $this->prepareData();

        $options = [
            CURLOPT_URL => $this->buildUrl(),
            CURLOPT_CUSTOMREQUEST => $this->method,
            CURLOPT_HTTPHEADER => $headers,
        ];

        if (in_array($this->method, [self::METHOD_POST, self::METHOD_PUT, self::METHOD_PATCH])) {
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = $data;
        }


        $options = array_replace($this->options, $options);

        curl_setopt_array($ch, $options);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        $errno = curl_errno($ch);

        if ($errno) {
            throw new \RuntimeException("cURL error [$errno]: $error");
        }

        curl_close($ch);

        return $this->parseResponse($response);
    }

    private function prepareData()
    {
        if ($this->contentType === self::CONTENT_JSON && is_array($this->data)) {
            return json_encode($this->data);
        }
        return $this->data;
    }

    private function parseResponse($response)
    {
        if ($this->contentType === self::CONTENT_JSON) {
            return json_decode($response, true, 512);
        }
        return $response;
    }


    private function buildUrl()
    {
        if ($this->method === self::METHOD_GET && !empty($this->data) && is_array($this->data)) {
            return $this->url . '?' . http_build_query($this->data);
        }
        return $this->url;
    }
}
