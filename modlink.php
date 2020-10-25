<?php

// $Id: modlink.php,v 1.1 2006/03/26 21:16:29 mikhail Exp $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://xoopscube.org>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //

include 'header.php';
$myts = MyTextSanitizer::getInstance(); // MyTextSanitizer object
require_once XOOPS_ROOT_PATH . '/class/xoopstree.php';
require_once XOOPS_ROOT_PATH . '/class/module.errorhandler.php';
$mytree = new XoopsTree($xoopsDB->prefix('bizdir_cat'), 'cid', 'pid');

if (!empty($_POST['submit'])) {
    $eh = new ErrorHandler(); //ErrorHandler object

    if (empty($xoopsUser)) {
        redirect_header(XOOPS_URL . '/user.php', 2, _MD_MUSTREGFIRST);

        exit();
    }

    $user = $xoopsUser->getVar('uid');

    $lid = (int)$_POST['lid'];

    // Check if Title exist

    if ('' == $_POST['title']) {
        $eh::show('1001');
    }

    // Check if URL exist

    //if ($_POST["url"]=="") {

    //$eh->show("1016");

    //}

    // Check if Description exist

    //if ($_POST['description']=="") {

    //$eh->show("1008");

    //}

    $url = $myts->addSlashes($_POST['url']);

    $cid = (int)$_POST['cid'];

    $title = $myts->addSlashes($_POST['title']);

    $description = $myts->addSlashes($_POST['description']);

    $newid = $xoopsDB->genId($xoopsDB->prefix('bizdir_mod') . '_requestid_seq');

    $sql = sprintf(
        "INSERT INTO %s (requestid, lid, cid, title, Contact, address, address2, city, state, zip, country, phone, fax, email, url, description, modifysubmitter) VALUES (%u, %u, %u, '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', %u)",
        $xoopsDB->prefix('bizdir_mod'),
        $newid,
        $lid,
        $cid,
        $title,
        $_POST['Contact'],
        $_POST['address'],
        $_POST['address2'],
        $_POST['city'],
        $_POST['state'],
        $_POST['zip'],
        $_POST['country'],
        $_POST['phone'],
        $_POST['fax'],
        $_POST['email'],
        $url,
        $description,
        $user
    );

    $xoopsDB->query($sql) or $eh::show('0013');

    $tags = [];

    $tags['MODIFYREPORTS_URL'] = XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/admin/index.php?op=listModReq';

    $notificationHandler = xoops_getHandler('notification');

    $notificationHandler->triggerEvent('global', 0, 'link_modify', $tags);

    redirect_header('index.php', 2, _MD_THANKSFORINFO);

    exit();
}
    $lid = (int)$_GET['lid'];
    if (empty($xoopsUser)) {
        redirect_header(XOOPS_URL . '/user.php', 2, _MD_MUSTREGFIRST);

        exit();
    }
    $GLOBALS['xoopsOption']['template_main'] = 'bizdir_modlink.html';
    require XOOPS_ROOT_PATH . '/header.php';

    $xoopsTpl->assign('module_name', $xoopsModule->name());

    $result = $xoopsDB->query('select cid, title, Contact, address, address2, city, state, zip, country, phone, fax, email, url, logourl from ' . $xoopsDB->prefix('bizdir_links') . " where lid=$lid and status>0");
    $xoopsTpl->assign('lang_requestmod', _MD_REQUESTMOD);
    [$cid, $title, $Contact, $address, $address2, $city, $state, $zip, $country, $phone, $fax, $email, $url, $logourl] = $xoopsDB->fetchRow($result);
    $result2 = $xoopsDB->query('SELECT description FROM ' . $xoopsDB->prefix('bizdir_text') . " WHERE lid=$lid");
    [$description] = $xoopsDB->fetchRow($result2);
    $xoopsTpl->assign(
        'link',
        [
            'id' => $lid,
            'rating' => number_format($rating, 2),
            'title' => htmlspecialchars($title, ENT_QUOTES | ENT_HTML5),
            'address' => htmlspecialchars($address, ENT_QUOTES | ENT_HTML5),
            'address2' => htmlspecialchars($address2, ENT_QUOTES | ENT_HTML5),
            'city' => htmlspecialchars($city, ENT_QUOTES | ENT_HTML5),
            'state' => htmlspecialchars($state, ENT_QUOTES | ENT_HTML5),
            'zip' => htmlspecialchars($zip, ENT_QUOTES | ENT_HTML5),
            'country' => htmlspecialchars($country, ENT_QUOTES | ENT_HTML5),
            'phone' => htmlspecialchars($phone, ENT_QUOTES | ENT_HTML5),
            'fax' => htmlspecialchars($fax, ENT_QUOTES | ENT_HTML5),
            'email' => htmlspecialchars($email, ENT_QUOTES | ENT_HTML5),
            'url' => htmlspecialchars($url, ENT_QUOTES | ENT_HTML5),
            '$logourl' => htmlspecialchars($logourl, ENT_QUOTES | ENT_HTML5),
            'updated' => formatTimestamp($time, 'm'),
            'description' => htmlspecialchars($description, ENT_QUOTES | ENT_HTML5),
            'adminlink' => $adminlink,
            'hits' => $hits,
            'votes' => $votestring,
        ]
    );
    $xoopsTpl->assign('lang_linkid', _MD_LINKID);
    $xoopsTpl->assign('lang_sitetitle', _MD_SITETITLE);
    $xoopsTpl->assign('lang_contact', _MD_CONTACT);
    $xoopsTpl->assign('lang_siteaddress', _MD_BUSADDRESS);
    $xoopsTpl->assign('lang_siteaddress2', _MD_BUSADDRESS2);
    $xoopsTpl->assign('lang_sitecity', _MD_BUSCITY);
    $xoopsTpl->assign('lang_sitestate', _MD_BUSSTATE);
    $xoopsTpl->assign('lang_sitezip', _MD_BUSZIP);
    $xoopsTpl->assign('lang_country', _MD_BUSCOUNTRY);
    $xoopsTpl->assign('lang_sitephone', _MD_BUSPHONE);
    $xoopsTpl->assign('lang_fax', _MD_BUSFAX);
    $xoopsTpl->assign('lang_email', _MD_BUSEMAIL);
    $xoopsTpl->assign('lang_siteurl', _MD_SITEURL);
    $xoopsTpl->assign('lang_category', _MD_CATEGORYC);
    ob_start();
    $mytree->makeMySelBox('title', 'title', $cid);
    $selbox = ob_get_contents();
    ob_end_clean();
    $xoopsTpl->assign('category_selbox', $selbox);
    $xoopsTpl->assign('lang_description', _MD_DESCRIPTIONC);
    $xoopsTpl->assign('lang_sendrequest', _MD_SENDREQUEST);
    $xoopsTpl->assign('lang_cancel', _CANCEL);
    require XOOPS_ROOT_PATH . '/footer.php';
