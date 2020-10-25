<?php

include 'header.php';
require 'include/vcard.inc.php';

$myts = MyTextSanitizer::getInstance(); // MyTextSanitizer object

$lid = (int)$_GET['lid'];

$result = $xoopsDB->query(
    'select l.lid, l.cid, l.title, l.Contact, l.address, l.address2, l.city, l.state, l.zip, l.country, l.phone, l.fax, l.email, l.url, l.logourl, l.status, l.date, l.hits, l.rating, l.votes, l.comments, t.description from ' . $xoopsDB->prefix('bizdir_links') . ' l, ' . $xoopsDB->prefix(
        'bizdir_text'
    ) . " t where l.lid=$lid and l.lid=t.lid and status>0"
);

[$lid, $cid, $ltitle, $Contact, $address, $address2, $city, $state, $zip, $country, $phone, $fax, $email, $url, $logourl, $status, $time, $hits, $rating, $votes, $comments, $description] = $xoopsDB->fetchRow($result);

$v = new vCard();

if (!empty($Contact)) {
    $v->setFormattedName($Contact);
}

if (!empty($phone)) {
    $v->setPhoneNumber($phone, 'PREF;WORK;VOICE');
}

if (!empty($fax)) {
    $v->setPhoneNumber($fax, 'WORK;FAX');
}

$v->setOrg($ltitle);

if (!empty($address2)) {
    $address .= "\r\n$address2";
}

$v->setAddress('', '', $address, $city, $state, $zip, $country, 'WORK;POSTAL');

$v->setLabel('', '', $address, $city, $state, $zip, $country, 'WORK;POSTAL');

if (!empty($email)) {
    $v->setEmail($email);
}

if (!empty($description)) {
    $v->setNote(strip_tags($myts->xoopsCodeDecode($description)));
}

if (!empty($url)) {
    $v->setURL($url, 'WORK');
}

$output = $v->getVCard();

$filename = $v->getFileName();

header("Content-Disposition: attachment; filename=$filename");
header('Content-Length: ' . mb_strlen($output));
header('Connection: close');
header("Content-Type: text/x-vCard; name=$filename");

echo $output;
