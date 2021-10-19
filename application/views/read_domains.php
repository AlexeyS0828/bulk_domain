<!doctype html>
<html lang="en">
  <head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Domains</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
  <div class="mt-3">
        <table class="table table-bordered" id="users-list">
            <thead>
                <tr>
                    <th>All Domains</th>
                    <th>Success</th>
                    <th>Expired</th>
                    <th>Expired Domains</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td id="result_count"><?php echo $data['count']; ?></td>
                    <td id="result_success"><?php echo $data['success']; ?></td>
                    <td id="result_fail"><?php echo $data['fail']; ?></td>
                    <td id="result_fails"><?php 
                        $i = 0;
                        foreach($data['fails'] as $domain){
                            echo "<b>".(++$i).":</b> &nbsp;&nbsp;&nbsp;" . $domain . "<br>";
                        }
                    ?></td>
                </tr>
            </tbody>
        </table>
        <div class="alert alert-success result-status">
            <?php 
                if(isset($data['message'])){
                    echo $data['message'];
                }else{
                    echo 'scanning'; 
                }
            ?>
        </div>
  </div>
</div>
<div class="form-group" style="display: flex;">
	<div class="form-group-item" style="width: 70%;">
		&nbsp;
	</div>
    <div class="form-group-item">
		<a href="<?php echo site_url('domains/run_existing_scheduler');?>" class="btn btn-success btn-lg btn-block" style="color:#fff;">Scan</a>
	</div>
    <div class="form-group-item">
		<a href="<?php echo site_url('domains/add_domains');?>" class="btn btn-success btn-lg btn-block" style="color:#fff;">Add Domains</a>
	</div>
	<div class="form-group-item">
		<a href="<?php echo site_url('domains');?>" class="btn btn-success btn-lg btn-block" style="color:#fff;">Return to List</a>
	</div>
	<div class="form-group-item">
		<a href="<?php echo site_url('Logout');?>" class="btn btn-success btn-lg btn-block" style="color:#fff;">Logout</a>
	</div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.js" type="text/javascript"></script>

<script>
    function run_scheduler(scheduler_id){
        $.ajax({
            type: "POST",
            url: "<?php echo site_url("/domains/run_scheduler"); ?>", 
            data: {id: scheduler_id},
            dataType: "text",  
            cache:false,
            success: 
                function(data){
                    result = JSON.parse(data);
                    $("#result_success").html(parseInt($("#result_success").html()) + result.result.success);
                    $("#result_fail").html(parseInt($("#result_fail").html()) + result.result.fail);
                    var expired_domains = $("#result_fails").html();
                    expired_domains += (expired_domains != "" ? "<br>" : "") + result.result.fails.join("<br>")
                    $("#result_fails").html(expired_domains);
                    if(result.result.count>0){
                        $(".result-status").text("running  " + scheduler_id + " domains " + result.result.count);
                        run_scheduler(scheduler_id);
                    }else{
                        $(".result-status").text("completed scaning");
                    }
                }
        });
    }
    $(document).ready(function(){
        var scheduler_id = <?php echo($data['scheduler_id']); ?>;
        if (!isNaN(scheduler_id))
            run_scheduler(scheduler_id);        
    });

</script>
</body>
</html>