<!-- DataTables -->
<link rel="stylesheet" href="<?php echo base_url().ASSETS;?>plugins/datatables/dataTables.bootstrap.css">
 <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Users
        <small>Usr List</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Users</a></li>
        <li class="active">User List</li>
      </ol>
    </section>
 
 <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
				<?php $message = $this->session->flashdata('success');
					if(!empty($message)):?><div class="alert alert-success">
						<?php echo $message;?></div><?php endif; ?>
				<?php $error = $this->session->flashdata('error');
					if(!empty($error)):?><div class="alert alert-danger">
				<?php echo $error;?></div><?php endif; ?>
				<div id="message"></div>
				<a href="javascript:void(0)"  onclick="open_modal('users')" class="btn btn-primary">
					<?php echo lang('add_user');?>
				<i class="fa fa-plus"></i>
				</a>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Serial No</th>
                  <th>Name</th>
                  <th>Email</th>
                </tr>
                </thead>
                <tbody>
				<?php
					if (isset($list) && !empty($list)):
						$rowCount = 0;
						foreach ($list as $rows):
							$rowCount++;
				?>
                <tr>
					<td><?php echo $rowCount; ?></td>            
					<td><?php echo $rows->full_name?></td>
					<td><?php echo $rows->email?></td>
                </tr>
				<?php endforeach; endif;?>
                </tbody>
                <tfoot>
                <tr>
                  <th>Serial No</th>
                  <th>Name</th>
                  <th>Email</th>
                </tr>
                </tfoot>
              </table>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
		  
		  <div id="form-modal-box"></div>
		  
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <!-- DataTables -->
<script src="<?php echo base_url().ASSETS;?>plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url().ASSETS;?>plugins/datatables/dataTables.bootstrap.min.js"></script>
  <script>
  $(function () {
    $("#example1").DataTable();
    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false
    });
  });
</script>