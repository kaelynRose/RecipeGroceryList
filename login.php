<?php
    require_once('pagetitles.php');
    $page_title = RB_LOGIN_PAGE;
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="Description" content="View all the recipes and build a grocery list.">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@600;700&family=Raleway:wght@400;500&family=Shadows+Into+Light+Two&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="css/main.css">
        <title><?= $page_title ?></title>
    </head>
    <body>
        <header>
            <?php require_once('navbar.php'); ?>
        </header>
        <main>
            <?php 
                // Submit form
                if (empty($_SESSION['user_id']) && isset($_POST['login_submission'])) {
                    // Get login information
                    $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
                    $password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);

                    if (!empty($username) && !empty($password)) {
                        require_once('dbconnection.php');
                        require_once('queryutils.php');

                        $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or trigger_error('Error connecting to MySQL server for ' . DB_NAME, E_USER_ERROR);

                        // Check if user already exists
                        $query = "SELECT userID, userName, salted_password, admin_privileges FROM User WHERE userName = ?";
                        $results = parameterizedQuery($dbc, $query, 's', $username) or trigger_error(mysqli_error($dbc), E_USER_ERROR);

                        // IF user was found, validate password
                        if (mysqli_num_rows($results) == 1) {
                            $row = mysqli_fetch_array($results);

                            if (password_verify($password, $row['salted_password'])) {
                                $_SESSION['user_id'] = $row['userID'];
                                $_SESSION['user_name'] = $row['userName'];
                                $_SESSION['admin_priv'] = $row['admin_privileges'];

                                // Redirect to the home page
                                $home_url = dirname($_SERVER['PHP_SELF']);
                                header('Location: ' . $home_url);
                                exit;
                            }
                        }
                        else if (mysqli_num_rows($resutls) == 0) {
                            // User does not exist
                            echo "<p class='danger-text'>An account does not exist for this username: <span class='font-weight-bold'>($username)</span>. Please use a different user name.</p><hr>";
                        }
                        else {
                            echo "<p class='danger-text'>Something went terribly wrong!</p><hr>";
                        }
                    }
                    else {
                        // Output error message
                        echo "<p class='danger-text'>You must enter both a user name and password.</p><hr>";
                    }
                }

                if (empty($_SESSION['user_id'])) :
            ?>
            <h1 class="center">Recipe Book Login</h1>
            <form class="small-custom-form" action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
                <div id="login-username" class="form-group">
                    <label class="subtitle" for="username">Username: </label>
                    <input type="text" name="username" id="username" required>
                    <p class="caption">Required</p>
                </div>
                <div id="login-password" class="form-group">
                    <label class="subtitle" for="password">Password: </label>
                    <input type="password" name="password" id="password" required>
                    <p class="caption">Required</p>
                </div>
                <button id="login_submission" class="primary-btn center subtitle" type="submit" name="login_submission">Login</button>
            </form>
            <?php
                elseif (isset($_SESSION['user_name'])):
                    echo "<h4><p class='success'>You are logged in as: <strong>{$_SESSION['user_name']}</strong>.</p></h4>";
                endif;
            ?>
            </main>
            <footer class="center">
                <p class="caption">Designed and developed by Kaelyn Lang, 2023</p>
            </footer>
        </div>
    </body>
</html>