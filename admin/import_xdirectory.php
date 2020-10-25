<?php

/**
 * $Id: import_xdirectory.php,v 1.1 2004/10/02 01:33:48 scurtescu Exp $
 * Module: BizDir
 * Author: Marius Scurtescu <mariuss@romanians.bc.ca>
 * Licence: GNU
 *
 * Import script from xDirectory to BizDir.
 *
 * It was tested with xDirectory version 1.5 and BizDir version 0.9
 */
require dirname(__DIR__, 3) . '/include/cp_header.php';
if (file_exists('../language/' . $xoopsConfig['language'] . '/main.php')) {
    include '../language/' . $xoopsConfig['language'] . '/main.php';
} else {
    include '../language/english/main.php';
}
require dirname(__DIR__) . '/include/functions.php';

$importFromModuleName = 'BizDir';
$scriptname = 'import_xdirectory.php';

$op = 'start';

if (isset($_POST['op']) && ('go' == $_POST['op'])) {
    $op = $_POST['op'];
}

if ('start' == $op) {
    xoops_cp_header();

    require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

    $result = $xoopsDB->query('select count(*) from ' . $xoopsDB->prefix('xdir_cat'));

    [$totalCat] = $xoopsDB->fetchRow($result);

    if (0 == $totalCat) {
        echo '<p>There are no categories to import from!</p>';
    } else {
        require_once XOOPS_ROOT_PATH . '/class/xoopstree.php';

        $result = $xoopsDB->query('select count(*) from ' . $xoopsDB->prefix('xdir_links'));

        [$totalLinks] = $xoopsDB->fetchRow($result);

        if (0 == $totalLinks) {
            echo '<p>There are no listings to import!</p>';
        } else {
            echo "There are $totalCat categories and $totalLinks to import.";

            $form = new XoopsThemeForm('Import Settings', 'import_form', XOOPS_URL . '/modules/bizdir/admin/' . $scriptname);

            $form->addElement(new XoopsFormHidden('op', 'go'));

            $form->addElement(new XoopsFormButton('', 'import', 'Import', 'submit'));

            $form->display();
        }

        exit();
    }
}

if ('go' == $op) {
    xoops_cp_header();

    echo 'Importing...';

    $cnt_imported_cat = 0;

    $cnt_imported_links = 0;

    $resultCat = $xoopsDB->query('select * from ' . $xoopsDB->prefix('xdir_cat') . ' order by title');

    while (false !== ($arrCat = $xoopsDB->fetchArray($resultCat))) {
        extract($arrCat, EXTR_PREFIX_ALL, 'xcat');

        // insert category into BizDir

        if ($xoopsDB->query('insert into ' . $xoopsDB->prefix('bizdir_cat') . " (pid, title, imageurl) values (0, $xcat_title, $xcat_imageurl)")) {
            $cid = $xoopsDB->getInsertId();

            $cnt_imported_cat++;

            echo "Imported category $xcat_title<br\>";
        } else {
            echo "Failed creating category: $xcat_title<br>";

            continue;
        }

        $resultLinks = $xoopsDB->query('select * from ' . $xoopsDB->prefix('xdir_links') . " where cid=$xcat_cid order by title");

        while (false !== ($arrLinks = $xoopsDB->fetchArray($resultLinks))) {
            extract($arrFAQ, EXTR_PREFIX_ALL, 'xlinks');

            // insert listing into BizDir

            if ($xoopsDB->query(
                'insert into '
                . $xoopsDB->prefix('bizdir_listing')
                . " (cid, title, address, address2, city, state, zip, country, phone, fax, email, url, logourl, submitter, status, date, hits, rating, votes, comments) values ($cid, $xlinks_title, $xlinks_address, $xlinks_address2, $xlinks_city, $xlinks_state, $xlinks_zip, $xlinks_country, $xlinks_phone, $xlinks_fax, $xlinks_email, $xlinks_url, $xlinks_logourl, $xlinks_submitter, $xlinks_status, $xlinks_date, $xlinks_hits, $xlinks_rating, $xlinks_votes, $xlinks_comments)"
            )) {
                $cnt_imported_links++;
            } else {
                continue;
            }
        }

        echo '<br>';
    }

    echo 'Done.<br>';

    echo "Imported $cnt_imported_cat categories and $cnt_imported_links listings<br>";

    exit();
}



