<?php
/**
 * Kernel.php
 *
 * Creator:    chongyi
 * Created at: 2016/08/07 23:19
 */

namespace Journey\Modules\Swoole\Foundation\Http;

use Illuminate\Foundation\Http\Kernel as LaravelKernel;


class Kernel extends LaravelKernel
{

    public function handle($request)
    {
//        try {
//            $request->enableHttpMethodParameterOverride();
//
//            $response = $this->sendRequestThroughRouter($request);
//        } catch (Exception $e) {
//            $this->reportException($e);
//
//            $response = $this->renderException($request, $e);
//        } catch (Throwable $e) {
//            $this->reportException($e = new FatalThrowableError($e));
//
//            $response = $this->renderException($request, $e);
//        }
//
//        $this->app['events']->dispatch(
//            new Events\RequestHandled($request, $response)
//        );

        return parent::handle($request);
    }

}