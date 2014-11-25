<!DOCTYPE html>
<html lang="en">
	<head>	
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title><?php echo ucfirst($this->router->class) . ' | ';?> EXIMPORT</title>
		<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
        <link rel="stylesheet" href="<?php echo base_url('assets/css/uikit.min.css');?>">
		<link rel="stylesheet" href="<?php echo base_url('assets/css/uikit.gradient.min.css');?>">

        <link rel="stylesheet" href="<?php echo base_url('assets/kendo/css/kendo.common.min.css');?>">
        <link rel="stylesheet" href="<?php echo base_url('assets/kendo/css/kendo.default.min.css');?>">

		<script src="<?php echo base_url('assets/js/jquery-1.9.1.min.js');?>"></script>
		<script src="<?php echo base_url('assets/js/uikit.min.js');?>"></script>
		<script src="<?php echo base_url('assets/vendor/highlight/highlight.js');?>"></script>
        <script src="<?php echo base_url('assets/kendo/js/kendo.web.min.js');?>"></script>
		<script src="<?php echo base_url('assets/js/date.format.js');?>"></script>
	</head>
	<body>
		<div class="uk-container uk-container-center">
			<div class="uk-grid">
				<div class="uk-width-1-1">
					<div>
                       <img style="width:300px" src="<?php echo base_url('assets/images/logo.jpg');?>" />
					</div>

					<nav class="uk-navbar">
						<!--a class="uk-navbar-brand uk-hidden-small uk-active" href="#">Home</a-->
						<ul class="uk-navbar-nav uk-hidden-small">
							<!--li><a href="<?php echo base_url();?>">Home</a></li-->
							<li><a href="<?php echo base_url('entry');?>">Job Order Entry</a></li>
							<li><a href="<?php echo base_url('distribution');?>">Job Distribution</a></li>
                            <li><a href="<?php echo base_url('maintenance');?>">Maintenance</a></li>
                            <li><a href="<?php echo base_url('reports');?>">Reports</a></li>
                            <li><a href="<?php echo base_url('invoice');?>">Invoice</a></li>
                            <li><a href="<?php echo base_url('security');?>">Security</a></li>

                            <?php if($this->ion_auth->logged_in()): ?>
                            <li><a href="<?php echo base_url("auth/logout");?>">Logout</a></li>
                            <?php endif; ?>
						</ul>

						<a href="#" class="uk-navbar-toggle uk-visible-small" data-uk-offcanvas="{target:'#offcanvas-3'}"></a>

					</nav>
				</div>
				<div id="offcanvas-3" class="uk-offcanvas">

						<div class="uk-offcanvas-bar">

							<ul class="uk-nav uk-nav-offcanvas uk-nav-parent-icon" data-uk-nav>
								<li><a href="#">Item</a></li>
								<li class="uk-active"><a href="#">Active</a></li>

								<li class="uk-parent">
									<a href="#">Parent</a>
									<ul class="uk-nav-sub">
										<li><a href="#">Sub item</a></li>
										<li><a href="#">Sub item</a>
											<ul>
												<li><a href="#">Sub item</a></li>
												<li><a href="#">Sub item</a></li>
											</ul>
										</li>
									</ul>
								</li>

								<li class="uk-parent">
									<a href="#">Parent</a>
									<ul class="uk-nav-sub">
										<li><a href="#">Sub item</a></li>
										<li><a href="#">Sub item</a></li>
									</ul>
								</li>

								<li><a href="#">Item</a></li>

								<li class="uk-nav-header">Header</li>
								<li><a href="#"><i class="uk-icon-star"></i> Item</a></li>
								<li><a href="#"><i class="uk-icon-twitter"></i> Item</a></li>
								<li class="uk-nav-divider"></li>
								<li><a href="#"><i class="uk-icon-rss"></i> Item</a></li>
							</ul>

							<form class="uk-search">
								<input class="uk-search-field" type="search" placeholder="search...">
								<button class="uk-search-close" type="reset"></button>
							</form>

							<div class="uk-panel">
								<h3 class="uk-panel-title">Title</h3>
								Lorem ipsum dolor sit amet, <a href="#">consetetur</a> sadipscing elitr.
							</div>

							<div class="uk-panel">
								<h3 class="uk-panel-title">Title</h3>
								Lorem ipsum dolor sit amet, <a href="#">consetetur</a> sadipscing elitr.
							</div>

						</div>

					</div>
			</div>

		