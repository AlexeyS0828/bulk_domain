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
                    <th>Fail</th>
                    <th>Failed Domains</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo $data['count']; ?></td>
                    <td><?php echo $data['success']; ?></td>
                    <td><?php echo $data['fail']; ?></td>
                    <td><?php 
                        $i = 0;
                        foreach($data['fails'] as $domain){
                            echo "<b>".(++$i).":</b> &nbsp;&nbsp;&nbsp;" . $domain . "<br>";
                        }
                    ?></td>
                </tr>
            </tbody>
        </table>
  </div>
</div>
<div class="form-group" style="display: flex;">
	<div class="form-group-item" style="width: 70%;">
		&nbsp;
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
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
</body>
</html>