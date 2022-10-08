<?php

/**
 * Class Responder
 */
class Responder
{
    /**
     * @var string
     */
    private string $acceptHeader;

    /**
     * @var string
     */
    private string $response;

    /**
     * @var string
     */
    private string $responseHeader;


    /**
     * @return String
     */
    public function getResponse(): string
    {
        return $this->response;
    }

    /**
     * @return String
     */
    public function getResponseHeader(): string
    {
        return $this->responseHeader;
    }

    /**
     * Responder constructor.
     *
     * The constructor is responsible for setting the $acceptHeader and $responseHeader attributes, depending on what the parameter's value is.
     * The parameter's value is checked with regex, deciding which type the response should be. The default response is JSON.
     *
     * @param string $acceptHeader A string that contains an http request's Accept header. $_SERVER['HTTP_ACCEPT']
     */
    public function __construct(string $acceptHeader)
    {
        $this->acceptHeader = 'json';
        $this->responseHeader = 'Content-Type: application/json';
        if (isset($acceptHeader) && !empty($acceptHeader)) {
            if (preg_match('/(application|text)\/(.*)xml(.*)/', $_SERVER['HTTP_ACCEPT'])) {
                $this->responseHeader = 'Content-Type: application/xml';
                $this->acceptHeader = 'xml';
            //} else if (preg_match('/(application|text)\/(.*)yaml(.*)/', $_SERVER['HTTP_ACCEPT'])) {
            //    $this->acceptHeader = 'yaml';
            //    $this->responseHeader = 'Content-Type: text/x-yaml';
            }
        }
    }

    /**
     * This function creates the response string.
     * Sets the object's $response value.
     *
     * @param Secret $secret The Secret object that's attributes needs to be written in the response
     * @throws Exception
     */
    public function createResponse(Secret $secret)
    {
        $this->response = match ($this->acceptHeader) {
            'xml' => $secret->toXML(),
            'json' => $secret->toJSON(),
            //'yaml' => $secret->toYAML(),
        };
    }

}
