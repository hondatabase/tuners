<?php
class UserLocation {
    public function __construct(
        public string $continent, // Two-letter continent code
        public string $country, // ISO 3166-1 alpha-2 country code
        public string $region, // Region/State short-code
        public string $city) {
    }
}

class User {
    public $id;
    public $username;
    public $location = null;
    
    public function __construct($ip) {
        // Use dummy data for dev purposes
        if (substr($ip, 0, 7) == '192.168' || $ip == '::1') $ip = DEV_IP;

        $ipApi = file_get_contents("http://ip-api.com/json/$ip?fields=status,message,continentCode,countryCode,region,city");
        // IP-API may be down so account for that
        if ($ipApi === false)  return;

        $ipApi = json_decode($ipApi);

        if ($ipApi->status == 'fail') return;

        $this->location = new UserLocation($ipApi->continentCode, $ipApi->countryCode, $ipApi->region, $ipApi->city);
    }
}