<?php
    if (!isset($_GET['profileTypeId'])) {
        $profile_type = 0;
    } else if ($_GET['profileTypeId'] == 1) {
        $profile_type = 1;
    } else if ($_GET['profileTypeId'] == 2) {
        $profile_type = 2;
    } else {
        $profile_type = 0;
    }
?>
<div class="form-group col-xs-1" style="margin-right:70vw">
    <form method="get">
    <input type="hidden" name="source" value="profile_add_athlete">
    <label for="profileTypeId">Profile Type</label>
    <select name='profileTypeId' onchange="this.form.submit();" required>;
        <option disabled <?php if ($profile_type == 0) echo "selected"; ?>>Choose one...</option>
        <option value="1" <?php if ($profile_type == 1) echo "selected"; ?>>Athlete</option>
        <option value="2" <?php if ($profile_type == 2) echo "selected"; ?>>Coach</option>
    </select>
    </form>
</div>
<form action="" method="post">
    <?php addAthleteInfo(); ?>
    <input type='number' style="display:none" name='profileTypeId' value=<?php echo $profile_type; ?> min="1">
    <div class="form-group">
        <div class="row">
            <div class="form-group col-xs-4">
                <label for="firstName">First Name</label>
                <input value="" type="text" class="form-control" name="firstName" >
            </div>
            <div class="form-group col-xs-2">
                <label for="middle">Middle Name</label>
                <input value="" type="text" class="form-control" name="middle" >
            </div>
            <div class="form-group form-group col-xs-4">
                <label for="lastName">Last Name</label>
                <input value="" type="text" class="form-control" name="lastName" >
            </div>
            <div class="form-group col-xs-1">
                <label for="suffix">Suffix</label>
                <select name='suffix'>;
                    <option value="">choose suffix</option>
                    <?php
                        $suffix_query = "SELECT * FROM suffix";
                        $all_suffix_query = mysqli_query($connection_production, $suffix_query);
                        while($row = mysqli_fetch_assoc($all_suffix_query)){
                            echo "<option value=".$row['id'].">".$row['suffix']."</option>";
                        }
                     ?>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-xs-3">
                <?php
                    if ($profile_type != 2) {
                ?>
                        <label for="nickname">Nickname</label>
                        <input value="" type="text" class="form-control" name="nickname" >
                <?php
                    } else {
                ?>
                        <label for="specialty">Specialty</label>
                        <input value="" type="text" class="form-control" name="specialty" >
                <?php } ?>
            </div>
            <div class="form-group col-xs-3">
                <label for="birthdate">Birthdate</label>
                <input value="" type="date" class="form-control" name="birthdate" >
            </div>
            <div class="form-group col-xs-1">
                <label for="status">Status</label>
                <select name='status'>;
                    <option value="complete">Complete</option>
                    <option value="incomplete">Incomplete</option>
                    <option value="disabled">Disabled</option>
                </select>
            </div>
            <div class="form-group col-xs-1">
                <label for="gender">Gender</label>
                <select name='gender'>;
                    <option value="M">Male</option>
                    <option value="F">Female</option>
                    <option value="O">Other</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="summary">Profile Summary</label>
            <textarea class="form-control" name="summary" id="" cols="30" rows="10"></textarea>
        </div>
        <div class="form-group">
            <label for="acclaim">Profile Acclaim</label>
            <textarea class="form-control" name="acclaim" id="" cols="30" rows="10"></textarea>
        </div>
        </div>
        <div class="form-group">
            <?php
                if ($profile_type == 0) echo "Profile type must be selected.";
            ?>
            <input class="btn btn-primary" type="submit" name="add_athlete_info" value="Add">
        </div>
    </div>
</form>
