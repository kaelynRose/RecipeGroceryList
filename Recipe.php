<?php
    class Recipe {
        // Properties
        private $recipe_name;
        private $recipe_tags;
        private $recipe_protein;
        private $dbc;

        // Getters and setters
        public function getRecipeName() {
            return $this -> recipe_name;
        }
        public function getRecipeTags() {
            return $this -> recipe_tags;
        }
        public function getRecipeProtein() {
            return $this -> recipe_protein;
        }
        public function setRecipeName($name) {
            $this -> recipe_name = $name;
        }
        public function setRecipeTags($tags) {
            $this -> recipe_tags = $tags;
        }
        public function setRecipeProtein($protein) {
            $this -> recipe_protein = $protein;
        }

        // Connect to database
        public function connectToDatabase() {
            require_once('dbconnection.php');
            $this -> dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or trigger_error('Error connecting to MySQL server' . DB_NAME, E_USER_ERROR);
        }

        // Insert into database
        public function insertRecipe() {
            if (!$this -> dbc) {
                $this -> connectToDatabase();
            }
            require_once('dbconnection.php');
            require_once('queryutils.php');

            // Check if recipe is already in database
            $query = "SELECT RecipeID, RecipeName FROM Recipe WHERE RecipeName LIKE ?";
            $result = parameterizedQuery($this -> dbc, $query, 's', $this -> recipe_name);

            if (mysqli_num_rows($result) == 0) {
                // Insert recipe into database
                $query = "INSERT INTO Recipe (RecipeName, Tags, Protein) VALUES (?, ?, ?)";
                parameterizedQuery($this -> dbc, $query, 'sss', $this -> recipe_name, $this -> recipe_tags, $this -> recipe_protein);

            }
            else {
                echo '<p class="center">Similar recipe(s) found:</p>';
                echo '<table><tbody>';
                while ($row = mysqli_fetch_array($result)) {
                    echo '<tr><td><a href="viewrecipe.php?id=' . $row['RecipeID'] . '">' . $row['RecipeName'] .'</a></td></tr>';
                }
                echo '</tbody></table>';
            }
        }
        // Retrieve recipe ID
        public function getRecipeID() {
            if (!$this -> dbc) {
                $this -> connectToDatabase();
            }
            require_once('dbconnection.php');
            require_once('queryutils.php');

            // Get recipe id
            $query = "SELECT RecipeID FROM Recipe WHERE RecipeName = ?";
            $result = parameterizedQuery($this -> dbc, $query, 's', $this -> recipe_name);
            $row = mysqli_fetch_assoc($result);
            $recipe_id = $row['RecipeID'];
            return $recipe_id;
        }
    }
?>