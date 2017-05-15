<?php

/*Todo
Iterate over multiple orders to generate multiple PDFs at once
Clean up display - title preceeding :, total needs two decimal places
*/

require_once('../TCPDF/tcpdf.php');
require_once('../TCPDF/examples/tcpdf_include.php');
require('config.php');

if(isset($_POST['formPO'])) {
	$selectedPO	= ($_POST['formPO']);
		$ch = curl_init();
			$url = 'https://api-na.hosted.exlibrisgroup.com/almaws/v1/acq/po-lines/{po_line_id}';
			$templateParamNames = array('{po_line_id}');
			$templateParamValues = array(urlencode($selectedPO));
			$url = str_replace($templateParamNames, $templateParamValues, $url);
			$queryParams = '?' . urlencode('apikey') . '=' . urlencode($exl_key);
			curl_setopt($ch, CURLOPT_URL, $url . $queryParams);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_HEADER, FALSE);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
			$response = curl_exec($ch);
		curl_close($ch);
		}

$xml = simplexml_load_string($response);

$PO_number = $xml->number[0];
$odate = $xml->status_date[0];
$vendor = $xml->vendor[0];
$title =  $xml->resource_metadata->title[0];
$author = $xml->resource_metadata->author[0];
$pubplace = $xml->resource_metadata->publication_place[0];
$publisher = $xml->resource_metadata->publisher[0];
$pubyear = $xml->resource_metadata->publication_year[0];
$vennote = $xml->vendor_note[0];
$status = $xml->status_date[0];
$total = $xml->fund_distributions->fund_distribution->amount->sum[0];


//============================================================+
// START TCPDF
//============================================================+


//Write HTML For PDF content

	$html = "<p>PO Number: " . $PO_number . "<br />";
	$html = $html . "Order Date: " . $odate . "</p>";
	$html = $html . "<p>Vendor Name: " . $vendor . "</p>";
	$html = $html . "<p>: " . $title . ". " . $author . ". " . $pubplace . ". " . $pubyear . "</p>";
	$html = $html . "<p>1 copy; " . $vennote . ".</p>";
	$html = $html . "<p style=\"text-align: right\">Total: $" . $total . ".</p>";
	$html = $html . $ship;
	$html = $html . $certify;
	$html = $html . "<p>Certifying Officer: ________________________________________________________ </p>";
	$html = $html . $contact;

//Generate PDF with TCPDF
	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('System');
	$pdf->SetTitle('PO_Line');

// remove default header/footer
	$pdf->setPrintHeader(false);
	$pdf->setPrintFooter(false);

// set default monospaced font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

// set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
	if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    	require_once(dirname(__FILE__).'/lang/eng.php');
    	$pdf->setLanguageArray($l);
	}

// ---------------------------------------------------------

// set font
	$pdf->SetFont('times', '', 12);

// add a page
	$pdf->AddPage();

//Write HTML defined above
	$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

// ---------------------------------------------------------

//Close and output PDF document
	$pdf->Output('PrintedPO.pdf', 'I');

//============================================================+
// END OF TCPDF
//============================================================+

?>