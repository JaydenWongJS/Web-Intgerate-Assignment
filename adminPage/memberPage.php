<?php

$title = "Members";
require('../_base.php');
include('_headerAdmin.php');
include('_sideBar.php');

// Retrieve search and filter input
$name = req('name', '');
$status = req('status', '');

if (isset($_GET['name'])) {
    $name = $_GET['name'];
}

// Define SQL query with combined first name and last name
$query = 'SELECT * FROM member WHERE role = ?';
$params = ['member']; // Ensure only members are selected

if ($name !== '') {
    $query .= ' AND CONCAT(firstname, " ", lastname) LIKE ?';
    $params[] = "%$name%";
}

if ($status !== '') {
    $query .= ' AND status = ?';
    $params[] = $status;
}

// Sorting
$fields = [
    'member_id'       => 'Member Id',
    'firstname'       => 'First Name',
    'lastname'        => 'Last Name',
    'gender'          => 'Gender',
    'member_points'   => 'Point',
    'city'            => 'City',
    'status'          => 'Status',
];

$sort = req('sort');
key_exists($sort, $fields) || $sort = 'member_id';

$dir = req('dir');
in_array($dir, ['asc', 'desc']) || $dir = 'asc';

// Append sorting to the query
$query .= " ORDER BY $sort $dir";

// Paging
$page = req('page', 1);

require_once '../lib/SimplePager.php';
$p = new SimplePager($query, $params, 10, $page);
$arr = $p->result;

?>

<div class="dashboard_container">
    <div class="title">
        <h2>Manage Members <i style="font-size: 40px;" class="fas fa-user-edit"></i> </h2>
    </div>

    <!--SEARCH & FILTER-->
    <div class="member_page_search_filter">
        <form>
            <?= html_text('name', 'placeholder="Search by name"', $name, 'input-text') ?>
            <?= html_select_ron('status', $_member_status, 'All Status', '', 'select-box') ?>
            <button type="submit" ><i class="fas fa-search"></i></button>
        </form>
    </div>

    <!--RESULT-->
    <div class="member_page_result_showing">
        <p>
            <?= $p->count ?> of <?= $p->item_count ?> record(s) |
            Page <b><?= $p->page ?></b> of <?= $p->page_count ?>
        </p>
    </div>

    <!--MEMBER LIST TABLE-->
    <div class="member_page_content_table">
        <table>
            <tr class="member_page_table_row">
                <?= table_headers($fields, $sort, $dir, "page=$page") ?>
                <th>Action</th>
            </tr>
            <?php foreach ($arr as $s): ?>
                <tr>
                    <td><?= $s->member_id ?></td>
                    <td><?= $s->firstname ?></td>
                    <td><?= $s->lastname ?></td>
                    <td><?= $s->gender ?></td>
                    <td><cite><?= $s->member_points ?></cite></td>
                    <td><?= $s->city ?></td>
                    <td>
                        <!--Color for different status-->
                        <?php
                        $status_class = '';
                        if ($s->status === 'inactive') {
                            $status_class = 'member_status_pending';
                        } elseif ($s->status === 'suspend') {
                            $status_class = 'member_status_suspended';
                        } elseif ($s->status === 'active') {
                            $status_class = 'member_status_active';
                        }
                        ?>
                        <span class="<?= $status_class ?>"><?= $s->status ?></span>
                    </td>
                    <td class="member_page_button">
                        <!-- View Detail -->
                        <button class="detail_button" onclick="location.href='memberDetail.php?member_id=<?= $s->member_id ?>'">Detail</button>
                    </td>
                </tr>
            <?php endforeach ?>
        </table>
    </div>

    <?= $p->html("sort=$sort&dir=$dir&name=$name&status=$status") ?>

</div>

<?php include('_footerAdmin.php') ?>
