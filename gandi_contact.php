<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

// quick and dirty create test contact

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

function createcontact () {
$contact_spec = array(
'given' => 'First',
'family' => 'Last',
'email' => 'your_email@your.domain',
'streetaddr' => '1212 Massachusetts Street',
'zip' => '02138',
'city' => 'Cambridge',
'country' => 'US',
'phone' => '+1-800-555-1212',
'type' => 0,
'password' => 'passwordything');

// end Things to configure

$contact_api = XML_RPC2_Client::create($xml_server, array('prefix' => 'contact.', 'sslverify' => false));

$contact = $contact_api->create($xml_apikey, $contact_spec);

if (isset($debug)) {print_r($contact['handle'])};
}

function chkcontact($domain, $contact, $xml_server, $xml_apikey) {
$contact_api = XML_RPC2_Client::create($xml_server, array('prefix' => 'contact.', 'sslverify' => false));
$association_spec = array(
        'domain' => $domain,
        'owner' => true,
        'admin' => true );
print_r( $contact_api->can_associate_domain($xml_apikey, $contact, $association_spec) );
}

function chkcontacts($domain, $contact, $xml_server, $xml_apikey) {
$contact_api = XML_RPC2_Client::create($xml_server, array('prefix' => 'contact.', 'sslverify' => false));
print_r( $contact_api->list($xml_apikey) );
print_r( $contact_api->info($xml_apikey) );
}

chkcontacts($domain, $contact, $xml_server, $xml_apikey);

