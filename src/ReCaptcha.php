<?php

namespace FruitcakeStudio;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class to show and verify ReCaptcha
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 * @author Fruitcake Studio (http://fruitcakestudio.com)
 */
class ReCaptcha {

    /** @var string URL to retrieve the verification from */
    protected $verifyUrl = "https://www.google.com/recaptcha/api/siteverify";

    /** @var array Error messages from https://developers.google.com/recaptcha/docs/verify */
    protected $errorMessages = array(
        'missing-input-secret' => 'The secret parameter is missing.',
        'invalid-input-secret' => 'The secret parameter is invalid or malformed.',
        'missing-input-response' => 'The response parameter is missing.',
        'invalid-input-response' => 'The response parameter is invalid or malformed.',
    );

    /** @var string siteKey and secret from https://www.google.com/recaptcha/admin */
    protected $siteKey;
    protected $secret;

    /** @var array Errors when available */
    protected $errors = array();

    public function __construct($siteKey, $secret)
    {
        $this->siteKey = $siteKey;
        $this->secret = $secret;
    }

    /**
     * Get the Site Key
     *
     * @return string
     */
    public function getSiteKey()
    {
        return $this->siteKey;
    }

    /**
     * Get the secret
     *
     * @return string
     */
    public function getSecret()
    {
        return $this->secret;
    }
    /*
     * Get the script to include.
     *
     * @param  string $hl
     * @param  string $render
     * @param  string $onload
     * @return string
     */
    public function getScript($hl = 'en', $render = 'onload', $onload = null)
    {
        $params = array(
            'hl' => $hl,
            'render' => $render
        );

        if ($onload) {
            $params['onload'] = $onload;
        }

        $qs = http_build_query($params);

        return sprintf('<script type="text/javascript" src="//www.google.com/recaptcha/api.js?%s"></script>', $qs);
    }

    /**
     * Get the captcha code for the form
     *
     * @return string
     */
    public function getWidget($theme = 'light', $type = 'image')
    {
        return sprintf('<div class="g-recaptcha" data-sitekey="%s" data-theme="%s" data-type="%s"></div>', $this->siteKey, $theme, $type);
    }

    /**
     * Verify a response string
     *
     * @param  string $response
     * @param  string $remoteip
     * @return bool
     */
    public function verify($response, $remoteip = null)
    {
        $params = array(
            'secret' => $this->secret,
            'response' => $response,
        );

        if ($remoteip) {
            $params['remoteip'] = $remoteip;
        }

        $response = $this->fetchResponse($params);

        if ($response['success']) {
            $this->errors = array();
            return true;
        } else {
            $this->errors = isset($response['error-codes']) ? $response['error-codes'] : array();
            return false;
        }
    }

    /**
     * Verify the response using the GLOBAL vars
     *
     * @return bool
     */
    public function verifyGlobals()
    {
        $response = isset($_POST["g-recaptcha-response"]) ? $_POST["g-recaptcha-response"] : '';
        $remoteip = isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : null;

        return $this->verify($response, $remoteip);
    }

    /**
     * Verify the response using a Symfony Request object
     *
     * @param  \Symfony\Component\HttpFoundation\Request $request
     * @return bool
     */
    public function verifyRequest(Request $request)
    {
        $response = $request->request->get('g-recaptcha-response');
        $remoteip = $request->getClientIp();

        return $this->verify($response, $remoteip);
    }

    /**
     * Get the errors from the last response
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Get the error messages as human readable message
     */
    public function getErrorMessage()
    {
        $messages = array();
        foreach ($this->errors as $error) {
            if (isset($this->errorMessages[$error])) {
                $messages[] = $this->errorMessages[$error];
            } else{
                $messages[] = $error;
            }
        }

        return implode(' ', $messages);
    }

    /**
     * Get a response from the API
     */
    protected function fetchResponse($params)
    {
        foreach($params as &$param){
            $param = urlencode($param);
        }

        $qs = http_build_query($params);
        $response = file_get_contents($this->verifyUrl . '?' . $qs);

        return json_decode($response, true);
    }
}
