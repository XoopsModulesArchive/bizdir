<?php

//
// ------------------------------------------------------------------------- //
//               E-Xoops: Content Management for the Masses                  //
//                       < http://www.e-xoops.com >                          //
// ------------------------------------------------------------------------- //
// Original Author: Pascal Le Boustouller
// Author Website : pascal.e-xoops@perso-search.com
// Licence Type   : GPL
// ------------------------------------------------------------------------- //

if ($_POST['submit']) {
    include 'header.php';

    global $xoopsConfig, $xoopsDB, $myts, $meta;

    $result = $xoopsDB->query('SELECT title, email FROM ' . $xoopsDB->prefix('bizdir_links') . " WHERE lid = '" . $_POST['id'] . "'");

    while (list($title, $email) = $xoopsDB->fetchRow($result)) {
        if ($_POST['tele']) {
            $teles = 'Phone: ' . $_POST['tele'];
        } else {
            $teles = '';
        }

        $message .= 'Message from ' . $_POST['namep'] . "\nEmail: " . $_POST['post'] . ' ' . $meta['title'] . "\n$teles\n\n";

        $message .= $_POST['namep'] . " wrote:\n";

        $message .= $_POST['messtext'] . "\n\n\n";

        $message .= "This message was sent using the Email form on {X_SITENAME}.  \n\n\n";

        $subject = 'Email Submission from {X_SITENAME}';

        $mail = getMailer();

        $mail->useMail();

        $mail->setFromEmail($_POST['post']);

        $mail->setToEmails($email);

        $mail->setSubject($subject);

        $mail->setBody($message);

        $mail->send();

        echo $mail->getErrors();
    }

    redirect_header('index.php', 1, _CLA_MESSEND);

    exit();
}
    include 'header.php';
    require XOOPS_ROOT_PATH . '/header.php';
    OpenTable();

    $result = $xoopsDB->query('SELECT title, email FROM ' . $xoopsDB->prefix('bizdir_links') . " WHERE lid = '" . $_GET['lid'] . "'");
    while (list($title, $email) = $xoopsDB->fetchRow($result)) {
        echo '<script>
          function verify() {
                var msg = "Errors were found during the validation of this form!\\n__________________________________________________\\n\\n";
                var errors = "FALSE";

			
				if (document.Cont.namep.value == "") {
                        errors = "TRUE";
                        msg += "The Name field is a required field.\\n";
                }
				
				if (document.Cont.post.value == "") {
                        errors = "TRUE";
                        msg += "The e-Mail field is a required field.\\n";
                }
				
				if (document.Cont.messtext.value == "") {
                        errors = "TRUE";
                        msg += "The Message field is a required field.\\n";
                }
				
  
                if (errors == "TRUE") {
                        msg += "__________________________________________________\\n\\nPlease correct the errors listed above before submitting this form.\\n";
                        alert(msg);
                        return false;
                }
          }
          </script>';

        echo '<B></B><br><br>';

        echo "Send a message to:<br><font size=4>$title</font><br>";

        echo '<form onSubmit="return verify();" method="post" action="contact.php" NAME="Cont">';

        echo '<INPUT TYPE="hidden" NAME="id" VALUE="' . $_GET['lid'] . '">';

        echo '<INPUT TYPE="hidden" NAME="submit" VALUE="1">';

        if ($xoopsUser) {
            $idd = $xoopsUser->getVar('name', 'E');

            $idde = $xoopsUser->getVar('email', 'E');
        }

        echo "<TABLE WIDTH=100% BORDER=0 CELLSPACING=1>
    <TR>
      <TD>Your Name: </TD>
      <TD><input type=\"text\" name=\"namep\" size=\"42\" value=\"$idd\"></TD>
    </TR>
    <TR>
      <TD>Your e-Mail: </TD>
      <TD><input type=\"text\" name=\"post\" size=\"42\" value=\"$idde\"></font></TD>
    </TR>
    <TR>
      <TD>Your Phone: </TD>
      <TD><input type=\"text\" name=\"tele\" size=\"42\"></font></TD>
    </TR>
    <TR>
      <TD>Message: </TD>
      <TD><textarea rows=\"5\" name=\"messtext\" cols=\"40\"></textarea></TD>
    </TR>
</TABLE>
      <p><INPUT TYPE=\"submit\" VALUE=\"Send\">
</form>";

        CloseTable();

        require XOOPS_ROOT_PATH . '/footer.php';
    }
