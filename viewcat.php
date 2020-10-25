<?php

// $Id: viewcat.php,v 1.1 2006/03/26 21:16:29 mikhail Exp $
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

$cid = (int)$_GET['cid'];
$GLOBALS['xoopsOption']['template_main'] = 'bizdir_viewcat.html';
require XOOPS_ROOT_PATH . '/header.php';

$xoopsTpl->assign('module_name', $xoopsModule->name());

if (isset($_GET['show'])) {
    $show = (int)$_GET['show'];
} else {
    $show = $xoopsModuleConfig['perpage'];
}
$min = isset($_GET['min']) ? (int)$_GET['min'] : 0;
if (!isset($max)) {
    $max = $min + $show;
}
if (isset($_GET['orderby'])) {
    $orderby = convertorderbyin($_GET['orderby']);
} else {
    $orderby = 'title ASC';
}

$pathstring = "<a href='index.php'>" . _MD_MAIN . '</a> : ';
$pathstring .= $mytree->getNicePathFromId($cid, 'title', 'viewcat.php?op=');
$xoopsTpl->assign('category_path', $pathstring);
$xoopsTpl->assign('category_id', $cid);
// get child category objects
$arr = [];
$arr = $mytree->getFirstChild($cid, 'title');
if (count($arr) > 0) {
    $scount = 1;

    foreach ($arr as $ele) {
        $sub_arr = [];

        $sub_arr = $mytree->getFirstChild($ele['cid'], 'title');

        $space = 0;

        $chcount = 0;

        $infercategories = '';

        foreach ($sub_arr as $sub_ele) {
            $chtitle = htmlspecialchars($sub_ele['title'], ENT_QUOTES | ENT_HTML5);

            if ($chcount > 5) {
                $infercategories .= '...';

                break;
            }

            if ($space > 0) {
                $infercategories .= ', ';
            }

            $infercategories .= '<a href="viewcat.php?cid=' . $sub_ele['cid'] . '">' . $chtitle . '</a>';

            $space++;

            $chcount++;
        }

        $xoopsTpl->append('subcategories', ['title' => htmlspecialchars($ele['title'], ENT_QUOTES | ENT_HTML5), 'id' => $ele['cid'], 'infercategories' => $infercategories, 'totallinks' => getTotalItems($ele['cid'], 1), 'count' => $scount]);

        $scount++;
    }
}

if (1 == $xoopsModuleConfig['useshots']) {
    $xoopsTpl->assign('shotwidth', $xoopsModuleConfig['shotwidth']);

    $xoopsTpl->assign('tablewidth', $xoopsModuleConfig['shotwidth'] + 10);

    $xoopsTpl->assign('show_screenshot', true);

    $xoopsTpl->assign('lang_noscreenshot', _MD_NOSHOTS);
}

if (!empty($xoopsUser) && $xoopsUser->isAdmin($xoopsModule->mid())) {
    $isadmin = true;
} else {
    $isadmin = false;
}
$fullcountresult = $xoopsDB->query('select count(*) from ' . $xoopsDB->prefix('bizdir_links') . " where cid=$cid and status>0");
[$numrows] = $xoopsDB->fetchRow($fullcountresult);
$page_nav = '';
if ($numrows > 0) {
    $xoopsTpl->assign('lang_description', _MD_DESCRIPTIONC);

    $xoopsTpl->assign('lang_lastupdate', _MD_LASTUPDATEC);

    $xoopsTpl->assign('lang_hits', _MD_HITSC);

    $xoopsTpl->assign('lang_rating', _MD_RATINGC);

    $xoopsTpl->assign('lang_ratethissite', _MD_RATETHISSITE);

    $xoopsTpl->assign('lang_tellafriend', _MD_TELLAFRIEND);

    $xoopsTpl->assign('lang_modify', _MD_MODIFY);

    $xoopsTpl->assign('lang_category', _MD_CATEGORYC);

    $xoopsTpl->assign('lang_visit', _MD_VISIT);

    $xoopsTpl->assign('show_links', true);

    $xoopsTpl->assign('lang_comments', _COMMENTS);

    $xoopsTpl->assign('lang_phone', _MD_BUSPHONE);

    $xoopsTpl->assign('lang_fax', _MD_BUSFAX);

    $xoopsTpl->assign('lang_email', _MD_BUSEMAIL);

    $xoopsTpl->assign('lang_url', _MD_SITEURL);

    $xoopsTpl->assign('lang_getmap', _MD_GETMAP);

    $xoopsTpl->assign('lang_emailcontact', _MD_EMAILCONTACT);

    $sql = 'select l.lid, l.cid, l.title, l.address, l.address2, l.city, l.state, l.zip, l.country, l.phone, l.fax, l.email, l.url, l.logourl, l.status, l.date, l.hits, l.rating, l.votes, l.comments, t.description from '
              . $xoopsDB->prefix('bizdir_links')
              . ' l, '
              . $xoopsDB->prefix('bizdir_text')
              . " t where cid=$cid and l.lid=t.lid and status>0 order by $orderby";

    $result = $xoopsDB->query($sql, $show, $min);

    //if 2 or more items in result, show the sort menu

    if ($numrows > 1) {
        $xoopsTpl->assign('show_nav', true);

        $orderbyTrans = convertorderbytrans($orderby);

        $xoopsTpl->assign('lang_sortby', _MD_SORTBY);

        $xoopsTpl->assign('lang_title', _MD_TITLE);

        $xoopsTpl->assign('lang_titleatoz', _MD_TITLEATOZ);

        $xoopsTpl->assign('lang_titleztoa', _MD_TITLEZTOA);

        $xoopsTpl->assign('lang_date', _MD_DATE);

        $xoopsTpl->assign('lang_dateold', _MD_DATEOLD);

        $xoopsTpl->assign('lang_datenew', _MD_DATENEW);

        $xoopsTpl->assign('lang_rating', _MD_RATING);

        $xoopsTpl->assign('lang_ratinglow', _MD_RATINGLTOH);

        $xoopsTpl->assign('lang_ratinghigh', _MD_RATINGHTOL);

        $xoopsTpl->assign('lang_popularity', _MD_POPULARITY);

        $xoopsTpl->assign('lang_popularityleast', _MD_POPULARITYLTOM);

        $xoopsTpl->assign('lang_popularitymost', _MD_POPULARITYMTOL);

        $xoopsTpl->assign('lang_cursortedby', sprintf(_MD_CURSORTEDBY, convertorderbytrans($orderby)));
    }

    while (list($lid, $cid, $ltitle, $address, $address2, $city, $state, $zip, $country, $phone, $fax, $email, $url, $logourl, $status, $time, $hits, $rating, $votes, $comments, $description) = $xoopsDB->fetchRow($result)) {
        $result2 = $xoopsDB->query(
            'select l.lid, l.cid, l.title, l.address, l.address2, l.city, l.state, l.zip, l.country, l.phone, l.fax, l.email, l.url, l.logourl, l.status, l.date, l.hits, l.rating, l.votes, l.comments, t.description from '
            . $xoopsDB->prefix('bizdir_links')
            . ' l, '
            . $xoopsDB->prefix('bizdir_text')
            . " t where cid=$cid and l.lid=t.lid and status>0 order by $orderby"
        );

        [$premlid, $premcid, $premtitle, $premaddress, $premaddress2, $premcity, $premstate, $premzip, $premcountry, $premphone, $premfax, $prememail, $premurl, $premlogourl, $premstatus, $premdate, $premhits, $premrating, $premvotes, $premcomments, $premdescription] = $xoopsDB->fetchRow($result2);

        //			$result2 = $xoopsDB->query("select description from ".$xoopsDB->prefix("bizdir_text")." where lid=$lid");

        //			list($origdescription) = $xoopsDB->fetchRow($result2);

        if ($isadmin) {
            $adminlink = '<a href="admin/index.php?op=modLink&lid=' . $lid . '"><img src="images/editicon.gif" border="0" alt="' . _MD_EDITTHISLINK . '"></a>';
        } else {
            $adminlink = '';
        }

        if (1 == $votes) {
            $votestring = _MD_ONEVOTE;
        } else {
            $votestring = sprintf(_MD_NUMVOTES, $votes);
        }

        $path = $mytree->getPathFromId($cid, 'title');

        $path = mb_substr($path, 1);

        $path = str_replace('/', " <img src='images/arrow.gif' board='0' alt=''> ", $path);

        $new = newlinkgraphic($time, $status);

        $pop = popgraphic($hits);

        $xoopsTpl->append(
            'links',
            [
                'id' => $lid,
                'cid' => $cid,
                'url' => $url,
                'rating' => number_format($rating, 2),
                'title' => htmlspecialchars($ltitle, ENT_QUOTES | ENT_HTML5) . $new . $pop,
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
                'comments' => $comments,
                'votes' => $votestring,
                'mail_subject' => rawurlencode(sprintf(_MD_INTRESTLINK, $xoopsConfig['sitename'])),
                'mail_body' => rawurlencode(sprintf(_MD_INTLINKFOUND, $xoopsConfig['sitename']) . ':  ' . XOOPS_URL . '/modules/bizdir/singlelink.php?cid=' . $cid . '&lid=' . $lid),
            ]
        );
    }

    $orderby = convertorderbyout($orderby);

    //Calculates how many pages exist.  Which page one should be on, etc...

    $linkpages = ceil($numrows / $show);

    //Page Numbering

    if (1 != $linkpages && 0 != $linkpages) {
        $cid = (int)$_GET['cid'];

        $prev = $min - $show;

        if ($prev >= 0) {
            $page_nav .= "<a href='viewcat.php?cid=$cid&min=$prev&orderby=$orderby&show=$show'><b><u>«</u></b></a> ";
        }

        $counter = 1;

        $currentpage = ($max / $show);

        while ($counter <= $linkpages) {
            $mintemp = ($show * $counter) - $show;

            if ($counter == $currentpage) {
                $page_nav .= "<b>($counter)</b> ";
            } else {
                $page_nav .= "<a href='viewcat.php?cid=$cid&min=$mintemp&orderby=$orderby&show=$show'>$counter</a> ";
            }

            $counter++;
        }

        if ($numrows > $max) {
            $page_nav .= "<a href='viewcat.php?cid=$cid&min=$max&orderby=$orderby&show=$show'>";

            $page_nav .= '<b><u>»</u></b></a>';
        }
    }
}
$xoopsTpl->assign('page_nav', $page_nav);
require XOOPS_ROOT_PATH . '/footer.php';
