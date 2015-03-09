<?php

namespace CloudinaryExtension\Security;

use CloudinaryExtension\Credentials;

class SignedUrl
{

    private $signedUrl;

    private function __construct(Url $url, Credentials $credentials)
    {
        $params = array("timestamp" => time(), "mode" => "check");
        $params["signature"] = (string)ApiSignature::fromSecretAndParams($credentials->getSecret(), $params);
        $params["api_key"] = (string)$credentials->getKey();
        $query = http_build_query($params);

        $this->signedUrl = $url . '?' . $query;
    }

    public static function fromUrlAndCredentials(Url $url, Credentials $credentials)
    {
        return new SignedUrl($url, $credentials);
    }

    public function __toString()
    {
        return $this->signedUrl;
    }
}
