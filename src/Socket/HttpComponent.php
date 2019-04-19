<?php

namespace Swarm\Socket;

use Exception;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use function GuzzleHttp\Psr7\str;
use Illuminate\Database\DetectsLostConnections;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Psr\Http\Message\RequestInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Http\HttpServerInterface;
use Swarm\Server\QueryParameters;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\HttpKernel\Exception\HttpException;

abstract class HttpComponent implements HttpServerInterface
{
    use DetectsLostConnections;

    /**
     * The PSR-7 request.
     *
     * @var \Psr\Http\Message\RequestInterface
     */
    protected $request;

    /**
     * Request Content-Length.
     *
     * @var int
     */
    protected $contentLength;

    /**
     * Message received for the request.
     *
     * @var string
     */
    protected $messageReceived = '';

    /**
     * {@inheritdoc}
     */
    public function onOpen(ConnectionInterface $connection, RequestInterface $request = null)
    {
        $this->request = $request;
        $this->contentLength = $this->findRequestContentLength($request->getHeaders());
        $this->messageReceived = (string) $request->getBody();

        $this->responseOnCompleted($connection);
    }

    /**
     * {@inheritdoc}
     */
    public function onMessage(ConnectionInterface $connection, MessageInterface $message)
    {
        $this->messageReceived .= $message->getContents();

        $this->responseOnCompleted($connection);
    }

    /**
     * {@inheritdoc}
     */
    public function onClose(ConnectionInterface $connection)
    {
        //
    }

    /**
     * {@inheritdoc}
     */
    public function onError(ConnectionInterface $connection, Exception $exception)
    {
        if (! $exception instanceof HttpException) {
            if ($this->causedByLostConnection($exception)) {
                exit(0);
            }

            return;
        }

        $response = new Response($exception->getStatusCode(), [
            'Content-Type' => 'application/json',
        ], \json_encode([
            'error' => $exception->getMessage(),
        ]));

        $connection->send(str($response));
        $connection->close();
    }

    /**
     * Validate whether we have received completed message.
     *
     * @param \Ratchet\ConnectionInterface $connection
     *
     * @return void
     */
    protected function responseOnCompleted(ConnectionInterface $connection): void
    {
        if (\strlen($this->messageReceived) === $this->contentLength) {
            $this->responseWith($connection);
        }
    }

    /**
     * Proxy the request as Laravel request and return a response.
     *
     * @param \Ratchet\ConnectionInterface $connection
     *
     * @return void
     */
    protected function responseWith(ConnectionInterface $connection): void
    {
        $baseRequest = (new ServerRequest(
            $this->request->getMethod(),
            $this->request->getUri(),
            $this->request->getHeaders(),
            $this->messageReceived,
            $this->request->getProtocolVersion()
        ))->withQueryParams(QueryParameters::create($this->request)->all());

        $request = Request::createFromBase((new HttpFoundationFactory())->createRequest($baseRequest));

        $response = $this($request);

        $connection->send(JsonResponse::create($response));
        $connection->close();
    }

    /**
     * Find Request Content-Type.
     *
     * @param array $headers
     *
     * @return int
     */
    protected function findRequestContentLength(array $headers): int
    {
        return Collection::make($headers)->first(function ($values, $header) {
            return strtolower($header) === 'content-length';
        })[0] ?? 0;
    }
}
