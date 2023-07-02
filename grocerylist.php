<?php
    require_once('pagetitles.php');
    $page_title = RB_GROCERY_LIST_PAGE;
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
        <link rel="stylesheet" href="css/grocery.css">
        <title><?= $page_title ?></title>
    </head>
    <body>
        <header>
            <?php require_once('navbar.php'); ?>
        </header>
        <main>
            <h1 class="center"><?= $page_title ?></h1>
            <table id="grocery-table">
                <thead>
                    <tr>
                        <th class="subtitle">Item Name</th>
                        <th class="subtitle">Item Quantity</th>
                        <th class="subtitle">Unit of Measurement</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        require_once('dbconnection.php');
                        require_once('queryutils.php');
                        $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or trigger_error('Error connecting to database ' . DB_NAME, E_USER_ERROR);

                        $query = "SELECT * 
                                  FROM Grocery
                                  INNER JOIN Ingredient USING (IngredientID)
                                  INNER JOIN Measurement USING (MeasurementID)
                                  WHERE UserID = ?";
                        $results = parameterizedQuery($dbc, $query, 'i', $_SESSION['user_id']);

                        while($row = mysqli_fetch_assoc($results)) {
                            ?>
                            <tr>
                                <td class="text"><?= $row['IngredientName'] ?></td>
                                <td class="text"><?= $row['IngredientQty'] ?></td>
                                <td class="text"><?= $row['Measurement'] ?></td>
                                <td>
                                    <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
                                        <input type="hidden" id="item_id" name="item_id" value="<?= $row['IngredientID'] ?>">
                                        <input type="hidden" id="measure_id" name="measure_id" value="<?= $row['MeasurementID'] ?>">
                                        <button class="text primary-btn" type="submit" id="remove_item" name="remove_item">Remove Item</button>
                                    </form>
                                </td>
                            </tr>
                            <?php
                        }

                        if(isset($_POST['remove_item'])) {
                            $query = "SELECT ListID FROM Grocery WHERE UserID = ?";
                            $result = parameterizedQuery($dbc, $query, 'i', $_SESSION['user_id']);
                            $row = mysqli_fetch_assoc($result);
                            $item_id = filter_var($_POST['item_id'], FILTER_SANITIZE_STRING);
                            $measure_id = filter_var($_POST['measure_id'], FILTER_SANITIZE_STRING);

                            $query = "DELETE FROM Grocery
                                    WHERE ListID = ?
                                        AND UserID = ?
                                        AND IngredientID = ?
                                        AND MeasurementID = ?";
                            
                            parameterizedQuery($dbc, $query, 'iiii', $row['ListID'], $_SESSION['user_id'], $item_id, $measure_id);
                        }
                    ?>
                </tbody>
            </table>
            <form id="clear-form" action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
                <button class="primary-btn center subtitle" type="submit" id="clear_list" name="clear_list">Clear List</button>
                <?php
                    if(isset($_POST['clear_list'])) {
                        $query = "SELECT ListID FROM Grocery WHERE UserID = ?";
                        $result = parameterizedQuery($dbc, $query, 'i', $_SESSION['user_id']);
                        $row = mysqli_fetch_assoc($result);
                        
                        $query = "DELETE FROM Grocery
                                WHERE ListID = ?
                                    AND UserID = ?";
                        parameterizedQuery($dbc, $query, 'ii', $row['ListID'], $_SESSION['user_id']);
                    }
                ?>
            </form>
        </main>
        <footer class="center">
            <p class="caption">Designed and developed by Kaelyn Lang, 2023</p>
        </footer>
    </body>
</html>