<?php

namespace App\Http\Responses;

use Illuminate\Http\Response;

class ApiResponse extends Response
{
    /** Header HTTP Response Code **/
    const OK            = Response::HTTP_OK;
    const CREATED       = Response::HTTP_CREATED;
    const NO_CONTENT    = Response::HTTP_NO_CONTENT;
    const NOT_MODIFIED  = Response::HTTP_NOT_MODIFIED;
    const UNAUTHORIZED  = Response::HTTP_UNAUTHORIZED;
    const VALIDATION    = Response::HTTP_UNPROCESSABLE_ENTITY;
    const SERVER_ERROR  = Response::HTTP_INTERNAL_SERVER_ERROR;
    const BAD_REQUEST   = Response::HTTP_BAD_REQUEST;

    /**
     * ApiResponse constructor.
     * @param bool $success
     * @param array $data
     * @param int $status
     * @param array $headers
     * @return Response
     */
    public function __construct($data = [], $success = true, $status = self::OK, array $headers = [])
    {
        $content = $this->createResponse($success, $data, $status);
        parent::__construct($content, $status, $headers);
    }

    /**
     * @param bool $success
     * @param array $data
     * @param int $status
     * @param array $headers
     * @return Response
     */
    public static function create($data = [], $success = true, $status = self::OK, array $headers = [])
    {
        return new static($data, $success, $status, $headers);
    }

    /**
     * @param string $message
     * @param array $headers
     * @return Response
     */
    public static function __create(string $message, array $headers = [])
    {
        return self::create([
            'message' => [$message]
        ], true, self::OK, $headers);
    }

    /**
     * @param bool $success
     * @param mixed $data
     * @param int $status
     * @return array
     */
    protected function createResponse($success, $data, $status)
    {
        return [
            'data'      => $success ? $data : null,
            'error'     => $success ? null : $data,
            'status'    => $status,
            'success'   => $success,
        ];
    }

    /**
     * @param Throwable $e
     * @param int $code
     * @return Response
     */
    public static function createServerError($e, $code = self::HTTP_BAD_REQUEST)
    {

        $data = [
            'message' => [
                $e->getMessage()
            ]
        ];
        return new static($data, false, $code, []);
    }

    public static function __createServerError($msg, array $headers = [])
    {
        $data = [
            'message' => [
                $msg
            ]
        ];
        return new static($data, false, self::SERVER_ERROR, $headers);
    }

    /**
     * @param array|object $data
     * @param array $headers
     * @return Response
     */
    public static function createBadResponse($data = [], array $headers = [])
    {
        return new static($data, false, self::BAD_REQUEST, $headers);
    }

    /**
     * @param string $message
     * @param array $headers
     * @return Response
     */
    public static function __createBadResponse(string $message, array $headers = [])
    {
        return self::createBadResponse([
            'message' => [$message]
        ], $headers);
    }

    /**
     * @param array|object $data
     * @param array $headers
     * @return Response
     */
    public static function createValidationResponse($data = [], array $headers = [])
    {
        return new static($data, false, self::VALIDATION, $headers);
    }

    /**
     * @param string $message
     * @param array $headers
     * @return Response
     */
    public static function __createValidationResponse(string $message, array $headers = [])
    {
        return self::createValidationResponse([
            'message' => [$message]
        ], $headers);
    }

    /**
     * @param array|object $data
     * @param array $headers
     * @return Response
     */
    public static function createUnAuthorizedResponse($data = [], array $headers = [])
    {
        return new static($data, false, self::UNAUTHORIZED, $headers);
    }

    /**
     * @param string $message
     * @param array $headers
     * @return Response
     */
    public static function __createUnAuthorizedResponse(string $message, array $headers = [])
    {
        return self::createUnAuthorizedResponse([
            'message' => [$message]
        ], $headers);
    }
}
