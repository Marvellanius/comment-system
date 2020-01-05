<?php


namespace Commentsystem;


class Response
{
    const HTTP_OK = 200;
    const HTTP_CREATED = 201;
    const HTTP_NOT_FOUND = 404;

    public array $headers;

    protected string $content;

    protected string $version;

    protected int $statusCode;

    protected string $statusText;

    protected string $charset;

    public function __construct($content = '', int $status = 200, array $headers = [])
    {
        $this->headers = $headers;
        $this->content = $content;
        $this->statusCode = $status;
    }

    public function setContent(string $content)
    {
        $this->content = $content;
    }

    public function setStatusCode(int $statusCode)
    {
        $this->statusCode = $statusCode;
    }

    public function send()
    {
        foreach($this->headers as $header) {
            header($header);
        }
        echo $this->content;
        return $this;
    }

    public function withHeaders(array $headers)
    {
        $this->headers = $headers;
    }
}