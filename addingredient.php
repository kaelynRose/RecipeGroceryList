<?php
    require_once('pagetitles.php');
    $page_title = RB_ADD_INGREDIENT_PAGE;
?>
<!DOCTYPE html lang="en">
<html>
    <head>
        <meta charset="utf-8">
        <meta name="Description" content="Add a new ingredient for recipes.">
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
            <h1 class="center"><?= $page_title ?></h1>
            <p class="subtitle center">Add new ingredients with the form below.</p>

            <form class="small-custom-form" action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
                <div class="form-group">
                    <label class="subtitle" for="name">Ingredient Name:</label>
                    <input id="name" name="name" type="text" required>
                    <p class="caption">Ingredient name is required.</p>
                </div>
                <button class="primary-btn subtitle" type="submit" name="sumbit_ingredient">Add Ingredient</button>
            </form>
            <?php
                if (isset($_POST['sumbit_ingredient'])) {
                    require_once('Ingredient.php');
                    $new_ingredient = new Ingredient();

                    // Grab ingredient name
                    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);

                    // Verify name not empty
                    if (empty($name)) {
                        echo '<p class="danger-text">The ingredient name field must be filled out</p>';
                    }
                    else {
                        $new_ingredient -> setIngredientName($name);
                        $new_ingredient -> insertIngredient();
                        echo '<p class="text center success-text">The ingredient ' . $new_ingredient -> getIngredientName() . ' was successfully added</p>';
                    }
                }
            ?>
        </main>
        <footer class="center">
            <p class="caption">Designed and developed by Kaelyn Lang, 2023</p>
        </footer>
    </body>
</html>