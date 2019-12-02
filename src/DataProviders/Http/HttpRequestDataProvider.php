<?php

namespace Laracatch\Client\DataProviders\Http;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Route;
use Laracatch\Client\Contracts\DataProviderContract;
use Laracatch\Client\Models\ErrorModel;
use Laracatch\Client\Support\AttributeTypeSerializationTrait;
use Throwable;

class HttpRequestDataProvider implements DataProviderContract
{
    use AttributeTypeSerializationTrait;

    /**
     * @param ErrorModel $errorModel
     * @param Application $app
     * @param Throwable|null $throwable
     *
     * @return mixed|void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function handle(ErrorModel $errorModel, Application $app, Throwable $throwable = null)
    {
        /**
         * @var $request Request
         */
        $request = $app->make(Request::class);

        $this->setContextRequest($errorModel, $request, $app);
        $this->setContextRequestData($errorModel, $request);
        $this->setContextSession($errorModel, $request);
        $this->setContextCookies($errorModel, $request);
        $this->setContextHeaders($errorModel, $request);
        $this->setContextRoute($errorModel, $request);
    }

    /**
     * @param ErrorModel $errorModel
     * @param Request $request
     * @param Application $app
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function setContextRequest(ErrorModel $errorModel, Request $request, Application $app)
    {
        $clientIp = $app->make('config')->get('laracatch.data_providers.anonymize_client_ip') ? null : $request->getClientIp();

        return $errorModel->setContextRequest(
            $request->getUri(),
            $request->getMethod(),
            $clientIp,
            $request->headers->get('User-Agent')
        );
    }

    /**
     * @param ErrorModel $errorModel
     * @param Request $request
     */
    protected function setContextRequestData(ErrorModel $errorModel, Request $request)
    {
        $files = $this->formatFiles($request->files);

        $errorModel->setContextRequestData($request->query->all(), $this->getRequestBody($request), $files);
    }

    /**
     * @param $files
     *
     * @return array
     */
    protected function formatFiles($files)
    {
        $formatted = [];

        foreach ($files as $file) {
            if (is_array($file)) {
                $formatted[] = $this->formatFiles($file);
            }

            if ($file instanceof UploadedFile) {
                $formatted[] = [
                    'pathname' => $file->getPathname(),
                    'size' => $file->getSize(),
                ];
            }
        }

        return $formatted;
    }

    /**
     * @param Request $request
     *
     * @return \IteratorAggregate|mixed|\Symfony\Component\HttpFoundation\ParameterBag|null
     */
    protected function getRequestBody(Request $request)
    {
        // Laravel merge and update the symphony `request` property that should hold only POST data
        // by checking if the request method is not GET or HEAD we can decide whether to use it or not
        if (in_array($request->getRealMethod(), ['GET', 'HEAD'])) {
            return null;
        }

        $source = null;

        if (method_exists($request, 'json') && method_exists($request, 'isJson') && $request->isJson()) {
            $source = $request->json();
        } else {
            if ($request->request instanceof \IteratorAggregate) {
                $source = $request->request;
            }
        }

        return $source;
    }

    /**
     * @param ErrorModel $errorModel
     * @param Request $request
     *
     * @return array
     */
    protected function setContextSession(ErrorModel $errorModel, Request $request)
    {
        if ( ! $request->hasSession()) {
            return [];
        }

        $data = [];

        foreach ($request->session()->all() as $key => $value) {
            $data[$key] = $this->serializeValue($value);
        }

        $errorModel->setContextSession($data);
    }

    /**
     * @param ErrorModel $errorModel
     * @param Request $request
     */
    protected function setContextCookies(ErrorModel $errorModel, Request $request)
    {
        $data = [];

        foreach ($request->cookies->all() as $key => $value) {
            $data[$key] = $this->serializeValue($value);
        }

        $errorModel->setContextCookies($data);
    }

    /**
     * @param ErrorModel $errorModel
     * @param Request $request
     */
    protected function setContextHeaders(ErrorModel $errorModel, Request $request)
    {
        $data = [];

        foreach ($request->headers->all() as $key => $value) {
            $data[$key] = $this->serializeValue($value);
        }

        $errorModel->setContextHeaders($data);
    }

    /**
     * @param ErrorModel $errorModel
     * @param Request $request
     *
     * @return array
     */
    protected function setContextRoute(ErrorModel $errorModel, Request $request)
    {
        /**
         * @var Route $route
         */
        if ( ! $route = $request->route()) {
            return [];
        }

        $parameters = [];

        foreach ((array)$route->parameters as $key => $value) {
            $parameters[$key] = $this->serializeValue($value);
        }

        $errorModel->setContextRoute([
            'route' => $route->getName(),
            'routeParameters' => $parameters,
            'controllerAction' => $route->getActionName(),
            'middleware' => array_values($route->gatherMiddleware()),
        ]);
    }
}