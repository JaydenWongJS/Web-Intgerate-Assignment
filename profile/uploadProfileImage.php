<?php

require '../_base.php';
auth("member");
$memberId=$_user->member_id;
// Check if a file was uploaded
if (is_post()) {
    $f = get_file('photo');
    $uploadError = null;

    if ($f == null) {
        $uploadError = "Error: No file uploaded or there was an upload error.";
    } else if (!str_starts_with($f->type, "image/")) {
        $uploadError = "Must be an image only!";
    } else if ($f->size > 1 * 1024 * 1024) {
        $uploadError = "Max 1MB only!";
    }

    if ($uploadError) {
        temp("updateProfileInfo", "<b class='fail'>$uploadError</b>");
        redirect("profile.php");
    } else {
        // Delete the old profile photo if it exists
        $stm = $_db->prepare('SELECT image FROM member WHERE member_id = ?');
        $stm->execute([$memberId]);
        $oldPhoto = $stm->fetchColumn();
        if ($oldPhoto) {
            unlink("../uploadsImage/userProfile/$oldPhoto");
        }

        // Save the new profile photo
        $photo = save_photo($f, '../uploadsImage/userProfile');

        // Update the database with the new photo
        $imageQuery = "UPDATE member SET image = ? WHERE member_id = ?";
        $stmt = $_db->prepare($imageQuery);
        $stmt->execute([$photo, $memberId]);

        if ($stmt->rowCount() > 0) {
            $_user->image = $photo;
            temp("updateProfileInfo", "<b class='successInfo'>Profile photo updated successfully</b>");
            
        } else {
            temp("updateProfileInfo", "<b class='fail'>Failed to update profile photo</b>");
        }

        redirect("profile.php");
    }
}
?>
