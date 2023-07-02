<?php
    class Ingredient {
        // Properties
        private $ingredient_name;
        private $dbc;

        // Getters and Setters
        public function getIngredientName() {
            return $this -> ingredient_name;
        }
        public function setIngredientName($name) {
            $this -> ingredient_name = $name;
        }

        // Connect to database
        public function connectToDatabase() {
            require_once('dbconnection.php');
            $this -> dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or trigger_error('Error connecting to MySQL server' . DB_NAME, E_USER_ERROR);
        }

        // Insert into database
        public function insertIngredient() {
            if (!$this -> dbc) {
                $this -> connectToDatabase();
            }
            require_once('dbconnection.php');
            require_once('queryutils.php');

            // Check if item is already in database
            $query = "SELECT IngredientID FROM Ingredient WHERE IngredientName LIKE ?";
            $result = parameterizedQuery($this -> dbc, $query, 's', $this -> ingredient_name) or trigger_error('Error querying database ' . DB_NAME, E_USER_ERROR);

            if (mysqli_num_rows($result) == 0) {
                $query = "INSERT INTO Ingredient (IngredientName) VALUES (?)";
                parameterizedQuery($this -> dbc, $query, 's', $this -> ingredient_name) or trigger_error('Error querying database ' . DB_NAME, E_USER_WARNING);
            }
            else {
                echo '<p class="danger-text">This ingredient is already in the list</p>';
            }
        }

        // Get all ingredients
        public function ingredientList() {
            if (!$this -> dbc) {
                $this -> connectToDatabase();
            }
            require_once('queryutils.php');

            $query = "SELECT IngredientName FROM Ingredient ORDER BY IngredientName ASC";
            $result = mysqli_query($this -> dbc, $query) or trigger_error('Error querying database.', E_USER_WARNING);

            return $result;
        }

        // Get ingredient ID
        public function getIngredientID() {
            if (!$this -> dbc) {
                $this -> connectToDatabase();
            }
            require_once('queryutils.php');

            $query = "SELECT IngredientID FROM Ingredient WHERE IngredientName = ?";
            $result = parameterizedQuery($this -> dbc, $query, 's', $this -> ingredient_name);
            $ingredient_id = mysqli_fetch_assoc($result)['IngredientID'];
            return $ingredient_id;
        }
    }
?>