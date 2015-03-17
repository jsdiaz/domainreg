<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

// this is a simple script that runs in a loop to watch a domain that is becoming
// available soon and attempt to register it as soon as it is released.  There must be
// valid contact and enough credit to register the domain for this to work.

// things to configure
// domain to register
$domain = 'replace.com';

// email of admin
$admin_email = 'replace_with@your.email';

// test server and creds
//$contact = 'replace-GANDI';
//$xml_apikey = 'replace_with_your_api_key';
//$xml_server = 'https://rpc.ote.gandi.net/xmlrpc/';
//$nameservers = array('a.dns.gandi-ote.net', 'b.dns.gandi-ote.net', 'c.dns.gandi-ote.net');

// prod server and creds
$contact = 'replace-GANDI';
$xml_apikey = 'replace_with_your_api_key';
$xml_server = 'https://rpc.gandi.net/xmlrpc/';
$nameservers = array('a.dns.gandi.net', 'b.dns.gandi.net', 'c.dns.gandi.net');

// enable debug
// $debug = 1;

// end Things to configure

// Library installed from PEAR
require_once 'XML/RPC2/Client.php';

function checkdomain($domain, $xml_server, $xml_apikey) {
	$xml_options =  array( 'prefix' => 'domain.', 'sslverify' => false );

	$domain_api = XML_RPC2_Client::create( $xml_server, $xml_options );
	$result = $domain_api->available($xml_apikey, array($domain));

	while ( $result[$domain] == 'pending') {
    usleep(700000);
		$result = $domain_api->available($xml_apikey, array($domain));
	}

	if (isset($debug)) {print_r($result)};
	return $result;
}

function chkcontact($domain, $contact, $xml_server, $xml_apikey) {
	$contact_api = XML_RPC2_Client::create($xml_server, array('prefix' => 'contact.', 'sslverify' => false));
	$association_spec = array(
	 	'domain' => $domain,
		'owner' => true,
		'admin' => true );
	print_r( $contact_api->can_associate_domain($xml_apikey, $contact, $association_spec) );
}

function registerdomain($domain, $contact, $nameservers, $xml_server, $xml_apikey) {
	$xml_options =  array( 'prefix' => 'domain.', 'sslverify' => false );
	$domain_api = XML_RPC2_Client::create( $xml_server, $xml_options );
	$domain_spec = array(
		'owner' => $contact,
		'admin' => $contact,
		'bill' => $contact,
		'tech' => $contact,
		'nameservers' => $nameservers,
		'duration' => 1);
	$op = $domain_api->__call('create', array($xml_apikey, $domain, $domain_spec));
	return $op;
}

do {
$chk_result = checkdomain($domain, $xml_server, $xml_apikey);
if (isset($debug)) {print_r($result[$domain])};
if ($chk_result[$domain] == 'available') {
	print "domain available\n";
	$xml_options =  array( 'prefix' => 'operation.', 'sslverify' => false );
	$operation_api = XML_RPC2_Client::create( $xml_server, $xml_options );
	$reg_result = registerdomain($domain, $contact, $nameservers, $xml_server, $xml_apikey);
	$reg_result = $operation_api->info($xml_apikey, $reg_result['id']);
    if (isset($debug)) {echo $reg_result['step']};
	print "domain registered\n";
	$message = "Domain $domain was registered.\r\rReturned:\r" . print_r($reg_result, true);
	mail($admin_email,'domain registration',$message);
    if (isset($debug)) {print_r ($message)};
	}
if ($chk_result[$domain] == 'unavailable') {
	print "domain unavailable at " . date("D M d, Y G:i:s a") . "\n";
    sleep(1);
	}
} while ($chk_result[$domain] == 'unavailable');
