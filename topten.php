<?php

// $Id: topten.php,v 1.1 2006/03/26 21:16:29 mikhail Exp $
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
$GLOBALS['xoopsOption']['template_main'] = 'bizdir_topten.html';
require XOOPS_ROOT_PATH . '/header.php';
//generates top 10 charts by rating and hits for each main category

$xoopsTpl->assign('module_name', $xoopsModule->name());

if (isset($rate)) {
    $sort = _MD_RATING;

    $sortDB = 'rating';
} else {
    $sort = _MD_HITS;

    $sortDB = 'hits';
}
$xoopsTpl->assign('lang_sortby', $sort);
$xoopsTpl->assign('lang_rank', _MD_RANK);
$xoopsTpl->assign('lang_title', _MD_TITLE);
$xoopsTpl->assign('lang_category', _MD_CATEGORY);
$xoopsTpl->assign('lang_hits', _MD_HITS);
$xoopsTpl->assign('lang_rating', _MD_RATING);
$xoopsTpl->assign('lang_vote', _MD_VOTE);
$arr = [];
$result = $xoopsDB->query('select cid, title from ' . $xoopsDB->prefix('bizdir_cat') . ' where pid=0');
$e = 0;
$rankings = [];
while (list($cid, $ctitle) = $xoopsDB->fetchRow($result)) {
    $rankings[$e]['title'] = sprintf(_MD_TOP10, htmlspecialchars($ctitle, ENT_QUOTES | ENT_HTML5));

    $query = 'select lid, cid, title, hits, rating, votes from ' . $xoopsDB->prefix('bizdir_links') . " where status>0 and (cid=$cid";

    // get all child cat ids for a given cat id

    $arr = $mytree->getAllChildId($cid);

    $size = count($arr);

    for ($i = 0; $i < $size; $i++) {
        $query .= ' or cid=' . $arr[$i] . '';
    }

    $query .= ') order by ' . $sortDB . ' DESC';

    $result2 = $xoopsDB->query($query, 10, 0);

    $rank = 1;

    while (list($lid, $lcid, $ltitle, $hits, $rating, $votes) = $xoopsDB->fetchRow($result2)) {
        $catpath = $mytree->getPathFromId($lcid, 'title');

        $catpath = mb_substr($catpath, 1);

        $catpath = str_replace('/', " <span class='fg2'>&raquo;</span> ", $catpath);

        $rankings[$e]['links'][] = ['id' => $lid, 'cid' => $cid, 'rank' => $rank, 'title' => htmlspecialchars($ltitle, ENT_QUOTES | ENT_HTML5), 'category' => $catpath, 'hits' => $hits, 'rating' => number_format($rating, 2), 'votes' => $votes];

        $rank++;
    }

    $e++;
}
$xoopsTpl->assign('rankings', $rankings);
require XOOPS_ROOT_PATH . '/footer.php';
