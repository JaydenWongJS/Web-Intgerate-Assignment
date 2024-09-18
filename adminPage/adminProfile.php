<?php

$title = "Admin Profile";
require('../_base.php');
include('_headerAdmin.php');
include('_sideBar.php');
?>
<?php


$success_message = '';

// Check if the form is submitted
if (is_post()) {
    $name = req('name');
    $email = req('email');
    $phone = req('phone');

    // Validation
    if ($name == '') {
        $_err['name'] = 'Name is required.';
    }
    if ($email == '') {
        $_err['email'] = 'Email is required.';
    } elseif (!is_email($email)) {
        $_err['email'] = 'Invalid email format.';
    }
    if ($phone == '') {
        $_err['phone'] = 'Phone number is required.';
    }

    // $f = get_file('image');
   
    // if ($f == null) {
    //     $_err['image'] = "Error: No file uploaded or there was an upload error.";
    // } else if (!str_starts_with($f->type, "image/")) {
    //     $_err['image']  = "Must be an image only!";
    // } else if ($f->size > 1 * 1024 * 1024) {
    //     $_err['image']  = "Max 1MB only!";
    // }

    // If no errors, proceed with updating the database
    if (!$_err) {
        // Delete the old profile photo if it exists
        // $stm = $_db->prepare('SELECT image FROM member WHERE member_id = ?');
        // $stm->execute([$memberId]);
        // $oldPhoto = $stm->fetchColumn();
        // if ($oldPhoto) {
        //     unlink("../uploadsImage/userProfile/$oldPhoto");
        // }

        // // Save the new profile photo
        // $photo = save_photo($f, '../uploadsImage/userProfile');

        $stm = $_db->prepare('UPDATE member 
                              SET firstname = ?,lastname=?, email = ?, phone = ?, 
                              WHERE member_id = ? AND role="admin"');
        $stm->execute([$firstname,$lastname, $email, $phone, $_user->member_id]);

        $success_message = "Profile successfully updated!";
    }
}

// Fetch current admin data from the database
$stm = $_db->prepare('SELECT * FROM member WHERE member_id = ?');
$stm->execute([$_user->member_id]);

$s = $stm->fetch();

if ($s) {
    extract((array)$s);
} else {
    // Handle the case where no member is found
    echo "No member found.";
}

?>
<div class="admin-profile-container">
    <h2>Admin Profile</h2>

    <!-- Success Message -->
    <?php if ($success_message): ?>
        <div class="success-message"><?= $success_message ?></div>
    <?php endif; ?>

    <form action="adminProfile.php" method="post" class="profile-form">
        <!-- Profile Picture Section -->
        <!-- <div class="profile-picture-section">
            <img src="../uploadsImage/adminProfile/<?= htmlspecialchars($s->image) ?>" alt="Profile Image" class="profile-img" />
            <input type="file" name="image" id="imageUpload" accept="image/*">
        </div> -->

        <!-- Admin Details -->
        <div class="profile-details">
            <label for="name">First Name:</label>
            <?= html_text_type("text", "firstname", "input-field") ?>

            <?= err('firstname', '') ?>

            <label for="name">Last Name:</label>
            <?= html_text_type("text", "lastname", "input-field") ?>

            <?= err('lastname', '') ?>

            <label for="email">Email Address:</label>
            <?= html_text_type("email", "email", "input-field") ?>

            <?= err('email', '') ?>

            <label for="phone">Phone Number:</label>
            <?= html_text_type("phone", "phone", "input-field") ?>

            <?= err('phone', '') ?>
        </div>

        <!-- Buttons -->
        <div class="form-buttons">
            <button type="submit" class="submit-btn"><i class="fas fa-save"></i> Save</button>
            <button type="reset" class="reset-btn"><i class="fas fa-redo"></i> Reset</button>
        </div>
    </form>
</div>

<?php include('_footerAdmin.php') ?>


<?php include '_footerAdmin.php'; ?>