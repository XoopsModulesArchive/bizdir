<?php

// $Id: singlelink.php,v 1.1 2006/03/26 21:16:29 mikhail Exp $
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
$mytree = new XoopsTree($xoopsDB->prefix('bizdir_cat'), 'cid', 'pid');
$lid = (int)$_GET['lid'];
$cid = (int)$_GET['cid'];
$sql = sprintf('UPDATE %s SET hits = hits+1 WHERE lid = %u AND status > 0', $xoopsDB->prefix('bizdir_links'), $lid);
$xoopsDB->queryF($sql);
$GLOBALS['xoopsOption']['template_main'] = 'bizdir_singlelink.html';
require XOOPS_ROOT_PATH . '/header.php';

$xoopsTpl->assign('module_name', $xoopsModule->name());

$result = $xoopsDB->query(
    'select l.lid, l.cid, l.title, l.Contact, l.address, l.address2, l.city, l.state, l.zip, l.country, l.phone, l.fax, l.email, l.url, l.logourl, l.status, l.date, l.hits, l.rating, l.votes, l.comments, t.description from ' . $xoopsDB->prefix('bizdir_links') . ' l, ' . $xoopsDB->prefix(
        'bizdir_text'
    ) . " t where l.lid=$lid and l.lid=t.lid and status>0"
);
[$lid, $cid, $ltitle, $Contact, $address, $address2, $city, $state, $zip, $country, $phone, $fax, $email, $url, $logourl, $status, $time, $hits, $rating, $votes, $comments, $description] = $xoopsDB->fetchRow($result);

$pathstring = "<a href='index.php'>" . _MD_MAIN . '</a> : ';
$pathstring .= $mytree->getNicePathFromId($cid, 'title', 'viewcat.php?op=');
$xoopsTpl->assign('category_path', $pathstring);

if ($xoopsUser && $xoopsUser->isAdmin($xoopsModule->mid())) {
    $adminlink = '<a href="admin/index.php?op=modLink&lid=' . $lid . '"><img src="images/editicon.gif" border="0" alt="' . _MD_EDITTHISLINK . '"></a>';
} else {
    $adminlink = '';
}
if (1 == $votes) {
    $votestring = _MD_ONEVOTE;
} else {
    $votestring = sprintf(_MD_NUMVOTES, $votes);
}

if (1 == $xoopsModuleConfig['useshots']) {
    $xoopsTpl->assign('shotwidth', $xoopsModuleConfig['shotwidth']);

    $xoopsTpl->assign('tablewidth', $xoopsModuleConfig['shotwidth'] + 10);

    $xoopsTpl->assign('show_screenshot', true);

    $xoopsTpl->assign('lang_noscreenshot', _MD_NOSHOTS);
}
$path = $mytree->getPathFromId($cid, 'title');
$path = mb_substr($path, 1);
$path = str_replace('/', " <img src='images/arrow.gif' board='0' alt=''> ", $path);
$new = newlinkgraphic($time, $status);
$pop = popgraphic($hits);
$xoopsTpl->assign(
    'link',
    [
        'id' => $lid,
        'cid' => $cid,
        'rating' => number_format($rating, 2),
        'url' => $url,
        'title' => htmlspecialchars($ltitle, ENT_QUOTES | ENT_HTML5) . $new . $pop,
        'Contact' => htmlspecialchars($Contact, ENT_QUOTES | ENT_HTML5),
        'address' => htmlspecialchars($address, ENT_QUOTES | ENT_HTML5),
        'address2' => htmlspecialchars($address2, ENT_QUOTES | ENT_HTML5),
        'city' => htmlspecialchars($city, ENT_QUOTES | ENT_HTML5),
        'state' => htmlspecialchars($state, ENT_QUOTES | ENT_HTML5),
        'zip' => htmlspecialchars($zip, ENT_QUOTES | ENT_HTML5),
        'country' => htmlspecialchars($country, ENT_QUOTES | ENT_HTML5),
        'phone' => htmlspecialchars($phone, ENT_QUOTES | ENT_HTML5),
        'fax' => htmlspecialchars($fax, ENT_QUOTES | ENT_HTML5),
        'email' => htmlspecialchars($email, ENT_QUOTES | ENT_HTML5),
        'category' => $path,
        'logourl' => htmlspecialchars($logourl, ENT_QUOTES | ENT_HTML5),
        'updated' => formatTimestamp($time, 'm'),
        'description' => $myts->displayTarea($description, 0),
        'adminlink' => $adminlink,
        'hits' => $hits,
        'votes' => $votestring,
        'comments' => $comments,
        'mail_subject' => rawurlencode(sprintf(_MD_INTRESTLINK, $xoopsConfig['sitename'])),
        'mail_body' => rawurlencode(sprintf(_MD_INTLINKFOUND, $xoopsConfig['sitename']) . ':  ' . XOOPS_URL . '/modules/bizdir/singlelink.php?lid=' . $lid),
    ]
);
$xoopsTpl->assign('lang_contact', _MD_CONTACT);
$xoopsTpl->assign('lang_description', _MD_DESCRIPTIONC);
$xoopsTpl->assign('lang_lastupdate', _MD_LASTUPDATEC);
$xoopsTpl->assign('lang_hits', _MD_HITSC);
$xoopsTpl->assign('lang_rating', _MD_RATINGC);
$xoopsTpl->assign('lang_ratethissite', _MD_RATETHISSITE);
$xoopsTpl->assign('lang_tellafriend', _MD_TELLAFRIEND);
$xoopsTpl->assign('lang_modify', _MD_MODIFY);
$xoopsTpl->assign('lang_category', _MD_CATEGORYC);
$xoopsTpl->assign('lang_visit', _MD_VISIT);
$xoopsTpl->assign('lang_comments', _COMMENTS);
$xoopsTpl->assign('lang_phone', _MD_BUSPHONE);
$xoopsTpl->assign('lang_fax', _MD_BUSFAX);
$xoopsTpl->assign('lang_email', _MD_BUSEMAIL);
$xoopsTpl->assign('lang_url', _MD_BUSURL);
$xoopsTpl->assign('lang_getmap', _MD_GETMAP);
$xoopsTpl->assign('lang_emailcontact', _MD_EMAILCONTACT);
require XOOPS_ROOT_PATH . '/include/comment_view.php';
require XOOPS_ROOT_PATH . '/footer.php';
