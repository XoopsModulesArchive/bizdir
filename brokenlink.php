<?php

// $Id: brokenlink.php,v 1.1 2006/03/26 21:16:29 mikhail Exp $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <https://www.xoops.org>                             //
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

if (!empty($_POST['submit'])) {
    if (empty($xoopsUser)) {
        $sender = 0;
    } else {
        $sender = $xoopsUser->getVar('uid');
    }

    $lid = (int)$_POST['lid'];

    $ip = getenv('REMOTE_ADDR');

    if (0 != $sender) {
        // Check if REG user is trying to report twice.

        $result = $xoopsDB->query('SELECT COUNT(*) FROM ' . $xoopsDB->prefix('bizdir_broken') . ' WHERE lid=' . $lid . ' AND sender=' . $sender . '');

        [$count] = $xoopsDB->fetchRow($result);

        if ($count > 0) {
            redirect_header('index.php', 2, _MD_ALREADYREPORTED);

            exit();
        }
    } else {
        // Check if the sender is trying to vote more than once.

        $result = $xoopsDB->query('SELECT COUNT(*) FROM ' . $xoopsDB->prefix('bizdir_broken') . " WHERE lid=$lid AND ip = '$ip'");

        [$count] = $xoopsDB->fetchRow($result);

        if ($count > 0) {
            redirect_header('index.php', 2, _MD_ALREADYREPORTED);

            exit();
        }
    }

    $newid = $xoopsDB->genId($xoopsDB->prefix('bizdir_broken') . '_reportid_seq');

    $sql = sprintf("INSERT INTO %s (reportid, lid, sender, ip) VALUES (%u, %u, %u, '%s')", $xoopsDB->prefix('bizdir_broken'), $newid, $lid, $sender, $ip);

    $xoopsDB->query($sql) || exit();

    $tags = [];

    $tags['BROKENREPORTS_URL'] = XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/admin/index.php?op=listBrokenLinks';

    $notificationHandler = xoops_getHandler('notification');

    $notificationHandler->triggerEvent('global', 0, 'link_broken', $tags);

    redirect_header('index.php', 2, _MD_THANKSFORINFO);

    exit();
}
    $GLOBALS['xoopsOption']['template_main'] = 'bizdir_brokenlink.html';
    require XOOPS_ROOT_PATH . '/header.php';
    $xoopsTpl->assign('lang_reportbroken', _MD_REPORTBROKEN);
    $xoopsTpl->assign('link_id', (int)$_GET['lid']);
    $xoopsTpl->assign('lang_thanksforhelp', _MD_THANKSFORHELP);
    $xoopsTpl->assign('lang_forsecurity', _MD_FORSECURITY);
    $xoopsTpl->assign('lang_cancel', _MD_CANCEL);
    require_once XOOPS_ROOT_PATH . '/footer.php';
