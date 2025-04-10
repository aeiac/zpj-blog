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
    public const METHOD_GET = 'GET';
    public const METHOD_POST = 'POST';
    public const METHOD_PUT = 'PUT';
    public const METHOD_DELETE = 'DELETE';
    public const METHOD_PATCH = 'PATCH';

    // Content Types
    public const CONTENT_FORM = 'application/x-www-form-urlencoded';
    public const CONTENT_JSON = 'application/json';
    public const CONTENT_MULTIPART = 'multipart/form-data';

    // Default options
    private const DEFAULT_OPTIONS = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_CONNECTTIMEOUT => 15,
    ];

    private string $url;
    private string $method = self::METHOD_GET;
    private array $headers = [];
    private $data;
    private string $contentType = self::CONTENT_FORM;
    private array $options = [];

    public function __construct(array $config = [])
    {
        $this->method = strtoupper($config['method'] ?? self::METHOD_GET);
        $this->setContentType($config['content_type'] ?? self::CONTENT_FORM);
        $this->options = array_replace(self::DEFAULT_OPTIONS, $config['options'] ?? []);
    }

    public function setMethod(string $method): self
    {
        $this->method = strtoupper($method);
        return $this;
    }

    public function setContentType(string $contentType): self
    {
        $this->contentType = $contentType;
        $this->setHeader('Content-Type', $contentType);
        return $this;
    }

    public function setHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function setHeaders(array $headers): self
    {
        foreach ($headers as $name => $value) {
            $this->setHeader($name, $value);
        }
        return $this;
    }

    public function setData($data): self
    {
        $this->data = $data;
        return $this;
    }

    public function setOption(int $option, $value): self
    {
        $this->options[$option] = $value;
        return $this;
    }

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

    private function buildUrl(): string
    {
        if ($this->method === self::METHOD_GET && !empty($this->data) && is_array($this->data)) {
            return $this->url . '?' . http_build_query($this->data);
        }
        return $this->url;
    }

    private function prepareData()
    {
        if ($this->contentType === self::CONTENT_JSON && is_array($this->data)) {
            return json_encode($this->data, JSON_THROW_ON_ERROR);
        }
        return $this->data;
    }

    private function parseResponse($response)
    {
        if ($this->contentType === self::CONTENT_JSON) {
            return json_decode($response, true, 512, JSON_THROW_ON_ERROR);
        }
        return $response;
    }
}
