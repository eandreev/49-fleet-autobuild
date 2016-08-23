<?php

class HttpRequest {

    /**
     * @see \Utils\HttpRequest::req
     */
    function perform($method, $url, $requestHeaders = array(), $postFields = false, $basicAuth = false) {
        return self::req($method, $url, $requestHeaders, $postFields, $basicAuth);
    }

    /**
     * Performs an HTTP requests and returns the results.
     *
     * @param string $method             request method
     * @param string $url                request url
     * @param array  $requestHeaders     request headers, e.g. array('User-Agent: ...', 'Accept-Charset: ...')
     * @param array  $postFields         request post fields, e.g. array('param1' => val1, 'param2' => val2)
     * @param string $basicAuth          request basic auth string ('user:passwd')
     * @return array                     request results: array('headers' => array(array('header', value), ...), 'body' => string, 'code' => integer, 'resp_code_level' => integer)
     */
    static function req($method, $url, $requestHeaders = array(), $postFields = false, $basicAuth = false) {
        $c = curl_init();

        curl_setopt($c, CURLOPT_URL, $url);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($c, CURLOPT_HEADER, 1);
        curl_setopt($c, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($c, CURLOPT_HTTPHEADER, $requestHeaders);
        if(false !== $postFields)
            curl_setopt($c, CURLOPT_POSTFIELDS, $postFields);
        if(false !== $basicAuth) {
            error_log(' ============================> basicAuth: '.$basicAuth);
            curl_setopt($c, CURLOPT_USERPWD, $basicAuth);
        }
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($c, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 5);

        $response = curl_exec($c);
        if(false === $response) {
            $msg = 'curl error #'.curl_errno($c).': '.curl_error($c);
            curl_close($c);
            throw new \Exception($msg);
        }

        $headersLength = curl_getinfo($c, CURLINFO_HEADER_SIZE);
        $respHeaders = substr($response, 0, $headersLength);
        $respBody = substr($response, $headersLength);

        // parse the headers
        $parsedHeaders = array();
        foreach(explode("\n", $respHeaders) as $h)
            if(strlen(trim($h)) > 0)
                if(preg_match('/^([^:]+): (.*)$/', $h, $m))
                    $parsedHeaders[] = array($m[1], $m[2]);
                else
                    $parsedHeaders[] = array('', $h);

        $respCode = curl_getinfo($c, CURLINFO_HTTP_CODE);
        //error_log(sprintf("%.3f %-4s %s", curl_getinfo($c, CURLINFO_TOTAL_TIME), $method, $url));
        $respCodeLevel = floor($respCode/100.0);

        $result = array();
        $result['headers'] = $parsedHeaders;
        $result['body']    = $respBody;
        $result['code']    = $respCode;
        $result['resp_code_level'] = $respCodeLevel;

        curl_close($c);

        return $result;
    }


    // END OF CLASS
}

