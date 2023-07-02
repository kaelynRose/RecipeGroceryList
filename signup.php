<?php
    require_once('pagetitles.php');
    $page_title = RB_SIGNUP_PAGE;
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
        <title><?= $page_title?></title>
    </head>
    <body>
        <header>
            <?php require_once('navbar.php'); ?>
        </header>
        <main>
            <h1 class="center">Sign Up For Recipe Book!</h1>
            <?php
                $show_signup_form = true;

                // If signup is submitted
                if (isset($_POST['signup_submission'])) {
                    // Get user information
                    $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
                    $password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);

                    // If user information is not empty
                    if (!empty($username) && !empty($password)) {
                        require_once('dbconnection.php');
                        require_once('queryutils.php');

                        // Connect to database
                        $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or trigger_error('Error connecting to MySQL server for ' . DB_NAME, E_USER_ERROR);

                        // Check if user already exists
                        $query = "SELECT * FROM User WHERE userName = ?";

                        $result = parameterizedQuery($dbc, $query, 's', $username) or trigger_error(mysqli_error($dbc), E_USER_ERROR);

                        // If user does not exist: create account for them
                        if (mysqli_num_rows($result) == 0){
                            $salted_hashed_password = password_hash($password, PASSWORD_DEFAULT);

                            $query = "INSERT INTO User (`username`, `salted_password`)
                                    VALUES (?, ?)";
                            parameterizedQuery($dbc, $query, 'ss', $username, $salted_hashed_password) or trigger_error(mysqli_error($dbc), E_USER_ERROR);

                            // Direct the user to the login page
                            echo "<p class='center'>Thank you for signing up <strong>$username</strong>! Your new account has been successfully created.<br>You're now ready to <a href='login.php'>log in</a>.</p>";

                            $show_signup_form = false;
                        }
                        else {
                            // An account already exists
                            echo "<p class='danger-text'>The username ($username) is already in use. Please choose another.</p>";
                        }
                    }
                    else {
                        // Error message
                        echo "<p class='danger-text'>There was an error creating your account. Make sure you have filled out all form fields and try again.</p>";
                    }
                }

                if ($show_signup_form) :
            ?>
            <form class="small-custom-form" action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
                <div id="signup-username" class="form-group">
                    <label class="subtitle" for="username">Username: </label>
                    <input type="text" name="username" id="username" required>
                    <p class="caption">Required</p>
                </div>
                <div id="signup-password" class="form-group">
                    <label class="subtitle" for="password">Password: </label>
                    <input type="password" name="password" id="password" required>
                    <p class="caption">Required</p>
                </div>
                <button class="subtitle primary-btn center" id="signup_submission" class="primary-btn" type="submit" name="signup_submission">Sign Me Up!</button>
            </form>
            <?php endif; ?>
        </main>
        <footer class="center">
            <p class="caption">Designed and developed by Kaelyn Lang, 2023</p>
        </footer>
    </body>
</html>