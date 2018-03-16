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

    // Return JSON instead of PHP objects
    $data = $cw->getTickerData("Etherium", true);

    // Get latest data for top 10 tickers
    $data = $cw->getAllTickersData(10);

    // Pagination
    $page1 = $cw->getAllTickersData(10, 0);
    $page2 = $cw->getAllTickersData(10, 10);

    // Currency Conversion
    $data = $cw->setCurrency("EUR")->getTickerData("Etherium");

    // Debug Mode
    $data = $cw->setDebug(true)->getTickerData("LTC");

    // Get latest Ripple quote with debug mode enabled, add GBP currency conversion, and return data in JSON format
    $data = $cw->setDebug(true)->setCurrency("GBP")->getTickerData("XRP", true);
    
    // Refresh ticker repository and fetch a list of all possible tickers
    $cw->updateTickerRepo()->getTickers();
    
    // Get Global data (market cap, volume, active currencies number, etc)
    $cw->getGlobalData();
