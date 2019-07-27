<script>
    function togglePopup() {
        var div = document.getElementById("toggle_popup");
        if (div.style.display == "none") div.style.display = "block";
        else div.style.display = "none";
    }
</script>
<?php
    if (empty($_SESSION['saved_keyvalues'])) {
        $saved_keyvalues = array();
        $_SESSION['saved_keyvalues'] = $saved_keyvalues;
    } else {
        $saved_keyvalues = $_SESSION['saved_keyvalues'];
    }

    $initial_display = "none";
    if (isset($_POST['save_keyvalue'])) {
        $key = $_POST['save_key'];
        $value = $_POST['save_value'];
        $saved_keyvalues[$key] = $value;
        $_SESSION['saved_keyvalues'] = $saved_keyvalues;
        $initial_display = "block";
    } else if (isset($_POST['clear_keyvalues'])) {
        $saved_keyvalues = array();
        $_SESSION['saved_keyvalues'] = $saved_keyvalues;
        $initial_display = "block";
    }

    echo "<div id='toggle_popup' style='border:1px solid red; border-radius:10px; padding:15px; display:".$initial_display."'>";
    if (!empty($saved_keyvalues)) {
        echo "<label>Saved info:</label>";
        echo "<table class='table table-striped table-bordered table-hover' style='max-width: 40vw'>";
        $count = 0;
        foreach ($saved_keyvalues as $key => $value) {
            if ($count == 0) echo "<tr>";
            $count++;
            echo "<td>".$key.": ".$value."</td>";
            if ($count == 4) {
                echo "</tr>";
                $count = 0;
            }
        }
        echo "</table>";
    }
        echo "<form method='post' id='clear_info'></form>";
        echo "<form method='post' id='popup_form'>";
            echo "<div class='form-group'>";
                echo "<div class='row'>";
                echo "<div class='form-group col-xs-2'>";
                    echo "<label for='save_key'>Key:</label><br>";
                    echo "<input type='text' name='save_key' required pattern='.*\S+.*' />";
                echo "</div>";
                echo "<div class='form-group col-xs-2'>";
                    echo "<label for='save_value'>Value:</label><br>";
                    echo "<input type='text' name='save_value' required pattern='.*\S+.*' />";
                echo "</div>";
                echo "<div class='form-group col-xs-2'>";
                    echo "<br><input form='popup_form' class='btn btn-primary' type='submit' name='save_keyvalue' value='Save' style='margin-right:1.5vw' />";
                    if (!empty($saved_keyvalues)) {
                        echo "<input form='clear_info' class='btn btn-danger' onClick=\"return confirm('Clear all saved key/values?');\" type='submit' name='clear_keyvalues' value='Clear' />";
                    }
                echo "</div>";
                echo "</div>";
            echo "</div>";
        echo "</form>";
    echo "</div>";

    echo "<input class='btn btn-primary' onClick='togglePopup();' type='submit' value='Show/hide saved info' style='margin:1vw' />";
?>
