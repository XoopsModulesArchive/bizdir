<?php

// This code is not tested
function b_waiting_bizdir_0()
{
    $xoopsDB = XoopsDatabaseFactory::getDatabaseConnection();

    $block = [];

    // bizdir links

    $result = $xoopsDB->query('SELECT COUNT(*) FROM ' . $xoopsDB->prefix('bizdir_links') . ' WHERE status=0');

    if ($result) {
        $block['adminlink'] = XOOPS_URL . '/modules/bizdir/admin/index.php?op=listNewLinks';

        [$block['pendingnum']] = $xoopsDB->fetchRow($result);

        $block['lang_linkname'] = _MB_WAITING_BIZDIR_LINKS;
    }

    return $block;
}

function b_waiting_bizdir_1()
{
    $xoopsDB = XoopsDatabaseFactory::getDatabaseConnection();

    $block = [];

    // bizdir modreq

    $result = $xoopsDB->query('SELECT COUNT(*) FROM ' . $xoopsDB->prefix('bizdir_mod'));

    if ($result) {
        $block['adminlink'] = XOOPS_URL . '/modules/bizdir/admin/index.php?op=listModReq';

        [$block['pendingnum']] = $xoopsDB->fetchRow($result);

        $block['lang_linkname'] = _MB_WAITING_BIZDIR_MODREQ;
    }

    return $block;
}
