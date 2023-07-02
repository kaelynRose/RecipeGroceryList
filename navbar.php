<?php
    $page_title = isset($page_title) ? $page_title : "";

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
?>
<nav>
    <a class="title <?= $page_title == RB_HOME_PAGE ? 'active' : '' ?>" href=<?= dirname($_SERVER['PHP_SELF']) ?>>Recipe Book</a>
    <a class="subtitle <?= $page_title == RB_ADD_PAGE ? 'active' : '' ?>" href="addrecipe.php">Add Recipe</a>
    <a class="subtitle <?= $page_title == RB_ADD_INGREDIENT_PAGE ? 'active' : '' ?>" href="addingredient.php">Add Ingredient</a>
    <?php
        if(isset($_SESSION['user_id'])) {
    ?>
            <a class="subtitle <?= $page_title == RB_GROCERY_LIST_PAGE ? 'active' : '' ?>" href="grocerylist.php">View Grocery List</a>
            <a class="subtitle" href="logout.php">Logout (<?= $_SESSION['user_name'] ?>)</a>
    <?php
        }
        else {
    ?>
            <a class="subtitle <?= $page_title == RB_LOGIN_PAGE ? 'active' : '' ?>" href="login.php">Login</a>
            <a class="subtitle <?= $page_title == RB_SIGNUP_PAGE ? 'active' : '' ?>" href="signup.php">Sign Up</a>
    <?php
        }
    ?>
</nav>