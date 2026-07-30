<!--site header-->
<header id="site-header" class="col-xs-12" role="banner">
	<div class="row">
		<div class="col-xs-12 col-lg-9 col-lg-push-3">
			<nav class="navbar navbar-default">
				<div class="container-fluid">
					<!-- Brand and toggle get grouped for better mobile display -->
					<div class="navbar-header">
						<button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
							data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
							<span class="sr-only">Toggle navigation</span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
						<a class="navbar-brand" href="<?php echo "{$ROOT}/index.php"; ?>"><img
								src="<?php echo IMG_DIR; ?>/sun-icon.png" border=0 /></a>
					</div>

					<!-- Collect the nav links, forms, and other content for toggling -->
					<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
						<ul class="nav navbar-nav">
							<li <?php if ($sActiveMenuItem == HOME_ACTIVE) {
								echo "class=\"active\"";
							} ?>><a
									href="<?php echo "{$ROOT}/index.php"; ?>">Home</a></li>
							<li <?php if ($sActiveMenuItem == SCHEDULE_ACTIVE) {
								echo "class=\"active\"";
							} ?>><a
									href="<?php echo "{$ROOT}/schedule.php"; ?>">Schedule</a></li>
							<li class="dropdown<?php if ($sActiveMenuItem == MUSIC_ACTIVE) {
								echo " active";
							} ?>">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
									aria-haspopup="true" aria-expanded="false">Music <span class="caret"></span></a>
								<ul class="dropdown-menu">
									<li><a href="http://www.cdbaby.com/all/sweeks" target="_blank">Purchase</a></li>
									<li><a href="<?php echo "{$ROOT}/performance.php"; ?>">Performance</a></li>
									<li><a href="<?php echo "{$ROOT}/music.php"; ?>">CDs & Singles</a></li>
									<li><a href="<?php echo "{$ROOT}/lyrics.php"; ?>">Lyrics</a></li>
								</ul>
							</li>
							<li <?php if ($sActiveMenuItem == CONTACT_ACTIVE) {
								echo "class=\"active\"";
							} ?>><a
									href="<?php echo "{$ROOT}/contact.php"; ?>">Contact</a></li>
							<li <?php if ($sActiveMenuItem == ABOUT_ACTIVE) {
								echo "class=\"active\"";
							} ?>><a
									href="<?php echo "{$ROOT}/about.php"; ?>">About</a></li>
						</ul>
						<form class="navbar-form navbar-left" role="search" method="get"
							action="<?php echo "{$ROOT}/search.php" ?>">
							<div class="form-group">
								<input name="search-text" id="search-text" type="text" class="form-control"
									placeholder="Search">
							</div>
							<button type="submit" class="btn btn-default">Submit</button>
						</form>
					</div><!-- /.navbar-collapse -->
				</div><!-- /.container-fluid -->
			</nav>
		</div>
		<div id="page-title-container" class="col-xs-12">
			<a href="<?php echo "{$ROOT}/index.php"; ?>">
				<div class="header-logo-container">
					<div id="header-logo"></div>
				</div>
			</a>
			<?php
			if (empty($sPageTitle)) {
				$sPageTitle = DEFAULT_PAGE_TITLE;
			}
			echo "<div class=\"page-title avoid-theme-sm\">{$sPageTitle}</div>";
			?>
			<div class="visible-lg">
				<?php
				include(INCLUDE_DIR . "/socialMedia.php");
				?>
			</div>
		</div>

	</div>
	<div class="row">
		<div class="col-xs-12 hidden-xs avoid-theme">
			<?php
			if (!empty($aBreadcrumb)) {
				$lastElement = end($aBreadcrumb);
				echo "<ol class=\"breadcrumb\">";

				foreach ($aBreadcrumb as $aBreadcrumbItem) {
					if (is_array($aBreadcrumbItem) && sizeof($aBreadcrumbItem) > 0) {
						if ($aBreadcrumbItem === $lastElement) {
							echo "<li>{$aBreadcrumbItem[0]}</li>";
						} else {
							echo "<li class=\"active\"><a href=\"{$ROOT}/{$aBreadcrumbItem[1]}\">{$aBreadcrumbItem[0]}</a></li>";
						}
					}
				}

				echo "</ol>";
			}
			?>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12 avoid-theme">
			<?php
			if (!empty($sPageSubTitle)) {
				echo "<div class=\"page-sub-title\">{$sPageSubTitle}</div>";
			}
			?>
		</div>
	</div>
</header>
<!--END site header-->