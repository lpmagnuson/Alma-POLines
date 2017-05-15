<?php
//Load Composer Dependencies (Guzzle)
require('vendor/autoload.php');
require_once('../TCPDF/tcpdf.php');
require('config.php');

/*Todo
Pull back POs in reverse order (most recent first)
Filter by vendor
Enter specific POL
*/



//pull back all PO lines to enable selection
use Guzzle\Http\Client;
$url = 'https://api-na.hosted.exlibrisgroup.com/almaws/v1/acq/po-lines?' . urlencode('status') . '=' . urlencode('ALL') . '&' . urlencode('limit') . '=' . urlencode('10') . '&' . urlencode('offset') . '=' . urlencode('0') . '&' . urlencode('order_by') . '=' . urlencode('title') . '&' . urlencode('direction') . '=' . urlencode('desc') . '&' . urlencode('acquisition_method') . '=' . urlencode('ALL') . '&' . urlencode('apikey') . '=' . urlencode($exl_key);
$client = new GuzzleHttp\Client();
$response = $client->get($url);

		$xml = $response->getBody(TRUE);
        $xmlObj = simplexml_load_string($xml);
        echo "<h1>Choose PO Lines to Print</h1>";
        echo "<form action=\"pol.php\" method=\"post\">";
          foreach ($xmlObj->po_line as $pol) {
            $polnum = $pol->number; 
            $vendor = $pol->vendor;   
            echo "<input type=\"checkbox\" name=\"formPO\" value=\"". $polnum . "\"/>" . $polnum . " | Vendor: " . $vendor . "<br />";
            }
        echo "<input type=\"submit\" name=\"formSubmit\" value=\"Submit\" /> </form>";

?>