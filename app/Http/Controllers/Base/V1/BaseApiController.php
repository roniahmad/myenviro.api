<?php

namespace App\Http\Controllers\Base\V1;

use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Config\Config;

class BaseApiController extends Controller
{
    /**
     * @var int
     */
    protected $statusCode = Response::HTTP_OK;

    /**
     * default pagination
     * @var int
     */
    protected $paginate = 15;

    /**
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param mixed $statusCode
     * @return $this
     */
     public function setStatusCode($statusCode)
     {
        $this->statusCode = $statusCode;
        return $this;
     }

    /**
     * @param $data
     * @param array headers
     * @return mixed
     */
     public function respond($data, $headers = [])
     {
        return response()->json($data, $this->getStatusCode(), $headers);
     }

     /**
     * 400
     * @param string $message
     * @return $this
     */
     public function respondBadRequest($message = 'Bad Request!')
     {
        return $this->setStatusCode(Response::HTTP_BAD_REQUEST)->respondWithError($message, 0);
     }

     /**
     * 401
     * @param string $message
     * @return $this
     */
     public function respondUnAuthorized($message = "We can't find an account with this credentials.")
     {
        return $this->setStatusCode(Response::HTTP_UNAUTHORIZED)->respondWithError($message, 0);
     }

     /**
     * 404
     * @param string $message
     * @return $this
     */
     public function respondNotFound($message = 'Not Found!')
     {
        return $this->setStatusCode(Response::HTTP_NOT_FOUND)->respondWithError($message, 0);
     }

     /**
     * 500
     * @param string $message
     * @return $this
     */
     public function respondInternalError($message = 'Internal Error!')
     {
        return $this->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR)->respondWithError($message, 0);
     }

     /**
     * @param string $message
     * @return $this
     */
     public function respondCreated($message = 'Successfully Created!')
     {
        return $this->setStatusCode(Response::HTTP_CREATED)->respondWithError($message, 0);
     }

     /**
     * @param $message
     * @return mixed
     */
     public function respondWithError($message, $success)
     {
        return $this->respond([
                'success' => $success,
                'message' => $message
            ]);
     }

}
