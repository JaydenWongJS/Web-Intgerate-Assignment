<?php

$title = "Members";
require('../_base.php');
include('_headerAdmin.php');
include('_sideBar.php');

/*----------------------------------*/
// Retrieve search and filter input
if (is_get()) {
    $id = req('member_id');

    // get id
    $stm = $_db->prepare('SELECT * FROM member WHERE member_id = ?');
    $stm->execute([$id]);
    $s = $stm->fetch();

    if (!$s) {
        redirect('/');
    }

    $status = $s->status;
    extract((array)$s);
}

if (is_post()) {
    // Input
    $id = req('member_id'); // get from URL
    $email = req('email');

    //Validate email
    if ($email == '') {
        $_err['email'] = 'Required';
    } else if (!is_email($email)) {
        $_err['email'] = 'Invalid Email Format Sir';
    } else if (!is_unique($email, "member", "email")) {
        $_err['email'] = $email . ' is NOT ALLOWED to use !';
    }



    // Output
    if (!$_err) {
        $stm = $_db->prepare('UPDATE member
                              SET  email = ?
                              WHERE member_id = ?');
        $stm->execute([$email, $id]);

        temp('info', 'Record updated');
        redirect('/adminPage/memberPage.php');
    } else {
        // If there are errors, fetch the existing member data again to avoid undefined errors
        $stm = $_db->prepare('SELECT * FROM member WHERE member_id = ?');
        $stm->execute([$id]);
        $s = $stm->fetch();
        if (!$s) {
            redirect('/');
        }
        $status = $s->status;
    }
}

$_title = 'Update Details';

?>
<div class="member-management-container">
    <div class="page-title">
        <h2>Manage Members</h2>
    </div>
    <div class="action-buttons">
        <div class="back-button">
            <button data-get="memberDetail.php?member_id=<?= $id ?>"><i class="fas fa-arrow-left"></i> Back</button>
        </div>
    </div>

    <form action="" method="post">
        <!-- Member Details Table -->
        <table class="member-details-table">
            <tr>
                <td colspan="4" class="profile-picture">
                    <img src="../uploadsImage/userProfile/<?= $s->image ?>" />
                </td>
            </tr>
            <tr>
                <th>Member ID:</th>
                <td><b><?= $s->member_id ?></b></td>
                <th>Member Name:</th>
                <td><?= $s->firstname . ' ' . $s->lastname ?></td>
            </tr>
            <tr>
                <th>Phone :</th>
                <td><?= $s->phone ?></td>
                <th>Email Address:</th>
                <td>
                    <?= html_text('email', 'maxlength="100", type="email"', isset($email) ? $email : $s->email) ?>
                    <?= err('email','') ?>
                </td>
            </tr>
            <tr>
                <th>Date of Birth:</th>
                <td><?= $s->birthdate ?></td>
                <th>Gender:</th>
                <td>
                    <?php
                    if ($s->gender == 'M') {
                        echo "Male";
                    } else {
                        echo "Female";
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <th>Member Points:</th>
                <td colspan="3"><?= $s->member_points ?></td>
            </tr>
        </table>

        <!-- Buttons for updating and resetting the form -->
        <div class="form-action-buttons">
            <button id="reset_profile" type="reset"><i class="fas fa-redo"></i> Reset</button>
            <button id="update_profile"><i class="fas fa-check"></i> Submit</button>
        </div>
    </form>
</div>

<?php include('_footerAdmin.php') ?>