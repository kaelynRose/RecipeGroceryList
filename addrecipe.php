<?php
    require_once('pagetitles.php');
    $page_title = RB_ADD_PAGE;
?>
<!DOCTYPE html lang="en">
<html>
    <head>
        <meta charset="utf-8">
        <meta name="Description" content="View all the recipes and build a grocery list.">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@600;700&family=Raleway:wght@400;500&family=Shadows+Into+Light+Two&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="css/main.css">
        <link rel="stylesheet" href="css/recipeform.css">
        <title><?= $page_title ?></title>
    </head>
    <body>
        <header>
            <?php require_once('navbar.php'); ?>
        </header>
        <main>
            <h1 class="center"><?= $page_title ?></h1>
            <p class="subtitle center">Add a new recipe using the form below.</p>
            <?php
                require_once('recipeform.php');
                require_once('Recipe.php');
                require_once('Ingredient.php');
                require_once('dbconnection.php');
                require_once('queryutils.php');

                $new_recipe = new Recipe();
                $new_ingredient = new Ingredient();
                $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or trigger_error('Error connecting to MySQL server' . DB_NAME, E_USER_ERROR);

                $checked_tags = [];

                if (isset($_POST['recipe_submit'])) {
                    // Get all the information and sanitize it
                    $title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
                    $protein = $_POST['protein'];
                    $row_count = filter_var($_POST['row_count'], FILTER_SANITIZE_STRING);
                    $tags = "";
                    if (isset($_POST['carb'])) {
                        $tags .= filter_var($_POST['carb'], FILTER_SANITIZE_STRING) . ", ";
                    }
                    if (isset($_POST['tag_checkbox'])) {
                        $checked_tags = $_POST['tag_checkbox'];
                        $tags .= implode(", ", $checked_tags);
                    }

                    // Put recipe in database FIRST
                    $new_recipe -> setRecipeName($title);
                    $new_recipe -> setRecipeProtein($protein);
                    $new_recipe -> setRecipeTags($tags);
                    $new_recipe -> insertRecipe();

                    // Get recipe ID and start pairing with ingredients
                    $recipe_id = $new_recipe -> getRecipeID();

                    // Get ingredients
                    for ($x = 1; $x <= $row_count; $x++) {
                        $item_id = "item" . $x;
                        $ingredient_name = $_POST[$item_id . 'Name'];
                        $ingredient_qty = filter_var($_POST[$item_id . 'Qty'], FILTER_SANITIZE_STRING);
                        $ingredient_measure = $_POST[$item_id . 'Measure'];
                        $new_ingredient -> setIngredientName($ingredient_name);
                        $ingredient_id = $new_ingredient -> getIngredientID();
                        
                        $query = "SELECT MeasurementID FROM Measurement WHERE Measurement = ?";
                        $result = parameterizedQuery($dbc, $query, 's', $ingredient_measure);
                        $row = mysqli_fetch_assoc($result);
                        $uom = $row['MeasurementID'];

                        $query = "INSERT INTO Recipe_Ingredient (RecipeID, IngredientID, IngredientQty, MeasurementID) VALUES (?, ?, ?, ?)";
                        parameterizedQuery($dbc, $query, 'iisi', $recipe_id, $ingredient_id, $ingredient_qty, $uom);
                    }
                    $home_url = dirname($_SERVER['PHP_SELF']);
                    header('Location: ' . $home_url);
                }
            ?>
        </main>
        <footer class="center">
            <p class="caption">Designed and developed by Kaelyn Lang, 2023</p>
        </footer>
    </body>
</html>