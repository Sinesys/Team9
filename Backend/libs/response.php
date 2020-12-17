<?php
class Response{
    private $code;
    private $data;
    private $is_file;

    /**
     * Create a response object
     *
     * @param int $code a valid HTTP status code.
     * @param mixed $data the data to be send in the response to a request.
     * @param boolean $is_file should be true if the data to be send is a file.
     */
    function __construct($code, $data=null, $is_file = false){
        $this->code = $code;
        $this->data = $data;
        $this->is_file - $is_file;
    }

    /**
     * Create a response object from the given code and the given data
     *
     * @param int $code a valid HTTP status code.
     * @param mixed $data the data to be send in the response to a request.
     * @return Response the response object.
     */
    public static function makeResponse($code, $data=null){
        return new Response($code, $data);
    }
    
    /**
     * Create a response object from the given code and the given data (converted in JSON format)
     *
     * @param int $code a valid HTTP status code.
     * @param mixed $data the data to be converted in JSON format and to be send in the response to a request.
     * @return Response the response object.
     */
    public static function makeJSONResponse($code, $data=null){
        return new Response($code, json_encode($data));
    }

    /**
     * Create a response object from the given code, and mark the variable is_file as true
     *
     * @param int $code a valid HTTP status code.
     * @return Response the response object.
     */
    public static function makeFileResponse($code){
        return new Response($code, null, true);
    }

    /**
     * Return the status code
     *
     * @return int the HTTP status code.
     */
    public function getCode(){
        return $this->code;
    }

    /**
     * Return the data
     *
     * @return mixed the data.
     */
    public function getData(){
        return $this->data;
    }

     /**
     * Returns the value of the variable is_file
     *
     * @return boolena the value of the variable is_file
     */
    public function isFile(){
        return $this->is_file;
    }

}