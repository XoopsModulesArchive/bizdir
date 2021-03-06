<?php

// $Id: submit.php,v 1.1 2006/03/26 21:16:29 mikhail Exp $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://xoopscube.org>                             //
// ------------------------------------------------------------------------- //
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
require_once XOOPS_ROOT_PATH . '/include/xoopscodes.php';

$eh = new ErrorHandler(); //ErrorHandler object
$mytree = new XoopsTree($xoopsDB->prefix('bizdir_cat'), 'cid', 'pid');

if (empty($xoopsUser) and !$xoopsModuleConfig['anonpost']) {
    redirect_header(XOOPS_URL . '/user.php', 2, _MD_MUSTREGFIRST);

    exit();
}

if (!empty($_POST['submit'])) {
    $submitter = !empty($xoopsUser) ? $xoopsUser->getVar('uid') : 0;

    // RMV - why store submitter on form??

    //if (!$_POST['submitter'] and $xoopsUser) {

    //   $submitter = $xoopsUser->uid();

    //}elseif(!$_POST['submitter'] and !$xoopsUser) {

    //	$submitter = 0;

    //}else{

    //	$submitter = intval($_POST['submitter']);

    //}

    // Check if Title exist

    if ('' == $_POST['title']) {
        $eh::show('1001');
    }

    // Check if URL exist

    //$url = $_POST["url"];

    //if ($url=="" || !isset($url)) {

    //   	$eh->show("1016");

    //}

    // Check if Description exist

    //if ($_POST['message']=="") {

    //  	$eh->show("1008");

    //}

    $notify = !empty($_POST['notify']) ? 1 : 0;

    if (!empty($_POST['cid'])) {
        $cid = (int)$_POST['cid'];
    } else {
        $cid = 0;
    }

    $url = '';

    if ('' != $_POST['url'] && 'http://' != $_POST['url']) {
        $url = $myts->addSlashes($_POST['url']);
    }

    $title = $myts->addSlashes($_POST['title']);

    $description = $myts->addSlashes($_POST['message']);

    $date = time();

    $newid = $xoopsDB->genId($xoopsDB->prefix('bizdir_links') . '_lid_seq');

    if (1 == $xoopsModuleConfig['autoapprove']) {
        // RMV-FIXME: shouldn't this be 'APPROVE' or something (also in mydl)?

        $status = $xoopsModuleConfig['autoapprove'];
    } else {
        $status = 0;
    }

    $sql = sprintf(
        "INSERT INTO %s (lid, cid, title, Contact, address, address2, city, state, zip, country, phone, fax, email, url, submitter, status, date, hits, rating, votes, comments) VALUES (%u, %u, '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', %u, %u, %u, %u, %u, %u, %u)",
        $xoopsDB->prefix('bizdir_links'),
        $newid,
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
        $submitter,
        $status,
        $date,
        0,
        0,
        0,
        0
    );

    $xoopsDB->query($sql) or $eh::show('0014');

    if (0 == $newid) {
        $newid = $xoopsDB->getInsertId();
    }

    $sql = sprintf("INSERT INTO %s (lid, description) VALUES (%u, '%s')", $xoopsDB->prefix('bizdir_text'), $newid, $description);

    $xoopsDB->query($sql) or $eh::show('0013');

    // RMV-NEW

    // Notify of new link (anywhere) and new link in category.

    $notificationHandler = xoops_getHandler('notification');

    $tags = [];

    $tags['LINK_NAME'] = $title;

    $tags['LINK_URL'] = XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/singlelink.php?cid=' . $cid . '&lid=' . $newid;

    $sql = 'SELECT title FROM ' . $xoopsDB->prefix('bizdir_cat') . ' WHERE cid=' . $cid;

    $result = $xoopsDB->query($sql);

    $row = $xoopsDB->fetchArray($result);

    $tags['CATEGORY_NAME'] = $row['title'];

    $tags['CATEGORY_URL'] = XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/viewcat.php?cid=' . $cid;

    if (1 == $xoopsModuleConfig['autoapprove']) {
        $notificationHandler->triggerEvent('global', 0, 'new_link', $tags);

        $notificationHandler->triggerEvent('category', $cid, 'new_link', $tags);

        redirect_header('index.php', 2, _MD_RECEIVED . '<br>' . _MD_ISAPPROVED . '');
    } else {
        $tags['WAITINGLINKS_URL'] = XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/admin/index.php?op=listNewLinks';

        $notificationHandler->triggerEvent('global', 0, 'link_submit', $tags);

        $notificationHandler->triggerEvent('category', $cid, 'link_submit', $tags);

        if ($notify) {
            require_once XOOPS_ROOT_PATH . '/include/notification_constants.php';

            $notificationHandler->subscribe('link', $newid, 'approve', XOOPS_NOTIFICATION_MODE_SENDONCETHENDELETE);
        }

        redirect_header('index.php', 2, _MD_RECEIVED);
    }

    exit();
}
    $GLOBALS['xoopsOption']['template_main'] = 'bizdir_submit.html';
    require XOOPS_ROOT_PATH . '/header.php';

    $xoopsTpl->assign('module_name', $xoopsModule->name());

    ob_start();
    xoopsCodeTarea('message', 37, 8);
    $xoopsTpl->assign('xoops_codes', ob_get_contents());
    ob_end_clean();
    ob_start();
    xoopsSmilies('message');
    $xoopsTpl->assign('xoops_smilies', ob_get_contents());
    ob_end_clean();
    $xoopsTpl->assign('notify_show', !empty($xoopsUser) && !$xoopsModuleConfig['autoapprove'] ? 1 : 0);
    $xoopsTpl->assign('lang_submitonce', _MD_SUBMITONCE);
    $xoopsTpl->assign('lang_submitlinkh', _MD_SUBMITLINKHEAD);
    $xoopsTpl->assign('lang_allpending', _MD_ALLPENDING);
    $xoopsTpl->assign('lang_dontabuse', _MD_DONTABUSE);
    $xoopsTpl->assign('lang_wetakeshot', _MD_TAKESHOT);
    $xoopsTpl->assign('lang_sitetitle', _MD_SITETITLE);
    $xoopsTpl->assign('lang_contact', _MD_CONTACT);
    $xoopsTpl->assign('lang_busaddress', _MD_BUSADDRESS);
    $xoopsTpl->assign('lang_busaddress2', _MD_BUSADDRESS2);
    $xoopsTpl->assign('lang_buscity', _MD_BUSCITY);
    $xoopsTpl->assign('lang_busstate', _MD_BUSSTATE);
    $xoopsTpl->assign('lang_buszip', _MD_BUSZIP);
    $xoopsTpl->assign('lang_buscountry', _MD_BUSCOUNTRY);
    $xoopsTpl->assign('lang_busphone', _MD_BUSPHONE);
    $xoopsTpl->assign('lang_busfax', _MD_BUSFAX);
    $xoopsTpl->assign('lang_busemail', _MD_BUSEMAIL);
    $xoopsTpl->assign('lang_siteurl', _MD_SITEURL);
    $xoopsTpl->assign('lang_category', _MD_CATEGORYC);
    $xoopsTpl->assign('lang_options', _MD_OPTIONS);
    $xoopsTpl->assign('lang_notify', _MD_NOTIFYAPPROVE);
    $xoopsTpl->assign('lang_description', _MD_DESCRIPTIONC);
    $xoopsTpl->assign('lang_descripthelp', _MD_DESCRIPTHELP);
    $xoopsTpl->assign('lang_submit', _SUBMIT);
    $xoopsTpl->assign('lang_cancel', _CANCEL);
    ob_start();
    $mytree->makeMySelBox('title', 'title', 0, 1);
    $selbox = ob_get_contents();
    ob_end_clean();
    $xoopsTpl->assign('category_selbox', $selbox);

    $prefillCountry = '';
    $prefillProvince = '';
    $prefillAreaCode = '';

    if (1 == $xoopsModuleConfig['prefillDefaults']) {
        $prefillCountry = $xoopsModuleConfig['defaultCountry'];

        $prefillProvince = $xoopsModuleConfig['defaultProvince'];

        $prefillAreaCode = $xoopsModuleConfig['defaultAreaCode'];
    }

    $xoopsTpl->assign('prefill_country', $prefillCountry);
    $xoopsTpl->assign('prefill_province', $prefillProvince);
    $xoopsTpl->assign('prefill_area_code', $prefillAreaCode);

    require XOOPS_ROOT_PATH . '/footer.php';
