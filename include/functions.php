<?php

// $Id: functions.php,v 1.1 2006/03/26 21:16:32 mikhail Exp $
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

function newlinkgraphic($time, $status)
{
    $count = 7;

    $new = '';

    $startdate = (time() - (86400 * $count));

    if ($startdate < $time) {
        if (1 == $status) {
            $new = '&nbsp;<img src="images/newred.gif" alt="' . _MD_NEWTHISWEEK . '">';
        } elseif (2 == $status) {
            $new = '&nbsp;<img src="images/update.gif" alt="' . _MD_UPTHISWEEK . '">';
        }
    }

    return $new;
}

function popgraphic($hits)
{
    global $xoopsModuleConfig;

    if ($hits >= $xoopsModuleConfig['popular']) {
        return '&nbsp;<img src="images/pop.gif" alt="' . _MD_POPULAR . '">';
    }

    return '';
}

//Reusable Link Sorting Functions
function convertorderbyin($orderby)
{
    switch (trim($orderby)) {
        case 'titleA':
            $orderby = 'title ASC';
            break;
        case 'dateA':
            $orderby = 'date ASC';
            break;
        case 'hitsA':
            $orderby = 'hits ASC';
            break;
        case 'ratingA':
            $orderby = 'rating ASC';
            break;
        case 'titleD':
            $orderby = 'title DESC';
            break;
        case 'hitsD':
            $orderby = 'hits DESC';
            break;
        case 'ratingD':
            $orderby = 'rating DESC';
            break;
        case'dateD':
        default:
            $orderby = 'date DESC';
            break;
    }

    return $orderby;
}

function convertorderbytrans($orderby)
{
    if ('hits ASC' == $orderby) {
        $orderbyTrans = '' . _MD_POPULARITYLTOM . '';
    }

    if ('hits DESC' == $orderby) {
        $orderbyTrans = '' . _MD_POPULARITYMTOL . '';
    }

    if ('title ASC' == $orderby) {
        $orderbyTrans = '' . _MD_TITLEATOZ . '';
    }

    if ('title DESC' == $orderby) {
        $orderbyTrans = '' . _MD_TITLEZTOA . '';
    }

    if ('date ASC' == $orderby) {
        $orderbyTrans = '' . _MD_DATEOLD . '';
    }

    if ('date DESC' == $orderby) {
        $orderbyTrans = '' . _MD_DATENEW . '';
    }

    if ('rating ASC' == $orderby) {
        $orderbyTrans = '' . _MD_RATINGLTOH . '';
    }

    if ('rating DESC' == $orderby) {
        $orderbyTrans = '' . _MD_RATINGHTOL . '';
    }

    return $orderbyTrans;
}

function convertorderbyout($orderby)
{
    if ('title ASC' == $orderby) {
        $orderby = 'titleA';
    }

    if ('date ASC' == $orderby) {
        $orderby = 'dateA';
    }

    if ('hits ASC' == $orderby) {
        $orderby = 'hitsA';
    }

    if ('rating ASC' == $orderby) {
        $orderby = 'ratingA';
    }

    if ('title DESC' == $orderby) {
        $orderby = 'titleD';
    }

    if ('date DESC' == $orderby) {
        $orderby = 'dateD';
    }

    if ('hits DESC' == $orderby) {
        $orderby = 'hitsD';
    }

    if ('rating DESC' == $orderby) {
        $orderby = 'ratingD';
    }

    return $orderby;
}

//updates rating data in itemtable for a given item
function updaterating($sel_id)
{
    global $xoopsDB;

    $query = 'select rating FROM ' . $xoopsDB->prefix('bizdir_votedata') . ' WHERE lid = ' . $sel_id . '';

    //echo $query;

    $voteresult = $xoopsDB->query($query);

    $votesDB = $xoopsDB->getRowsNum($voteresult);

    $totalrating = 0;

    while (list($rating) = $xoopsDB->fetchRow($voteresult)) {
        $totalrating += $rating;
    }

    $finalrating = $totalrating / $votesDB;

    $finalrating = number_format($finalrating, 4);

    $query = 'UPDATE ' . $xoopsDB->prefix('bizdir_links') . " SET rating=$finalrating, votes=$votesDB WHERE lid = $sel_id";

    //echo $query;

    $xoopsDB->query($query) || exit();
}

//returns the total number of items in items table that are accociated with a given table $table id
function getTotalItems($sel_id, $status = '')
{
    global $xoopsDB, $mytree;

    $count = 0;

    $arr = [];

    $query = 'select count(*) from ' . $xoopsDB->prefix('bizdir_links') . ' where cid=' . $sel_id . '';

    if ('' != $status) {
        $query .= " and status>=$status";
    }

    $result = $xoopsDB->query($query);

    [$thing] = $xoopsDB->fetchRow($result);

    $count = $thing;

    $arr = $mytree->getAllChildId($sel_id);

    $size = count($arr);

    for ($i = 0; $i < $size; $i++) {
        $query2 = 'select count(*) from ' . $xoopsDB->prefix('bizdir_links') . ' where cid=' . $arr[$i] . '';

        if ('' != $status) {
            $query2 .= " and status>=$status";
        }

        $result2 = $xoopsDB->query($query2);

        [$thing] = $xoopsDB->fetchRow($result2);

        $count += $thing;
    }

    return $count;
}
