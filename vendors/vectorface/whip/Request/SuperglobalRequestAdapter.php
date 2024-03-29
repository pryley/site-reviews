<?php

namespace GeminiLabs\Vectorface\Whip\Request;

/**
 * Provide IP address data from the $_SERVER superglobal.
 */
class SuperglobalRequestAdapter implements RequestAdapter
{
    /**
     * The $_SERVER-style array that serves as the source of data.
     *
     * @var string[]
     */
    private array $server;

    /**
     * A formatted version of the HTTP headers: ["header" => "value", ...]
     *
     * @var string[]
     */
    private array $headers;

    /**
     * Create a new adapter for a superglobal $_SERVER-style array.
     *
     * @param string[] $server An array in a format like PHP's $_SERVER var.
     */
    public function __construct(array $server)
    {
        $this->server = $server;
    }

    public function getRemoteAddr(): ?string
    {
        return $this->server['REMOTE_ADDR'] ?? null;
    }

    public function getHeaders(): array
    {
        return $this->headers ??= $this->serverToHeaders($this->server);
    }


    /**
     * Convert from $_SERVER-style format to normal header names.
     *
     * @param string[] $server The $_SERVER-style array.
     * @return string[] Array of headers with lowercased keys.
     */
    private static function serverToHeaders(array $server): array
    {
        $headers = [];
        foreach ($server as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $key = strtolower(str_replace("_", '-', substr($key, 5)));
                $headers[$key] = $value;
            }
        }
        return $headers;
    }
}
