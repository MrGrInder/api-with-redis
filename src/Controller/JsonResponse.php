<?php

declare(strict_types=1);

namespace App\Controller;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\ResponseInterface;

final class JsonResponse implements ResponseInterface
{
    private string $protocolVersion = '1.1';
    private int $statusCode;
    private string $reasonPhrase;
    private array $headers;
    private StreamInterface $body;

    /**
     * @param $data
     * @param int $status
     * @param array $headers
     */
    public function __construct(
        $data = null,
        int $status = 200,
        array $headers = []
    ) {
        $this->statusCode = $status;
        $this->body = $this->createBody($data);
        $this->headers = array_merge(['Content-Type' => 'application/json'], $headers);
    }

    /**
     * @param $data
     * @return StreamInterface
     * @throws \JsonException
     */
    private function createBody($data): StreamInterface
    {
        $body = new class implements StreamInterface {
            private string $content = '';

            public function __toString(): string
            {
                return $this->content;
            }

            public function close(): void {}
            public function detach() { return null; }
            public function getSize(): ?int { return strlen($this->content); }
            public function tell(): int { return 0; }
            public function eof(): bool { return true; }
            public function isSeekable(): bool { return false; }
            public function seek(int $offset, int $whence = SEEK_SET): void {}
            public function rewind(): void {}
            public function isWritable(): bool { return true; }
            public function write(string $string): int
            {
                $this->content .= $string;
                return strlen($string);
            }
            public function isReadable(): bool { return false; }
            public function read(int $length): string { return ''; }
            public function getContents(): string { return $this->content; }
            public function getMetadata(?string $key = null) { return null; }
        };

        if ($data !== null) {
            $json = json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES);
            $body->write($json);
        }

        return $body;
    }

    /**
     * @return string
     */
    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    /**
     * @param $version
     * @return $this
     */
    public function withProtocolVersion($version): self
    {
        $new = clone $this;
        $new->protocolVersion = $version;
        return $new;
    }

    /**
     * @return array|\string[][]
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasHeader($name): bool
    {
        return isset($this->headers[strtolower($name)]);
    }

    /**
     * @param $name
     * @return array|string[]
     */
    public function getHeader($name): array
    {
        $name = strtolower($name);
        return $this->headers[$name] ?? [];
    }

    /**
     * @param $name
     * @return string
     */
    public function getHeaderLine($name): string
    {
        return implode(', ', $this->getHeader($name));
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function withHeader($name, $value): self
    {
        $new = clone $this;
        $new->headers[strtolower($name)] = (array)$value;
        return $new;
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function withAddedHeader($name, $value): self
    {
        $new = clone $this;
        $name = strtolower($name);
        $new->headers[$name] = array_merge($new->headers[$name] ?? [], (array)$value);
        return $new;
    }

    /**
     * @param $name
     * @return $this
     */
    public function withoutHeader($name): self
    {
        $new = clone $this;
        unset($new->headers[strtolower($name)]);
        return $new;
    }

    /**
     * @return StreamInterface
     */
    public function getBody(): StreamInterface
    {
        return $this->body;
    }

    /**
     * @param StreamInterface $body
     * @return $this
     */
    public function withBody(StreamInterface $body): self
    {
        $new = clone $this;
        $new->body = $body;
        return $new;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @param $code
     * @param $reasonPhrase
     * @return $this
     */
    public function withStatus($code, $reasonPhrase = ''): self
    {
        $new = clone $this;
        $new->statusCode = $code;
        $new->reasonPhrase = $reasonPhrase;
        return $new;
    }

    /**
     * @return string
     */
    public function getReasonPhrase(): string
    {
        return $this->reasonPhrase;
    }
}
