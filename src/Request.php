<?php


namespace Commentsystem;

/**
 * Class Request
 *
 * @package Commentsystem
 */
class Request
{
    public array $url_elements;
    public string $method;
    public array $parameters;
    public string $uri;
    public string $body;

    public function __construct()
    {
        $this->method = strtoupper($_SERVER['REQUEST_METHOD']);
        $this->uri = $_SERVER['REQUEST_URI'];
        $this->url_elements = explode('/', $_SERVER['REQUEST_URI']);
        if($this->method === "POST") {
            $this->body = file_get_contents('php:://input');
        }
    }

    public function get(string $key): string
    {
        return $this->parameters[$key];
    }

    public function getUrlElements(): array
    {
        return $this->url_elements;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function addParams(string $key, string $value): void
    {
        $this->parameters[$key] = $value;
    }
}