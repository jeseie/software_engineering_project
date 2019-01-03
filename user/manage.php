<?php
    // permission_text and permission_option may be the problems
    
    include("../header.php");
    // include('../util/constant.php');
    // include('../util/connect.php');
    // include('../util/general.php');

    if ($_SESSION['default_permission'] < MODERATOR)
        exit('Not enough permission.');

    function showRules($permission)
    {
        global $con; // very important, it will cause a fatal error without this line.
        global $permission_text;
        // plus admin
        $strong_permission_text = $permission_text;
        array_push($strong_permission_text, "Admin");

        if ($permission >= MODERATOR)
        {
            $query = "SELECT * FROM rule ORDER BY user_id, board_id";
            $result = $con->query($query) or die($query . '<br/>' . $con->error);
            echo <<< EOT
            <div class="col-lg-12">
                <h1>Rule Manage</h1>
            </div>
            <div class="col-lg-12">
            <table class="table">
                    <tr>
                        <th class="col-xs-2">User ID</th>
                        <th class="col-xs-2">User name</th>
                        <th class="col-xs-2">Board ID</th>
                        <th class="col-xs-2">Board name</th>
                        <th class="col-xs-2">Permission</th>
                        <th class="col-xs-2">Option</th>
                    </tr>
EOT;
            while ($row = $result->fetch_array(MYSQLI_BOTH))
            {
                $user_id = $row['user_id'];
                $username = getUserName($user_id);
                $board_id = $row['board_id'];
                $board_name = getBoardName($board_id);
                $permission = $strong_permission_text[$row['permission']];
                echo <<< EOT
                    <tr>
                        <td class="col-xs-2">$user_id</td>
                        <td class="col-xs-2">$username</td>
                        <td class="col-xs-2">$board_id</td>
                        <td class="col-xs-2">$board_name</td>
                        <td class="col-xs-2">$permission</td>
                        <td class="col-xs-2"><button class="btn btn-outline-light btn-sm" onclick="windows.location.href='del_rule.php?user_id=$user_id&board_id=$board_id'">Delete</button></td>
                    </tr>
EOT;
            }
            echo("</table></div>");
        }
    }

    function showRuleInput($permission)
    {
        global $con; // very important, it will cause a fatal error without this line.

        if ($permission >= MODERATOR)
        {
            global $permission_option;
            $strong_permission_option = $permission_option;
            $strong_permission_option .= "<option value=3>Admin</option>\n";

            $query = "SELECT * FROM user ORDER BY user_id";
            $result = $con->query($query) or die($query . '<br/>' . $con->error);
            $user_option = '';
            while ($row = $result->fetch_array(MYSQLI_BOTH))
            {
                $user_id = $row['user_id'];
                $username = getUserName($user_id);
                $user_option .= "<option value='$user_id'>$username</option>";
            }

            $query = "SELECT * FROM board ORDER BY board_id";
            $result = $con->query($query) or die($query . '<br/>' . $con->error);
            $board_option = '';
            while ($row = $result->fetch_array(MYSQLI_BOTH))
            {
                $board_id = $row['board_id'];
                $board_name = getBoardName($board_id);
                $board_option .= "<option value='$board_id'>$board_name</option>";
            }

            echo <<< EOT
            <div class="col-lg-12">
                <h2>New Rule</h2>
            </div>
            <form method="post" action="add_rule.php" onSubmit="return inputCheck()">
                <div class="col-lg-12">
                    <table class="table">
                        <tr>
                            <th class="col-xs-3">Username</th>
                            <th class="col-xs-3">Board Name</th>
                            <th class="col-xs-3">Permission</th>
                            <th class="col-xs-3">Option</td>
                        </tr>
                        <tr>
                            <td class="col-xs-3">
                                <select class="form-control" id="username" name="user_id">
                                    $user_option
                                </select>
                            </td>
                            <td class="col-xs-3">
                                <select class="form-control" id="board_name" name="board_id">
                                    $board_option
                                </select>
                            </th>
                            <td class="col-xs-3">
                                <select class="form-control" id="permission" name="permission">
                                    $strong_permission_option
                                </select>
                            </td>
                            <td class="col-xs-3"><input class="btn btn-outline-light btn-sm" type="submit" name="submit" value="Add" /></td>
                        </tr>
                    </table>
                </div>
            </form>
EOT;

                // <div class="col-lg-12">
                //     <label for="username">Username: </label>
                //     <select class="form-control" id="username" name="user_id">
                //         $user_option
                //     </select>&nbsp; &nbsp;
                // </div>
                // <div class="col-lg-12">
                //     <label for="board_name">Board Name: </label>
                //     <select class="form-control" id="board_name" name="board_id">
                //         $board_option
                //     </select>&nbsp; &nbsp;
                // </div>
                // <div class="col-lg-12">
                //     <label for="permission">Permission: </label>
                //     <select class="form-control" id="permission" name="permission">
                //         $strong_permission_option
                //     </select>&nbsp; &nbsp;
                // </div>
                // <div class="col-lg-12">
                //     <input class="btn btn-outline-light btn-sm" type="submit" name="submit" value="Add" />
                // </div>
        }
    }

    function showUsers($permission)
    {
        global $con; // very important, it will cause a fatal error without this line.
        global $permission_text;
        $strong_permission_text = $permission_text;
        array_push($strong_permission_text, "Admin");

        if ($permission >= MODERATOR)
        {
            echo <<< EOT
            <div class="col-lg-12">
                <h1>User manage</h1>
            </div>
            <div class="col-lg-12">
                <table class="table">
                    <tr>
                        <th class="col-xs-2">User ID</th>
                        <th class="col-xs-2">User name</th>
                        <th class="col-xs-3">Registration time</th>
                        <th class="col-xs-2">Default permission</th>
                        <th class="col-xs-2"><button class="btn btn-outline-light btn-sm" onClick="window.location.href=window.location.href">Restore</button></th>
                    </tr>
EOT;
            $query = "SELECT * FROM user ORDER BY user_id";
            $result = $con->query($query) or die($query . '<br/>' . $con->error);
            $i = 0;
            while ($row = $result->fetch_array(MYSQLI_BOTH))
            {
                $user_id = $row["user_id"];
                $username = getUserName($user_id);
                $registration_time = $row["registration_time"];
                $permission = $row["permission"];

                $i++;
                $option = '';
                for ($j = 1; $j < count($strong_permission_text); $j++)
                {
                    if ($j == $permission)
                        $option .= "<option value=$j selected='selected'>$strong_permission_text[$j]</option>";
                    else
                        $option .= "<option value=$j>$strong_permission_text[$j]</option>";
                }

                echo <<< EOT
                <tr>
                    <form method="post" action="./change_permission.php">
                        <input type="hidden" name="user_id" value=$user_id />
                        <td>$user_id</td>
                        <td>$username</td>
                        <td>$registration_time</td>
                        <td>
                            <select class="form-control" id="permission_$i" name="permission" autoComplete="off" onChange="document.getElementById('submit_$i').disabled=false">
                                $option
                            <select>
                        </td>
                        <td><button class="btn btn-outline-light btn-sm" id="submit_$i" name="submit" disabled=true>Commit</button></td>
                    </form>
                </tr>
EOT;
            }
            echo("</table></div>");
        }
    }
?>

<!-- <!DOCTYPE html>
<html lang="en-US">
<head>
    <title>NTUST-ptt - user manage</title>
    <link href="/bootstrap-4.1.3-dist/css/bootstrap.min.css" />
    <link href="/css/style.css" rel="stylesheet" />
    <script src="/bootstrap-4.1.3-dist/js/bootstrap.min.js"></script>
</head> -->
<!-- <body>
    <header class="masthead">
        <div class="container">
            <div class="row">
                <div class="masthead-logo col-4">
                    NTUST-ptt
                </div>
                <div class="masthead-nav col-8">
                    <a href="/ptt/home.php">Home</a>
                    <?php showUserManagement($_SESSION['default_permission']); ?>
                    <a href="/user/user_info.php"><?php showUser(); ?></a>
                    <?php showDefaultPermission();?>
                    <a href="/logout.php">Log out</a>
                </div>
            </div>
        </div>
    </header> -->

    <div class="container markdown-body">
        <div class="row">
            <?php showRules($_SESSION['default_permission']); ?>
            <?php showRuleInput($_SESSION['default_permission']); ?>
            <?php showUsers($_SESSION['default_permission']); ?>		
        </div>	
        <!-- <footer class="footer">
            We are the best!
        </footer> -->
    </div>
</body>
</html>