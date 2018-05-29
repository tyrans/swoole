<?php
/**
 * Request.php
 *
 * Creator:    chongyi
 * Created at: 2016/08/07 14:50
 */

namespace Journey\Modules\Swoole;

use Illuminate\Foundation\Http\Kernel;
use Symfony\Component\HttpFoundation\Response;
use Journey\Modules\Swoole\Foundation\Application;
use Journey\Modules\Swoole\Http\Request;
use Swoole\Http\Request as SwooleRequest;
use Swoole\Http\Response as SwooleResponse;
use Swoole\Http\Server;

/**
 * Class Request
 *
 * @package Swoole\Laravel\Http
 */
class SwooleServer extends Server
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Kernel
     */
    protected $kernel;

    private function init_app () {
        $app = new Application(base_path());
        $this->kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
        $this->app = $app;
    }

    private function kernel_handle ($req) {
        $app = new Application(base_path());
        $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
        return $kernel->handle($req);
    }

    function start () {
        define('SWOOLE_START', microtime(true));
        $this->init_app();
        $this->on('request', [$this, 'onRequest']);
        parent::start();
    }

    function onRequest (SwooleRequest $request, SwooleResponse $response) {
        $realRequest = Request::captureViaSwooleRequest($request);
        $realResponse = $this->kernel->handle($realRequest);
//        $realResponse = $this->kernel_handle($realRequest);
        $this->send_with($response, $realResponse);
    }

    /**
     * @param SwooleResponse $response
     * @param Response $realResponse
     */
    protected function send_with(SwooleResponse $response, Response $realResponse)
    {
        // Build header.
        foreach ($realResponse->headers->allPreserveCase() as $name => $values) {
            foreach ($values as $value) {
                $response->header($name, $value);
            }
        }

        // Build cookies.
//        foreach ($realResponse->headers->getCookies() as $cookie) {
//            $response->cookie($cookie->getName(), $cookie->getValue(), $cookie->getExpiresTime(),
//                $cookie->getPath(),
//                $cookie->getDomain(), $cookie->isSecure(), $cookie->isHttpOnly());
//        }

        // Set HTTP status code into the swoole response.
        $response->status($realResponse->getStatusCode());
        /**
         * if ($realResponse instanceof BinaryFileResponse) {
         *    $swooleResponse->sendfile($realResponse->getFile()->getPathname());
         *  } else {
         *     $swooleResponse->end($realResponse->getContent());
         *   }
         */
        $response->end($realResponse->getContent());
    }

}