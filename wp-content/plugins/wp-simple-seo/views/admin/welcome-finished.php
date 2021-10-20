<div class="wrap about-wrap">
    <h1>
    	<?php _e( 'Welcome to WP Simple SEO', 'wp-simple-seo' ); ?>
    </h1>
		
	<div class="about-text">
		<?php _e( 'WP Simple SEO is ready to use!', 'wp-simple-seo' ); ?>
	</div>

	<p>
		<?php _e( 'Should you need to change any settings, they\'re divided into four main sections.', 'wp-simple-seo' ); ?>
	</p>

	<h2 class="nav-tab-wrapper needs-js" data-panel="panel">
		<a href="#general" id="general" class="nav-tab nav-tab-active">
			<?php _e( 'General', 'wp-simple-seo' ); ?>
		</a>
		<a href="#meta" id="meta" class="nav-tab">
			<?php _e( 'Meta', 'wp-simple-seo' ); ?>
		</a>
		<a href="#social" id="social" class="nav-tab">
			<?php _e( 'Social', 'wp-simple-seo' ); ?>
		</a>
		<a href="#sitemap" id="sitemap" class="nav-tab">
			<?php _e( 'Sitemap', 'wp-simple-seo' ); ?>
		</a>	
	</h2>

	<!-- General -->
	<div class="panel general-panel">
		<h3><?php _e( 'General', 'wp-simple-seo' ); ?></h3>
		<div class="feature-section two-col">
			<div class="col">
				<p>
					<?php _e( 'The General section allows you to verify ownership of your web site with various search engines, including Google and Bing.', 'wp-simple-seo' ); ?>
				</p>
				<p>
					<a href="admin.php?page=<?php echo $this->base->plugin->name; ?>" class="button button-primary">
						<?php _e( 'Go to General Settings', 'wp-simple-seo' ); ?>
					</a>
				</p>
			</div>
		</div>
	</div>

	<!-- Meta -->
	<div class="panel meta-panel">
		<h3><?php _e( 'Meta', 'wp-simple-seo' ); ?></h3>
		<div class="feature-section two-col">
			<div class="col">
				<p>
					<?php _e( 'The Meta section defines the output format for the browser title and meta description, depending on which part of the web site a visitor or search engine is viewing.', 'wp-simple-seo' ); ?>
				</p>
				<p>
					<a href="admin.php?page=<?php echo $this->base->plugin->name; ?>" class="button button-primary">
						<?php _e( 'Go to Meta Settings', 'wp-simple-seo' ); ?>
					</a>
				</p>
			</div>
		</div>
	</div>

	<!-- Social -->
	<div class="panel social-panel">
		<h3><?php _e( 'Social', 'wp-simple-seo' ); ?></h3>
		<div class="feature-section two-col">
			<div class="col">
				<p>
					<?php _e( 'By default, social metadata is enabled, which determines how shared content on social networks is displayed, when somebody shares your site\'s address.  You can change some default settings here.', 'wp-simple-seo' ); ?>
				</p>
				<p>
					<a href="admin.php?page=<?php echo $this->base->plugin->name; ?>" class="button button-primary">
						<?php _e( 'Go to Social Settings', 'wp-simple-seo' ); ?>
					</a>
				</p>
			</div>
		</div>
	</div>
	
	<!-- Sitemap -->
	<div class="panel sitemap-panel">
		<h3><?php _e( 'Sitemap', 'wp-simple-seo' ); ?></h3>
		<div class="feature-section two-col">
			<div class="col">
				<p>
					<?php _e( 'By default, your XML sitemap is enabled.  This can be submitted to the search engines (hopefully you did this in the previous step!), to tell them precisely which URLs to add to their index.', 'wp-simple-seo' ); ?>
				</p>
				<p>
					<a href="admin.php?page=<?php echo $this->base->plugin->name; ?>" class="button button-primary">
						<?php _e( 'Go to Sitemap Settings', 'wp-simple-seo' ); ?>
					</a>
				</p>
			</div>
		</div>
	</div>
</div>