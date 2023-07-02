<?php
    require_once('pagetitles.php');
    $page_title = RB_VIEW_RECIPE_PAGE;
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
        <link rel="stylesheet" href="css/viewrecipe.css">
        <title><?= $page_title?></title>
    </head>
    <body>
        <header>
            <?php require_once('navbar.php'); ?>
        </header>
        <main>
            <?php
                require_once('dbconnection.php');
                require_once('queryutils.php');
                $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
                $id_to_get;

                if (!isset($_GET['id'])) {
                    header('Location: index.php');
                }
                else {
                    $id_to_get = filter_var($_GET['id'], FILTER_SANITIZE_STRING);
                    // Get recipe information
                    $recipe_query = "SELECT RecipeName, Protein, Tags
                                    FROM Recipe
                                    WHERE RecipeID = ?";
                    $recipe_result = parameterizedQuery($dbc, $recipe_query, 'i', $id_to_get) or trigger_error('Error querying database ' . DB_NAME, E_USER_ERROR);
                    $ingredient_query = "SELECT IngredientName, IngredientQty, Measurement
                                        FROM Recipe_Ingredient
                                        INNER JOIN Ingredient USING (IngredientID)
                                        INNER JOIN Measurement USING (MeasurementID)
                                        WHERE RecipeID = ?";
                    $ingredient_result = parameterizedQuery($dbc, $ingredient_query, 'i', $id_to_get) or trigger_error('Error querying database ' . DB_NAME, E_USER_ERROR);
                    
                    $row = mysqli_fetch_assoc($recipe_result);
                    $recipe_name = $row['RecipeName'];
                    $recipe_tags = $row['Protein'] . ", " . $row['Tags'];
                }


            ?>
            <h1 class="center"><?= $page_title . ": " . $recipe_name ?></h1>
            <p class="center subtitle"><?= $recipe_tags ?></p>
            <section>
                <h2>Ingredients</h2>
                <table id="ingredient-table">
                    <thead>
                        <tr>
                            <th class="subtitle">Ingredient Name</th>
                            <th class="subtitle">Ingredient Quantity</th>
                            <th class="subtitle">Unit of Measurement</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            while($row = mysqli_fetch_assoc($ingredient_result)) {
                        ?>
                        <tr>
                            <td class="text"><?= $row['IngredientName'] ?></td>
                            <td class="text"><?= $row['IngredientQty'] ?></td>
                            <td class="text"><?= $row['Measurement'] ?></td>
                        </tr>
                        <?php
                            }
                        ?>
                    </tbody>
                </table>
            </section>
            <section>
                <?php
                    if(isset($_POST['delete_recipe'])) {
                        if($_SESSION['admin-priv'] > 0) {
                            $id_to_delete = $_POST['delete_id'];
                            $query = "DELETE FROM Recipe_Ingredient WHERE RecipeID = ?";
                            parameterizedQuery($dbc, $query, 'i', $id_to_delete);
                            $query = "DELETE FROM Recipe WHERE RecipeID = ?";
                            parameterizedQuery($dbc, $query, 'i', $id_to_delete);
                            echo '<script>alert("Recipe Deleted")</script>';
                            header('Location: index.php');
                        }
                        else {
                            echo '<script>alert("You do not have permission to delete recipes.")</script>';
                        }
                    }
                    
                ?>
                <form action="<?= $_SERVER['PHP_SELF'] ?>?id=<?= $id_to_get ?>" method="POST">
                    <input id="delete_id" name="delete_id" type="hidden" value="<?= $id_to_get ?>">
                    <button id="delete_recipe" type="submit" name="delete_recipe" class="center subtitle primary-btn">Delete Recipe</button>
                </form>
                <?php
                    if(isset($_POST['add_groceries'])) {
                        if($_SESSION['user_id']) {
                            // Get ingredients in recipe
                            $query = "SELECT IngredientID, IngredientQty, MeasurementID FROM Recipe_Ingredient WHERE RecipeID = ?";
                            $item_results = parameterizedQuery($dbc, $query, 'i', $_POST['list_id']);

                            while($row = mysqli_fetch_assoc($item_results)) {
                                $new_qty = $row['IngredientQty'];

                                // Get user's grocery list id
                                $query = "SELECT ListID FROM Grocery WHERE UserID = ?";
                                $result = parameterizedQuery($dbc, $query, 'i', $_SESSION['user_id']);
                                $result_row = mysqli_fetch_assoc($result);

                                // If user already has a list
                                if(isset($result_row)) {
                                    $list_id = $result_row['ListID'];
                                    // Check if item is on list already
                                    $query = "SELECT IngredientQty
                                            FROM Grocery
                                            WHERE ListID = ?
                                                AND IngredientID = ?
                                                AND MeasurementID = ?";
                                    $list_check = parameterizedQuery($dbc, $query, 'iii', $list_id, $row['IngredientID'], $row['MeasurementID']);
                                    if(mysqli_num_rows($list_check) != 0) {
                                        $qty_result = mysqli_fetch_assoc($list_check);
                                        $old_qty = $qty_result['IngredientQty'];
                                        $new_qty = $old_qty + $row['IngredientQty'];
                                        $update_query = "UPDATE Grocery
                                                        SET IngredientQty = ?
                                                        WHERE UserID = ?
                                                            AND ListID = ?
                                                            AND IngredientID = ?
                                                            AND MeasurementID = ?";
                                        parameterizedQuery($dbc, $update_query, 'siiii', $new_qty, $_SESSION['user_id'], $list_id, $row['IngredientID'], $row['MeasurementID']);
                                    }
                                    else {
                                        // Insert new row
                                        $insert_query = "INSERT INTO Grocery (ListID, UserID, IngredientID, IngredientQty, MeasurementID) VALUES (?, ?, ?, ?, ?)";
                                        parameterizedQuery($dbc, $insert_query, 'iiisi', $list_id, $_SESSION['user_id'], $row['IngredientID'], $new_qty, $row['MeasurementID']);
                                    }
                                }
                                else {
                                    // Create a new list for the user
                                    $insert_query = "INSERT INTO Grocery (UserID, IngredientID, IngredientQty, MeasurementID)
                                    VALUES (?, ?, ?, ?)";
                                    parameterizedQuery($dbc, $insert_query, 'iisi', $_SESSION['user_id'], $row['IngredientID'], $new_qty, $row['MeasurementID']);
                                }
                            }
                            // Go to grocery list page
                            header('Location: grocerylist.php');
                        }
                        else {
                            header('Location: signup.php');
                        }
                    }
                ?>
                <form action="<?= $_SERVER['PHP_SELF'] ?>?id=<?= $id_to_get ?>" method="POST">
                    <input id="list_id" name="list_id" type="hidden" value="<?= $id_to_get ?>">
                    <button type="submit" name="add_groceries" id="add_groceries" class="center subtitle primary-btn">Add Ingredients To Grocery List</button>
                </form>
            </section>

        </main>
        <footer class="center">
            <p class="caption">Designed and developed by Kaelyn Lang, 2023</p>
        </footer>
    </body>
</html>
