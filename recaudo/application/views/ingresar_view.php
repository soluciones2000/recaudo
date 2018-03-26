<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
	<div class="py-5">
	    <div class="container">
	      	<div class="row d-flex justify-content-center">
	        	<div class="col-md-6">
		        	<div class="row">
		        		<div class="col-md-12"><h1 style="text-align: center;">Login</h1></div>
		        	</div>
		        	<div class="row">
		        		<div class="col-md-12">
		        			<?php if($this->session->flashdata("mensaje")){
				      		 echo $this->session->flashdata("mensaje");
				      		 } ?>
		        		</div>
		        	</div>
	        		<form class="form-horizontal" role="form" action="<?php echo base_url('Login/ingresar') ?>" method="post">
		          		<div class="row">
		            		<div class="col-md-6">
		              			<p class="lead">Email</p>
		            		</div>
		            		<div class="col-md-6">
		              			<input type="email" class="form-control" name="email" id="email" placeholder="Email">
		            		</div>
		          		</div>
		          		<div class="row">
		            		<div class="col-md-6">
		              			<p class="lead">Password</p>
		            		</div>
		            		<div class="col-md-6">
		              			<input type="password" class="form-control" name="password" id="password" placeholder="Password">
		          			</div>
		          		</div>
			          	<div class="row my-2">
			          		<div class="col-md-6 d-flex justify-content-center">
			              		<button type="submit" class="btn btn-primary text-white btn-lg">Login</button>
			            	</div>
			            	<div class="col-md-6 d-flex justify-content-center">
			              		<button type="reset" class="btn btn-default text-white btn-lg">Clear</button>
			            	</div>
			          	</div>
	      			</form>
	        	</div>
	      	</div>
	    </div>
	</div>
