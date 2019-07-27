<?php addTeamProfile(); ?>
<form action="" method="post">
    <div class="form-group">
        <div class="row">
            <div class="form-group col-xs-4">
                <label for="locale">Locale</label>
                <input value="" type="text" class="form-control" name="locale" >
            </div>
            <div class="form-group col-xs-4">
                <label for="name">Name</label>
                <input value="" type="text" class="form-control" name="name" >
            </div>
        </div>
        <div class="row">
            <div class="form-group col-xs-2">
                <label for="nickname">Nickname</label>
                <input value="" type="text" class="form-control" name="nickname" >
            </div>  
        </div>
        <div class="row">
            <div class="form-group col-xs-2">
                <label for="status">Status</label>
                <select name='status'>;
                    <option value="complete">Complete</option>
                    <option value="incomplete">Incomplete</option>
                    <option value="disabled">Disabled</option>
                </select>
            </div>  
        </div>
        <div class="form-group">
            <label for="teamdescription">Team Profile Summary</label>
            <textarea class="form-control" name="teamdescription" id="" cols="30" rows="10"></textarea>
        </div>                
        <div class="form-group">
            <input class="btn btn-primary" type="submit" name="add_team_info" value="add team">
        </div>
    </div>
</form>