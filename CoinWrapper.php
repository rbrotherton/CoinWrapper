<?php

namespace CoinWrapper;

class CoinWrapper {
    // TODO: implement global stats API, Multiple/top/limit
    // TODO: Docs: global stats, multiple/top/limit
    // TODO: Make composer enabled/PSR4 autoload compat

	protected $api_uri 	   = "https://api.coinmarketcap.com";
	protected $api_version = "v1";
    protected $currency    = "USD";
    protected $debug       = false;

    // Populated later in constructor
    protected $tickers     = [];

    // Default currency mode
    protected $default_currency = "USD";

    // Supported currencies
    protected $currencies = [
        "AUD",
        "BRL",
        "CAD",
        "CHF",
        "CLP",
        "CNY",
        "CZK",
        "DKK",
        "EUR",
        "GBP",
        "HKD",
        "HUF",
        "IDR",
        "ILS",
        "INR",
        "JPY",
        "KRW",
        "MXN",
        "MYR",
        "NOK",
        "NZD",
        "PHP",
        "PKR",
        "PLN",
        "RUB",
        "SEK",
        "SGD",
        "THB",
        "TRY",
        "TWD",
        "ZAR"
    ];

    /**
     * CoinWrapper constructor.
     * @param string $currency
     */
    public function __construct($currency = "")
    {
        // Populate Tickers list
        $this->tickers = $this->loadTickers();

        // Allow for currency overriding
        if($currency === ""){
            $this->currency = $this->default_currency;
        } else {
            $currency = strtoupper($currency);
            if($this->currencyIsValid($currency)){
                $this->setCurrency($currency);
            } else {
                throw new \Exception("Invalid currency: ". $currency);
            }
        }
    }

    /**
     * Get a Ticker name associated with a given symbol
     * e.g. BTC => bitcoin
     *
     * @param $symbol
     * @return mixed
     * @throws \Exception
     */
    public function getNameBySymbol($symbol)
    {
        $symbol = strtoupper($symbol);
        foreach($this->tickers as $ticker){
            if($ticker->symbol == $symbol){
                return $ticker->id;
            }
        }

        throw new \Exception("Unknown Symbol: ". $symbol);

    }

    /**
     * Get a Symbol associated with a given Ticker name
     * e.g. bitcoin => BTC
     *
     * @param $symbol
     * @return mixed
     * @throws \Exception
     */
    public function getSymbolByName($name)
    {
        $name = strtolower($name);
        foreach($this->tickers as $ticker){
            if($ticker->id == $name){
                return $ticker->symbol;
            }
        }

        throw new \Exception("Unknown Ticker: ". $name);
    }

    /**
     *
     * Return all Tickers
     *
     * @return array|mixed
     */
    public function getTickers()
    {
        return $this->tickers;
    }

    /**
     *
     * Is a given ticker name valid?
     *
     * @param $ticker
     * @return bool
     */
    public function nameIsValid($name)
    {
        foreach($this->tickers as $ticker){
            if($ticker->id == $name){
                return true;
            }
        }

        return false;
    }

    /**
     *
     * Is a given ticker Symbol valid?
     *
     * @param $symbol
     * @return bool
     */
    public function symbolIsValid($symbol)
    {

        foreach($this->tickers as $ticker){
            if($ticker->symbol == $symbol){
                return true;
            }
        }

        return false;

    }

    /**
     *
     * Get data for a single Ticker from API
     *
     * @param $ticker
     * @param bool $return_json
     * @return mixed|string
     * @throws \Exception
     */
	public function getTickerData($ticker, $return_json = false)
	{

        // Valid Ticker?
		if(!$this->nameIsValid($ticker)){
            if($this->symbolIsValid($ticker)){
                $ticker = $this->getNameBySymbol($ticker);
            } else {
                throw new \Exception("Unknown Ticker: ". $ticker);
            }
        }

        // Currency Conversion
        if($this->currency != $this->default_currency){
            $params = "?convert=".$this->currency;
        } else {
            $params = "";
        }

        // Get data
		$api_endpoint = $this->api_uri . "/" . $this->api_version . "/ticker/" . $ticker . $params;
		$json = @file_get_contents($api_endpoint);

        // Support Debug mode
        if($this->debug){
            echo "Request to API Endpoint: ";
            var_dump($api_endpoint);
            echo "Response from API: ";
            var_dump($json);
        }

        // Decode and check for errors
        if($json === false){
            throw new \Exception("Failed to retrieve data from API. Invalid ticker?");
        }

        $decoded = json_decode($json);
        if(!is_array($decoded)){
            throw new \Exception("API Error: ". $decoded->error);
        }

        // Return
        if($return_json){
            return $json;
        } else {
            return $decoded;
        }

	}

    /**
     *
     * Enable static calls to getTickerData
     *
     * @param $ticker
     * @param bool $return_json
     * @return mixed
     * @throws \Exception
     */
    public static function getTickerDataStatic($ticker, $return_json = false){
        $obj = new static;
        return $obj->getTickerData($ticker, $return_json);
    }

    /**
     *
     * Get data for all Tickers
     *
     * @param int $limit
     * @param bool $return_json
     * @return mixed|string
     */
    public function getAllTickersData($limit = 0, $return_json = false)
    {

        $limit = intval($limit);

        // Currency Conversion
        if($this->currency != $this->default_currency){
            $params = "?limit=". $limit ."&convert=". $this->currency;
        } else {
            $params = "?limit=". $limit;
        }

        // Get data
        $api_endpoint = $this->api_uri . "/" . $this->api_version . "/ticker/" . $params;
        $json = @file_get_contents($api_endpoint);

        // Support Debug mode
        if($this->debug){
            echo "Request to API Endpoint: ";
            var_dump($api_endpoint);
            echo "Response from API: ";
            var_dump($json);
        }

        // Decode and check for errors
        if($json === false){
            throw new \Exception("Failed to retrieve data from API.");
        }

        $decoded = json_decode($json);
        if(!is_array($decoded)){
            throw new \Exception("API Error: ". $decoded->error);
        }

        // Return
        if($return_json){
            return $json;
        } else {
            return $decoded;
        }
    }

    /**
     *
     * Enable static calls to getAllTickersData
     *
     * @param $ticker
     * @param bool $return_json
     * @return mixed
     * @throws \Exception
     */
    public static function getAllTickersDataStatic($limit = 0, $return_json = false){
        $obj = new static;
        return $obj->getAllTickersData($limit, $return_json);
    }

    /**
     *
     * Set my active currency
     *
     * @param $currency
     * @throws \Exception
     * @return $this
     */
    public function setCurrency($currency)
    {
        $currency = strtoupper($currency);
        if($this->currencyIsValid($currency)){
            $this->currency = $currency;
        } else {
            throw new \Exception("Invalid currency type: ". $currency);
        }

        return $this;
    }

    /**
     *
     * Is a given currency abbreviation valid?
     *
     * @param $currency
     * @return mixed
     */
    public function currencyIsValid($currency)
    {
        return array_search($currency, $this->currencies);
    }

    /**
     *
     * Get Currencies array
     *
     * @return array
     */
    public function getCurrencies()
    {
        return $this->currencies;
    }

    /**
     * Set Debug Mode
     *
     * @param $state
     * @return $this
     */
    public function setDebug($state)
    {
        $this->debug = (boolean)$state;
        return $this;
    }


    /**
     *
     * Get a list of all Crypto-currencies served by the API
     *
     * @return array
     * @throws \Exception
     */
    public function getAllTickerSymbols()
    {
        $tickers = $this->getAllTickersData(0);
        $symbols = [];

        foreach($tickers as $ticker){
            $symbols[] = [
                "id"     => $ticker->id,
                "name"   => $ticker->name,
                "symbol" => $ticker->symbol,
            ];
        }

        return $symbols;
    }

    /**
     *
     * Update tickers.json repo
     *
     * @return $this
     * @throws \Exception
     */
    public function updateTickerRepo()
    {

        // Declare path to repo
        $tickers_path = "tickers.json";

        // Can we write to it?
        if(!is_writable($tickers_path)){
            throw new \Exception("tickers.json is not writable! Check file system permissions.");
        }

        // Get all ticker symbols from API
        $tickers = $this->getAllTickerSymbols();

        // If we got symbols back, encode them and write new JSON to file
        if(count($tickers)){
            $fh = fopen($tickers_path, "w");
            fwrite($fh, json_encode($tickers));
            fclose($fh);
        } else {
            throw new \Exception("Failed to retrieve new Symbols definition");
        }

        return $this;

    }

    /**
     *
     * Load, decode, and return the tickers.json repo
     *
     * @return mixed
     */
    protected function loadTickers()
    {
        return json_decode(file_get_contents("tickers.json"));
    }

    /**
     *
     * Get global data
     *
     * @param int $return_json
     * @return mixed|string
     * @throws \Exception
     */
    public function getGlobalData($return_json = 0)
    {
        // Currency Conversion
        if($this->currency != $this->default_currency){
            $params = "?convert=".$this->currency;
        } else {
            $params = "";
        }

        // Get data
        $api_endpoint = $this->api_uri . "/" . $this->api_version . "/global/" . $params;
        $json = @file_get_contents($api_endpoint);

        // Support Debug mode
        if($this->debug){
            echo "Request to API Endpoint: ";
            var_dump($api_endpoint);
            echo "Response from API: ";
            var_dump($json);
        }

        // Decode and check for errors
        if($json === false){
            throw new \Exception("Failed to retrieve data from API.");
        }

        $decoded = json_decode($json);

        // Return
        if($return_json){
            return $json;
        } else {
            return $decoded;
        }
    }

}