# CoinWrapper
###### A simple PHP wrapper class for the [CoinMarketCap Public API](https://coinmarketcap.com/api/).

----
## Usage

#### Simple
    require "CoinWrapper.php";
    $cw   = new \CoinWrapper\CoinWrapper();
    $data = $cw->getTickerData("BTC");
    
#### Simple Static
    require "CoinWrapper.php";
    $data = \CoinWrapper\CoinWrapper::getTickerDataStatic("BTC");

#### Advanced 
    
    // Init
    require "CoinWrapper.php";
    $cw   = new \CoinWrapper\CoinWrapper();

    // Get latest Ehterium quote with currency conversion to EUR
    $data = $cw->setCurrency("EUR")->getTickerData("Etherium");

    // Get latest Litecoin quote with debug mode enabled
    $data = $cw->setDebug(true)->getTickerData("LTC");

    // Get latest Ripple quote with debug mode enabled, add GBP currency conversion, and return data in JSON format
    $data = $cw->setDebug(true)->setCurrency("GBP")->getTickerData("XRP", true);

    // Get all tickers
    $data = $cw->getTickerData("LTC");
    
    // Refresh ticker repository and fetch a list of all possible tickers
    $cw->updateTickerRepo()->getTickers();
    
    // Get Global data (market cap, volume, active currencies number, etc)
    $cw->getGlobalData();
