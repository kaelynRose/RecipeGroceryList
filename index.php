<?php
    require_once('pagetitles.php');
    $page_title = RB_HOME_PAGE;
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
        <link rel="stylesheet" href="css/index.css">
        <title><?= $page_title ?></title>
    </head>
    <body>
        <header>
            <?php require_once('navbar.php'); ?>
        </header>
        <main>
            <h1 class="center"><?= $page_title ?></h1>
            <p class="subtitle center">Check out all these recipes!</p>

            <?php
                $protein_filter = 'default_protein';
                $carb_filter = 'default_carb';
                $checked_tags = [];
                $other_tags = '';
                require_once('dbconnection.php');
                require_once('queryutils.php');

                $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or trigger_error('Error connecting to database ' . DB_NAME, E_USER_ERROR);

                if (isset($_POST['filter'])) {

                    // If filters are not checked
                    if (!isset($_POST['protein']) && !isset($_POST['carb']) && !isset($_POST['tag_checkbox'])) {
                        $query = "SELECT * FROM Recipe";
                        $results = mysqli_query($dbc, $query) or trigger_error('Error querying database ' . DB_NAME, E_USER_ERROR);
                    }
                    else {
                        // If any filters are checked
                        if (isset($_POST['protein'])) {
                            $protein_filter = filter_var($_POST['protein'], FILTER_SANITIZE_STRING);
                        }
                        else {
                            $protein_filter = "default_protein";
                        }
                        if (isset($_POST['carb'])) {
                            $carb_filter = filter_var($_POST['carb'], FILTER_SANITIZE_STRING);
                        }
                        else {
                            $carb_filter = "default_carb";
                        }
                        if (isset($_POST['tag_checkbox'])) {
                            $checked_tags = $_POST['tag_checkbox'];
                            $other_tags = implode(", ", $checked_tags);
                        }
                        else {
                            $other_tags = '';
                        }

                        $filter_query = "SELECT * FROM Recipe";
                        
                        if (isset($protein_filter) && $protein_filter != "default_protein") {
                            $filter_query .= " WHERE Protein = '$protein_filter'";
                            if (isset($carb_filter) && $carb_filter != "default_carb") {
                                $filter_query .= " AND Tags LIKE '%$carb_filter%'";
                            }
                            if (isset($other_tags) && $other_tags != '') {
                                $filter_query .= " AND Tags LIKE '%$other_tags%'";
                            }
                            $results = mysqli_query($dbc, $filter_query) or trigger_error('Error querying database ' . DB_NAME, E_USER_ERROR);
                        }
                        else if (isset($carb_filter) && $carb_filter != "default_carb") {
                            $filter_query .= " WHERE Tags LIKE '%$carb_filter%'";
                            if (isset($other_tags) && $other_tags != '') {
                                $filter_query .= " AND Tags LIKE '%$other_tags%'";
                            }
                            $results = mysqli_query($dbc, $filter_query) or trigger_error('Error querying database ' . DB_NAME, E_USER_ERROR);
                        }
                        else if (isset($other_tags) && $other_tags != '') {
                            $filter_query .= " WHERE Tags LIKE '%$other_tags%'";
                            $results = mysqli_query($dbc, $filter_query) or trigger_error('Error querying database ' . DB_NAME, E_USER_ERROR);
                        }
                        else {
                            $filter_query = "SELECT * FROM Recipe";
                            $results = mysqli_query($dbc, $filter_query) or trigger_error('Error querying database ' . DB_NAME, E_USER_ERROR);
                        }
                    }
                }
                else {
                    // Get all the recipes
                    $query = "SELECT * FROM Recipe";
                    $results = mysqli_query($dbc, $query) or trigger_error('Error querying database ' . DB_NAME, E_USER_ERROR);
                }
            ?>

            <section id="filters">
                <form class="filter-custom-form" action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
                    <p class="subtitle">Filters</p>
                    <fieldset>
                        <legend class="text">Protein Types</legend>
                        <label class="caption" for="default_protein"><input type="radio" name="protein" id="default_protein" value="default_protein" <?= $protein_filter=='default_protein' ? 'checked' : '' ?>>All</label>
                        <label class="caption" for="vegetarian"><input type="radio" name="protein" id="vegetarian" value="vegetarian" <?= $protein_filter=='vegetarian' ? 'checked' : '' ?>>Vegetarian</label>
                        <label class="caption" for="chicken"><input type="radio" name="protein" id="chicken" value="chicken" <?= $protein_filter=='chicken' ? 'checked' : '' ?>>Chicken</label>
                        <label class="caption" for="pork"><input type="radio" name="protein" id="Pork" value="Pork" <?= $protein_filter=='pork' ? 'checked' : '' ?>>Pork</label>
                        <label class="caption" for="beef"><input type="radio" name="protein" id="Beef" value="Beef" <?= $protein_filter=='beef' ? 'checked' : '' ?>>Beef</label>
                        <label class="caption" for="fish"><input type="radio" name="protein" id="Fish" value="Fish" <?= $protein_filter=='fish' ? 'checked' : '' ?>>Fish</label>
                    </fieldset>
                    <fieldset>
                        <legend class="text">Carb Types</legend>
                        <label class="caption" for="default_carb"><input type="radio" name="carb" id="default_carb" value="default_carb" <?= $carb_filter=='default_carb' ? 'checked' : '' ?>>All</label>
                        <label class="caption" for="rice"><input type="radio" name="carb" id="rice" value="rice" <?= $carb_filter=='rice' ? 'checked' : '' ?>>Rice</label>
                        <label class="caption" for="starch"><input type="radio" name="carb" id="starch" value="starch" <?= $carb_filter=='starch' ? 'checked' : '' ?>>Starchy Vegetable</label>
                        <label class="caption" for="nut"><input type="radio" name="carb" id="nut" value="nut" <?= $carb_filter=='nut' ? 'checked' : '' ?>>Nuts</label>
                        <label class="caption" for="legume"><input type="radio" name="carb" id="legume" value="legume" <?= $carb_filter=='legume' ? 'checked' : '' ?>>Legumes</label>
                        <label class="caption" for="Pasta"><input type="radio" name="carb" id="Pasta" value="pasta" <?= $carb_filter=='pasta' ? 'checked' : '' ?>>Pasta</label>
                    </fieldset>
                    <fieldset>
                        <legend class="text">Other tags</legend>
                        <label class="caption" for="spicy"><input id="spicy" name="tag_checkbox[]" value="spicy" type="checkbox" <?=in_array('spicy', $checked_tags) ? 'checked' : '' ?>>Spicy</label>
                        <label class="caption" for="lowcal"><input id="lowcal" name="tag_checkbox[]" value="lowcal" type="checkbox" <?=in_array('lowcal', $checked_tags) ? 'checked' : '' ?>>Low Calorie</label>
                        <label class="caption" for="vegan"><input id="vegan" name="tag_checkbox[]" value="vegan" type="checkbox" <?=in_array('vegan', $checked_tags) ? 'checked' : '' ?>>Vegan</label>
                        <label class="caption" for="gluten-free"><input id="gluten-free" name="tag_checkbox[]" value="gluten-free" type="checkbox" <?=in_array('gluten-free', $checked_tags) ? 'checked' : '' ?>>Gluten-free</label>
                    </fieldset>
                    <button class="primary-btn center text" name="filter" id="filter">Filter</button>
                </form>
            </section>
            <section id="recipes">
                <h2>Recipes</h2>
                <table id="recipe-table">
                    <thead>
                        <tr>
                            <th class="subtitle">Recipe Title</th>
                            <th class="subtitle">Protein</th>
                            <th class="subtitle">Tags</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            while($row = mysqli_fetch_assoc($results)) {
                        ?>

                        <tr>
                            <td class="recipe-link text"><a href="viewrecipe.php?id=<?=$row['RecipeID']?>"><?=$row['RecipeName']?></a></td>
                            <td class="text"><?=$row['Protein']?></td>
                            <td class="text"><?=$row['Tags']?></td>
                        </tr>

                        <?php
                            }
                        ?>
                    </tbody>
                </table>
            </section>
        </main>
        <footer class="center">
            <p class="caption">Designed and developed by Kaelyn Lang, 2023</p>
        </footer>
    </body>
</html>