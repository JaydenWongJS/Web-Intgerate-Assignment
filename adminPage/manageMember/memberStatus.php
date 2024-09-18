<?php
$title = "Members";
require('../../_base.php');
include('../_headerAdmin.php');
include('../_sideBar.php');

auth("admin");
/*----------------------------------*/
if (is_post()) {
    // TODO
    $id = req('member_id');
    $status = req('status');
    $stm = $_db->prepare('Update member SET status=? WHERE member_id = ?');
    $stm->execute([$status, $id]);

    temp('info', 'User has been ' . $status .' !');
}

// TODO
redirect('memberPage.php');
