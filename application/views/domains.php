<!doctype html>
<html lang="en">
  <head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Domains</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
</head>
<body>
<?php
	$keys = [
		'Domain_Name',
		'Registrant_Name',
		'Registrar',
		'Creation_Date',
		'Updated_Date',
		'Registry_Expiry_Date',
		'Drop_Date',
		'Name_Server'
	];
?>
<div class="container-fluid mt-5">
  <div class="mt-3">
     <table class="table table-bordered" id="users-list">
       <thead>
          <tr>
		  	<th>No</th>
			<?php foreach($keys as $key): ?>
             <th><?php echo $key; ?></th>
			<?php endforeach; ?>
			<th>Expired Domain</th>
			<th>Delete</th>
          </tr>
       </thead>
       <tbody>
	   <?php
			$i = 0;
			foreach($domains as $domain){
				echo "<tr id='$domain->id'><td>" . ++$i . "</td>";
				foreach($keys as $key){
					echo "<td>{$domain->$key}</td>";
				}
				echo "<td>". ($domain->expired==1?"expired":"-") . "</td>";
				echo "<td><a href='#' onclick='drop_domain(" . $domain->id . ")'>Remove</a></td>";
				echo "</tr>";
			}
		?>
       </tbody>
     </table>

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
		<a href="<?php echo site_url('Logout');?>" class="btn btn-success btn-lg btn-block" style="color:#fff;">Logout</a>
	</div>
</div>
<!-- <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script> -->
<script src="https://code.jquery.com/jquery-3.5.1.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready( function () {
      $('#users-list').DataTable();
  } );
  function drop_domain(id){
	  if (!confirm("Are you sure delete this domain?"))
	  	return false;
	  $.ajax({
		type: "POST",
		url: "<?php echo site_url("/domains/drop_domain"); ?>", 
		data: {id: id},
		dataType: "text",  
		cache:false,
		success: 
			function(data){
				$("#"+id).remove();
			}
	});
  }
</script>
</body>
</html>

