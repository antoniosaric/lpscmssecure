<?php
    function findAllImages($filter, $pageinate, $words) {
        global $connection_production;

        $img_sql = "SELECT image.id AS image_id, image.imageName AS image_name, image_type.type AS image_type,
            activity_image_sm.activityId AS activity_id, entity_activity_sm_image_sm.entityActivitySmId AS e_a_sm_id,
            entity_image_sm.entityId AS entity_id, institution_image_sm.institutionId AS institution_id,
            participant_image_sm.participantId AS participant_id, partition_image_sm.partitionId AS partition_id,
            profile_activity_sm_image_sm.profileActivitySmId AS p_a_sm_id, profile_franchise_sm_image_sm.profileFranchiseSmId AS p_f_sm_id,
            profile_entity_activity_sm_partition_sm_sm_image_sm.profileEntityActivitySmPartitionSmSmId AS p_e_a_sm_p_sm_id,
            profile_image_sm.profileId AS profile_id, profile_partition_sm_image_sm.profilePartitionSmId AS p_p_sm_id,
            profile_team_sm_image_sm.profileTeamSmId AS p_t_sm_id, school_image_sm.schoolId AS school_id,
            team_image_sm.teamId AS team_id FROM image
            LEFT JOIN image_type ON image_type.id = image.imageTypeId
            LEFT JOIN activity_image_sm ON activity_image_sm.imageId = image.id
            LEFT JOIN entity_activity_sm_image_sm ON entity_activity_sm_image_sm.imageId = image.id
            LEFT JOIN entity_image_sm ON entity_image_sm.imageId = image.id
            LEFT JOIN institution_image_sm ON institution_image_sm.imageId = image.id
            LEFT JOIN participant_image_sm ON participant_image_sm.imageId = image.id
            LEFT JOIN partition_image_sm ON partition_image_sm.imageId = image.id
            LEFT JOIN profile_activity_sm_image_sm ON profile_activity_sm_image_sm.imageId = image.id
            LEFT JOIN profile_entity_activity_sm_partition_sm_sm_image_sm ON profile_entity_activity_sm_partition_sm_sm_image_sm.imageId = image.id
            LEFT JOIN profile_franchise_sm_image_sm ON profile_franchise_sm_image_sm.imageId = image.id
            LEFT JOIN profile_image_sm ON profile_image_sm.imageId = image.id
            LEFT JOIN profile_partition_sm_image_sm ON profile_partition_sm_image_sm.imageId = image.id
            LEFT JOIN profile_team_sm_image_sm ON profile_team_sm_image_sm.imageId = image.id
            LEFT JOIN school_image_sm ON school_image_sm.imageId = image.id
            LEFT JOIN team_image_sm ON team_image_sm.imageId = image.id "
            .$filter." LIMIT ?, 20";
        $stmt = $connection_production->prepare($img_sql);

        $bind_parameters = array();
        $bind_parameters[0] = "";
        if (!empty($words)) {
            foreach ($words as $key) {
                $bind_parameters[0] = $bind_parameters[0]."ssi";
                $format_param = '%'.$key.'%';
                array_push($bind_parameters, $format_param);
                array_push($bind_parameters, $format_param);
                array_push($bind_parameters, $key);
            }
        }
        $bind_parameters[0] = $bind_parameters[0]."i";
        array_push($bind_parameters, $pageinate);
        call_user_func_array(array($stmt, "bind_param"), refValues($bind_parameters));
        $stmt->execute();
        $select_all_images_result = $stmt->get_result();
        $stmt->close();

        while ($row = mysqli_fetch_assoc($select_all_images_result)) {
            $image_id = $row['image_id'];
            $image_name = $row['image_name'];
            $image_type = $row['image_type'];

            $href_link = "";
            if (!!$row['activity_id']) {
                $assoc_id = $row['activity_id'];
                $assoc_name = "activity";
                $assoc_table = "activity_image_sm";
                $href_link = "<a href='categories.php?source=sports_update&activity_id=".$assoc_id."'>";
            } else if (!!$row['e_a_sm_id']) {
                $assoc_id = $row['e_a_sm_id'];
                $assoc_name = "entity_activity_sm";
                $assoc_table = "entity_activity_sm_image_sm";
            } else if (!!$row['entity_id']) {
                $assoc_id = $row['entity_id'];
                $assoc_name = "entity";
                $assoc_table = "entity_image_sm";
                $href_link = "<a href='categories.php?source=league_update&entity_id=".$assoc_id."'>";
            } else if (!!$row['institution_id']) {
                $assoc_id = $row['institution_id'];
                $assoc_name = "institution";
                $assoc_table = "institution_image_sm";
            } else if (!!$row['participant_id']) {
                $assoc_id = $row['participant_id'];
                $assoc_name = "participant";
                $assoc_table = "participant_image_sm";
            } else if (!!$row['partition_id']) {
                $assoc_id = $row['partition_id'];
                $assoc_name = "partition";
                $assoc_table = "partition_image_sm";
                $href_link = "<a href='categories.php?source=update_partition&partition_id=".$assoc_id."'>";
            } else if (!!$row['p_a_sm_id']) {
                $assoc_id = $row['p_a_sm_id'];
                $assoc_name = "profile_activity_sm";
                $assoc_table = "profile_activity_sm_image_sm";
            } else if (!!$row['p_f_sm_id']) {
                $assoc_id = $row['p_f_sm_id'];
                $assoc_name = "profile_franchise_sm";
                $assoc_table = "profile_franchise_sm_image_sm";
            } else if (!!$row['p_e_a_sm_p_sm_id']) {
                $assoc_id = $row['p_e_a_sm_p_sm_id'];
                $assoc_name = "profile_entity_activity_sm_partition_sm_sm";
                $assoc_table = "profile_entity_activity_sm_partition_sm_sm_image_sm";
            } else if (!!$row['profile_id']) {
                $assoc_id = $row['profile_id'];
                $assoc_name = "profile";
                $assoc_table = "profile_image_sm";
                $href_link = "<a href='categories.php?source=profile_update_athlete&profile_id=".$assoc_id."'>";
            } else if (!!$row['p_p_sm_id']) {
                $assoc_id = $row['p_p_sm_id'];
                $assoc_name = "profile_partition_sm";
                $assoc_table = "profile_partition_sm_image_sm";
            } else if (!!$row['p_t_sm_id']) {
                $assoc_id = $row['p_t_sm_id'];
                $assoc_name = "profile_team_sm";
                $assoc_table = "profile_team_sm_image_sm";
            } else if (!!$row['school_id']) {
                $assoc_id = $row['school_id'];
                $assoc_name = "school";
                $assoc_table = "school_image_sm";
            } else if (!!$row['team_id']) {
                $assoc_id = $row['team_id'];
                $assoc_name = "team";
                $assoc_table = "team_image_sm";
                $href_link = "<a href='categories.php?source=team_update&team_id=".$assoc_id."'>";
            } else {
                $assoc_id = NULL;
                $assoc_name = "None";
                $assoc_table = NULL;
            }

            echo "<tr>";
            echo "<td>".$image_id."</td>";
            echo "<td>".$image_name."</td>";
            echo "<td>".$image_type."</td>";
            echo "<td>".$assoc_name."</td>";
            echo "<td>".$href_link.$assoc_id.(empty($href_link) ? "</td>" : "</a></td>");
            echo "<td><a class='btn btn-info' href='categories.php?source=update_image&image_id=".$image_id."'>Edit</a></td>";

            echo "<td><form method='post'>";
            echo "<input type='hidden' name='image_id' value=".$image_id.">";
            echo "<input type='hidden' name='image_name' value=".$image_name.">";
            echo "<input type='hidden' name='folder' value=".$assoc_name.">";
            echo "<input type='hidden' name='linked_table' value=".$assoc_table.">";
            echo "<input onClick=\"return confirm('Are you sure you want to do that?');\" class='btn btn-danger' type='submit' name='delete_image' value='DELETE' />";
            echo "</form></td>";

            echo "</tr>";
        }
    }
    function deleteImageFile() {
        global $connection_production;
        if (isset($_POST['delete_image'])) {
            $image_id = $_POST['image_id'];
            $image_name = $_POST['image_name'];
            $folder = $_POST['folder'];
            $linked_table = $_POST['linked_table'];
            $no_assoc = empty($linked_table) || strcasecmp($linked_table, "none") == 0;

            $del_img_sql = "DELETE FROM image WHERE id=?";
            $del_img_stmt = $connection_production->prepare($del_img_sql);
            $del_img_stmt->bind_param("i", $image_id);

            if (!$no_assoc) {
                $del_assoc_sql = "DELETE FROM ".$linked_table." WHERE ".$linked_table.".imageId=?";
                $del_assoc_stmt = $connection_production->prepare($del_assoc_sql);
                $del_assoc_stmt->bind_param("i", $image_id);
            }

            $activity_bool = strcasecmp(trim($folder), "activity") == 0;
            $folder = $activity_bool ? "activity-black" : $folder;

            $delete_file_updates = array();

            $target_dir = realpath(__DIR__."/../../../../assets/images/".$folder);
            if ($target_dir !== false) {
                $target_file = $target_dir."/".$image_name;

                if (file_exists($target_file)) {
                    if (is_writable($target_file) && unlink($target_file)) {
                        array_push($delete_file_updates, "deleted file assets/images/".$folder."/".$image_name);
                    } else {
                        array_push($delete_file_updates, "error deleting assets/images/".$folder."/".$image_name);
                    }
                } else {
                    array_push($delete_file_updates, "file: assets/images/".$folder."/".$image_name." - not found");
                }
            } else {
                array_push($delete_file_updates, "directory: ".$target_dir." - not found");
            }
            if ($activity_bool) {

                $target_dir = realpath(__DIR__."/../../../../assets/images/activity-blue");
                if ($target_dir !== false) {
                    $target_file = $target_dir."/".$image_name;

                    if (file_exists($target_file)) {
                        if (is_writable($target_file) && unlink($target_file)) {
                            array_push($delete_file_updates, "deleted file assets/images/activity-blue/".$image_name);
                        } else {
                            array_push($delete_file_updates, "error deleting assets/images/activity-blue/".$image_name);
                        }
                    } else {
                        array_push($delete_file_updates, "file: assets/images/activity-blue/".$image_name." - not found");
                    }
                } else {
                    array_push($delete_file_updates, "directory: ".$target_dir." - not found");
                }
            }

            if ($no_assoc || $del_assoc_stmt->execute()) {
                if ($del_img_stmt->execute()) {
                    $update_string = "deleted image id: ".$image_id.", deleted association in ".$linked_table." table, ";
                    insertChange($_SESSION['account_id'], 'image', 'delete image', $image_id, $update_string.implode(", ", $delete_file_updates));
                    header("location: categories.php?source=images");
                } else {
                    echo "<h3 style='color:red;'>Something went wrong deleting image</h3>";
                }
            } else {
                echo "<h3 style='color:red;'>Something went wrong deleting ".$linked_table." association</h3>";
            }
            $del_img_stmt->close();
            $del_assoc_stmt->close();
        }
    }
    function uploadImage() {
        global $connection_production;
        if (isset($_POST['upload_image'])) {
            $image_name = $_POST['image_name'];
            list($folder, $link_column, $assoc_table) = explode(":", $_POST['image_assoc']);
            $linked_id = $_POST['linked_id'];
            $type_id = $_POST['type_id'];

            if (strcasecmp(trim($folder), "partition") == 0) {
                $find_linked_sql = "SELECT * FROM `partition` AS partition_LP WHERE id=?";
            } else {
                $find_linked_sql = "SELECT * FROM ".$folder." WHERE id=?";
            }
            $find_linked_stmt = $connection_production->prepare($find_linked_sql);
            $find_linked_stmt->bind_param("i", $linked_id);
            $find_linked_stmt->execute();
            $row_find_linked = $find_linked_stmt->get_result()->fetch_assoc();
            $find_linked_stmt->close();

            if (!!$row_find_linked['id']) {

                $activity_bool = strcasecmp(trim($folder), "activity") == 0;
                $folder = $activity_bool ? "activity-black" : $folder;
                $update_string = "";

                $target_dir = realpath(__DIR__."/../../../../assets/images/".$folder);
                if ($target_dir !== false) {
                    $target_file = $target_dir."/".$image_name;

                    if (!file_exists($target_file)) {

                        if (is_uploaded_file($_FILES['image_file']['tmp_name'][0]) && $_FILES['image_file']['error'][0] == 0) {

                            $check = getimagesize($_FILES['image_file']['tmp_name'][0]);
                            if ($check !== false) {

                                if (move_uploaded_file($_FILES['image_file']['tmp_name'][0], $target_file)) {

                                    $image_sql = "INSERT INTO image (imageName, imageTypeId) VALUES (?, ?)";
                                    $image_stmt = $connection_production->prepare($image_sql);
                                    $image_stmt->bind_param("si", $image_name, $type_id);

                                    if ($image_stmt->execute()) {
                                        $new_img_id = $connection_production->insert_id;
                                        createUUID('image', $new_img_id);

                                        $insert_assoc_sql = "INSERT INTO ".$assoc_table." (imageId, ".$link_column.") VALUES (?, ?)";
                                        $insert_assoc_stmt = $connection_production->prepare($insert_assoc_sql);
                                        $insert_assoc_stmt->bind_param("ii", $new_img_id, $linked_id);

                                        if ($insert_assoc_stmt->execute()) {

                                            $update_string .= "Uploaded image ID: ".$new_img_id." :: name: ".$image_name.", imageTypeId: ".$type_id.", folder: assets/images/".$folder;
                                            $update_string .= ", added association in ".$assoc_table." to ".$link_column.": ".$linked_id;
                                            if (!$activity_bool) {
                                                insertChange($_SESSION['account_id'], 'image', 'upload image', $new_img_id, $update_string);
                                                header("location: categories.php?source=update_image&image_id=".$new_img_id);
                                            }
                                        } else {
                                            echo "<h3 style='color:red;'>Something went wrong inserting into ".$assoc_table." table</h3>";
                                        }
                                        $insert_assoc_stmt->close();
                                    } else {
                                        echo "<h3 style='color:red;'>Something went wrong inserting into image table</h3>";
                                    }
                                    $image_stmt->close();
                                } else {
                                    echo "<h3 style='color:red;'>Something went wrong moving the image to ".$target_file."</h3>";
                                }
                            } else {
                                echo "<h3 style='color:red;'>File is not an image</h3>";
                            }
                        } else {
                            echo "<h3 style='color:red;'>Image not uploaded / error</h3>";
                        }
                    } else {
                        echo "<h3 style='color:red;'>Error: ".$target_file." - already exists</h3>";
                    }
                } else {
                    echo "<h3 style='color:red;'>assets/images/".$folder."/ - Directory not found</h3>";
                }
                if ($activity_bool && !empty($update_string)) {

                    $target_dir = realpath(__DIR__."/../../../../assets/images/activity-blue");
                    if ($target_dir !== false) {
                        $target_file = $target_dir."/".$image_name;

                        if (!file_exists($target_file)) {

                            if (is_uploaded_file($_FILES['image_file']['tmp_name'][1]) && $_FILES['image_file']['error'][1] == 0) {

                                $check = getimagesize($_FILES['image_file']['tmp_name'][1]);
                                if ($check !== false) {

                                    if (move_uploaded_file($_FILES['image_file']['tmp_name'][1], $target_file)) {

                                        insertChange($_SESSION['account_id'], 'image', 'upload image', $new_img_id, $update_string.", added activity-blue image");
                                        header("location: categories.php?source=update_image&image_id=".$new_img_id);
                                    } else {
                                        echo "<h3 style='color:red;'>Something went wrong moving the image to ".$target_file."</h3>";
                                    }
                                } else {
                                    echo "<h3 style='color:red;'>Activity-blue file is not an image</h3>";
                                }
                            } else {
                                echo "<h3 style='color:red;'>Activity-blue image not uploaded / error</h3>";
                            }
                        } else {
                            echo "<h3 style='color:red;'>Error: ".$target_file." - already exists</h3>";
                        }
                    } else {
                        echo "<h3 style='color:red;'>assets/images/activity-blue/ - Directory not found</h3>";
                    }
                }
            } else {
                echo "<h3 style='color:red;'>".$folder." ID: ".$linked_id." - Not found</h3>";
            }
        }
    }
    function updateImageFile() {
        global $connection_production;
        if (isset($_POST['update_image'])) {
            $image_id = $_POST['image_id'];
            $image_name = trim($_POST['image_name']);
            $image_type_id = $_POST['type_id'];
            $folder = $_POST['folder'];

            $existing_image_sql = "SELECT image.imageName AS image_name, image_type.id AS image_type_id
                FROM image LEFT JOIN image_type ON image_type.id = image.imageTypeId
                WHERE image.id=?";
            $existing_image_stmt = $connection_production->prepare($existing_image_sql);
            $existing_image_stmt->bind_param("i", $image_id);
            $existing_image_stmt->execute();
            $row_existing_image = $existing_image_stmt->get_result()->fetch_assoc();
            $existing_image_stmt->close();

            $existing_image_name = trim($row_existing_image['image_name']);
            $existing_image_type_id = $row_existing_image['image_type_id'];

            $update_string = "Update image ID: ".$image_id." :: ";
            $updates = array();

            $new_name_bool = strcasecmp($image_name, $existing_image_name) != 0;
            $activity_bool = strcasecmp(trim($folder), "activity") == 0;
            $folder = $activity_bool ? "activity-black" : $folder;

            $target_dir = realpath(__DIR__."/../../../../assets/images/".$folder);
            if ($target_dir !== false) {
                $old_target_file = $target_dir."/".$existing_image_name;
                $new_target_file = $target_dir."/".$image_name;

                if (!($new_name_bool && file_exists($new_target_file))) {

                    if (is_uploaded_file($_FILES['image_file']['tmp_name'][0])) {
                        if ($_FILES['image_file']['error'][0] == 0) {

                            $check = getimagesize($_FILES['image_file']['tmp_name'][0]);
                            if ($check !== false) {

                                if (file_exists($old_target_file)) {
                                    if (is_writable($old_target_file) && unlink($old_target_file)) {
                                        array_push($updates, "delete old file: assets/images/".$folder."/".$existing_image_name);
                                    } else {
                                        echo "<h3 style='color:red;'>Error deleting ".$old_target_file." for replacement</h3>";
                                        return;
                                    }
                                }

                                if (move_uploaded_file($_FILES['image_file']['tmp_name'][0], $new_target_file)) {
                                    array_push($updates, "add new file: assets/images/".$folder."/".$image_name);
                                } else {
                                    echo "<h3 style='color:red;'>Error adding new file: ".$new_target_file."</h3>";
                                    return;
                                }
                            } else {
                                echo "<h3 style='color:red;'>Uploaded file is not an image</h3>";
                                return;
                            }
                        } else {
                            echo "<h3 style='color:red;'>Uploaded image has an error</h3>";
                            return;
                        }
                    } else {

                        if (file_exists($old_target_file) && $new_name_bool) {
                            if (is_writable($old_target_file) && rename($old_target_file, $new_target_file)) {
                                array_push($updates, "rename assets/images/".$folder."/".$existing_image_name." to assets/images/".$folder."/".$image_name);
                            } else {
                                echo "<h3 style='color:red;'>Error renaming ".$old_target_file." to ".$new_target_file."</h3>";
                                return;
                            }
                        }
                    }
                } else {
                    echo "<h3 style='color:red;'>Error: ".$new_target_file." - already exists</h3>";
                    return;
                }
            } else {
                echo "<h3 style='color:red;'>assets/images/".$folder."/ - Directory not found</h3>";
                return;
            }
            if ($activity_bool) {
                $target_dir = realpath(__DIR__."/../../../../assets/images/activity-blue");
                if ($target_dir !== false) {
                    $old_target_file = $target_dir."/".$existing_image_name;
                    $new_target_file = $target_dir."/".$image_name;

                    if (!($new_name_bool && file_exists($new_target_file))) {

                        if (is_uploaded_file($_FILES['image_file']['tmp_name'][1])) {
                            if ($_FILES['image_file']['error'][1] == 0) {

                                $check = getimagesize($_FILES['image_file']['tmp_name'][1]);
                                if ($check !== false) {

                                    if (file_exists($old_target_file)) {
                                        if (is_writable($old_target_file) && unlink($old_target_file)) {
                                            array_push($updates, "delete old file: assets/images/activity-blue/".$existing_image_name);
                                        } else {
                                            echo "<h3 style='color:red;'>Error deleting ".$old_target_file." for replacement</h3>";
                                            return;
                                        }
                                    }

                                    if (move_uploaded_file($_FILES['image_file']['tmp_name'][1], $new_target_file)) {
                                        array_push($updates, "add new file: assets/images/activity-blue/".$image_name);
                                    } else {
                                        echo "<h3 style='color:red;'>Error adding new file: ".$new_target_file."</h3>";
                                        return;
                                    }
                                } else {
                                    echo "<h3 style='color:red;'>Uploaded activity-blue file is not an image</h3>";
                                    return;
                                }
                            } else {
                                echo "<h3 style='color:red;'>Uploaded activity-blue image has an error</h3>";
                                return;
                            }
                        } else {

                            if (file_exists($old_target_file) && $new_name_bool) {
                                if (is_writable($old_target_file) && rename($old_target_file, $new_target_file)) {
                                    array_push($updates, "rename assets/images/activity-blue/".$existing_image_name." to assets/images/activity-blue/".$image_name);
                                } else {
                                    echo "<h3 style='color:red;'>Error renaming ".$old_target_file." to ".$new_target_file."</h3>";
                                    return;
                                }
                            }
                        }
                    } else {
                        echo "<h3 style='color:red;'>Error: ".$new_target_file." - already exists</h3>";
                        return;
                    }
                } else {
                    echo "<h3 style='color:red;'>assets/images/activity-blue/ - Directory not found</h3>";
                    return;
                }
            }

            if ($image_type_id != $existing_image_type_id) {
                $update_type_sql = "UPDATE image SET imageTypeId=? WHERE id=?";
                $update_type_stmt = $connection_production->prepare($update_type_sql);
                $update_type_stmt->bind_param("ii", $image_type_id, $image_id);
                if ($update_type_stmt->execute()) {
                    array_push($updates, "update image_type_id to: ".$image_type_id);
                    $update_type_stmt->close();
                } else {
                    echo "<h3 style='color:red;'>Error updating image type</h3>";
                    $update_type_stmt->close();
                    return;
                }
            }

            if ($new_name_bool) {
                $update_name_sql = "UPDATE image SET imageName=? WHERE id=?";
                $update_name_stmt = $connection_production->prepare($update_name_sql);
                $update_name_stmt->bind_param("si", $image_name, $image_id);
                if ($update_name_stmt->execute()) {
                    array_push($updates, "update image_name to: ".$image_name);
                    $update_name_stmt->close();
                } else {
                    echo "<h3 style='color:red;'>Error updating image name</h3>";
                    $update_name_stmt->close();
                    return;
                }
            }

            $update_string .= implode(", ", $updates);
            insertChange($_SESSION['account_id'], 'image', 'update image', $image_id, $update_string);
            header("location: categories.php?source=update_image&image_id=".$image_id);
        }
    }
?>
