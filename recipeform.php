<?php
    require_once('dbconnection.php');
    require_once('queryutils.php');
    $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    // Get all ingredients
    $query = "SELECT IngredientName FROM Ingredient ORDER BY IngredientName ASC";
    $item_result = mysqli_query($dbc, $query);

    // Get all measures
    $query = "SELECT Measurement FROM Measurement";
    $measure_result = mysqli_query($dbc, $query);

?>
<form class="custom-form" action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
    <div class="form-group">
        <label class="subtitle" for="title">Recipe Title:</label>
        <input id="title" name="title" type="text" required>
        <p class="caption">Recipe title is required.</p>
    </div>
    <div>
        <fieldset>
            <legend class="subtitle">Ingredients</legend>
            <p class="caption">You must have at least one (1) ingredient</p>
            <table class="center" id="item-container">
                <thead>
                    <tr class="text">
                        <th></th>
                        <th>Ingredient Name</th>
                        <th>Quantity</th>
                        <th>Measurement</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="table-body">
                    <tr>
                        <td class="subtitle">Item 1</td>
                        <td>
                            <select class="text" name="item1Name" id="item1Name" required>
                                <option value="">Select Ingredient</option>
                                <?php
                                    while ($row = mysqli_fetch_assoc($item_result)) {
                                        echo '<option value="' . $row['IngredientName'] . '">' . $row['IngredientName'] . '</option>';
                                    }
                                ?>
                            </select>
                        </td>
                        <td>
                            <input id="item1Qty" name="item1Qty" class="text" type="number">
                        </td>
                        <td>
                            <select id="item1Measure" name="item1Measure" class="text">
                                <option value="">Select Measurement</option>
                                <?php
                                    while ($row = mysqli_fetch_assoc($measure_result)) {
                                        echo '<option value="' . $row['Measurement'] . '">' . $row['Measurement'] . '</option>';
                                    }
                                ?>
                            </select>
                        </td>
                    </tr>
                </tbody>
            </table>
            <button type="button" id="addItem" class="primary-btn subtitle" onclick="addIngredient()">Add Ingredient</button>
            <button type="button" class="primary-btn subtitle center" onclick="removeIngredient()">Remove Ingredient</button>
        </fieldset>
    </div>
    <div class="form-group">
        <fieldset>
            <legend class="subtitle">Protein Type</legend>
            <p class="caption">A protein type is required</p>
            <table id="protein-container">
                <tbody>
                    <tr>
                        <td>
                            <label class="subtitle" for="veg">Vegetarian: <input type="radio" name="protein" id="veg" value="vegetarian" required></label>
                        </td>
                        <td>
                            <label class="subtitle" for="chicken">Chicken: <input type="radio" name="protein" id="chicken" value="chicken" required></label>
                        </td>
                        <td>
                            <label class="subtitle" for="pork">Pork: <input type="radio" name="protein" id="pork" value="pork" required></label>
                        </td>
                        <td>
                            <label class="subtitle" for="beef">Beef: <input type="radio" name="protein" id="beef" value="beef" required></label>
                        </td>
                        <td>
                            <label class="subtitle" for="fish">Fish: <input type="radio" name="protein" id="fish" value="fish" required></label>
                        </td>
                    </tr>
                </tbody>
            </table>
        </fieldset>
    </div>
    <div class="form-group">
        <fieldset>
            <legend class="subtitle">Carb Type</legend>
            <p class="caption">This tag is optional</p>
            <table id="carb-container">
                <tbody>
                    <tr>
                        <td>
                            <label class="subtitle" for="rice">Rice: <input id="rice" name="carb" value="rice" type="radio"></label>
                        </td>
                        <td>
                            <label class="subtitle" for="starch">Starchy Vegetable: <input id="starch" name="carb" value="starch" type="radio"></label>
                        </td>
                        <td>
                            <label class="subtitle" for="nut">Nuts: <input id="nut" name="carb" value="nut" type="radio"></label>
                        </td>
                        <td>
                            <label class="subtitle" for="legume">Legumes: <input id="legume" name="carb" value="legume" type="radio"></label>
                        </td>
                        <td>
                            <label class="subtitle" for="pasta">Pasta: <input id="pasta" name="carb" value="pasta" type="radio"></label>
                        </td>
                    </tr>
                </tbody>
            </table>
        </fieldset>
    </div>
    <div class="form-group">
        <fieldset>
            <legend class="subtitle">Recipe Tags</legend>
            <p class="caption">These tags are optional</p>
            <table id="tag-container">
                <tbody>
                    <tr>
                        <td>
                            <label class="subtitle" for="spicy">Spicy: <input id="spicy" name="tag_checkbox[]" value="spicy" type="checkbox"></label>
                        </td>
                        <td>
                            <label class="subtitle" for="lowcal">Low Calorie: <input id="lowcal" name="tag_checkbox[]" value="lowcal" type="checkbox"></label>
                        </td>
                        <td>
                            <label class="subtitle" for="vegan">Vegan: <input id="vegan" name="tag_checkbox[]" value="vegan" type="checkbox"></label>
                        </td>
                        <td>
                            <label class="subtitle" for="gluten-free">Gluten-free: <input id="gluten-free" name="tag_checkbox[]" value="gluten-free" type="checkbox"></label>
                        </td>
                    </tr>
                </tbody>
            </table>
        </fieldset>
    </div>
    <input id="row_count" type="hidden" name="row_count" value="1">
    <button class="subtitle center primary-btn" type="submit" name="recipe_submit" value="recipe_submit">Add Recipe</button>
</form>

<script type="text/javascript">
    var itemCount = document.getElementById("item-container").tBodies[0].rows.length;
    var ingredientList = document.getElementById("item1Name").innerHTML;
    var measureList = document.getElementById("item1Measure").innerHTML;

    function addIngredient() {
        itemCount = document.getElementById("item-container").tBodies[0].rows.length + 1;
        document.getElementById("row_count").value = itemCount;
        console.log(itemCount);
        var table = document.getElementById("item-container");
        var row = table.insertRow(-1);
        var itemID = "item" + itemCount;
        var cell1 = row.insertCell(0);
        var cell2 = row.insertCell(1);
        var cell3 = row.insertCell(2);
        var cell4 = row.insertCell(3);

        cell1.innerHTML = "Item " + itemCount;
        cell1.className = "subtitle";
        cell2.innerHTML = '<select class="text" name="' + itemID + 'Name" id="' + itemID + 'Name">' + ingredientList + '</select>';
        cell3.innerHTML = '<input id="' + itemID + 'Qty" name="' + itemID + 'Qty" class="text" type="number">';
        cell4.innerHTML = '<select class="text" name="' + itemID + 'Measure" id="' + itemID + 'Measure">' + measureList + '</select>';
    }

    function removeIngredient() {
        if (document.getElementById("item-container").tBodies[0].rows.length - 1 == 0) {
            alert("You must have at least one (1) ingredient");
        }
        else {
            document.getElementById("item-container").deleteRow(-1);
            document.getElementById("row_count").value = itemCount;
        }
    }
</script>

