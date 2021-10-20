<!-- Tabs -->
<h2 class="nav-tab-wrapper needs-js" data-panel="panel">
	<?php
	// Iterate through this screen's tabs
	foreach ( (array) $tabs as $tab_item ) {
		// Check if an icon needs to be displayed
		$icon = '';
		if ( ! empty( $tab_item['menu_icon'] ) ) {
			$icon = 'dashicons ' . $tab_item['menu_icon'];
		}
		?>
		<a href="#<?php echo $tab_item['name']; ?>" id="<?php echo $tab_item['name']; ?>" class="nav-tab<?php echo ( $tab_item['name'] == $tab['name'] ? ' nav-tab-active' : '' ); ?>"<?php echo ( isset( $tab_item['documentation'] ) ? ' data-documentation="' . $tab_item['documentation'] . '"' : '' ); ?>>
			<?php
			if ( ! empty( $icon ) ) {
				?>
				<span class="<?php echo $icon; ?>"></span>
				<?php
			}
			
			echo $tab_item['label'];
			?>
		</a>
		<?php
	}

	// Add a Documentation Tab
	?>
	<a href="https://wpsimpleseo.com/documentation/post-settings" class="nav-tab last documentation" target="_blank">
		<?php _e( 'Documentation', 'wp-simple-seo' ); ?>
		<span class="dashicons dashicons-admin-page"></span>
	</a>
</h2>

<!-- Meta -->
<div class="panel meta-panel">
	<div class="option">
		<div class="left">
			<strong><?php _e( 'Snippet Preview', 'wp-simple-seo' ); ?></strong>
			<span class="spinner" data-preview="spinner"></span>
		</div>
		<div class="right">
			<div class="wp-simple-seo-preview">
				<div class="title">
					<span data-preview="title">
						<?php 
						if ( strlen( $preview['title'] ) > $max_meta_title_length ) {
							echo substr( $preview['title'], 0, $max_meta_title_length ) . '...';
						} else {
							echo $preview['title']; 
						}
						?>
					</span>
					<span class="count<?php echo ( ( strlen( $preview['title'] ) > $max_meta_title_length ) ? ' alert' : '' ); ?>" data-tooltip="<?php echo sprintf( __( 'Only the first %s characters are displayed in search engine results.', 'wp-simple-seo' ), $max_meta_title_length ); ?>">
						<span class="number"><?php echo strlen( $preview['title'] ); ?></span>
						<?php _e( 'characters', 'wp-simple-seo' ); ?>
					</span>
				</div>
				<div class="url">
					<span data-preview="url"><?php echo $preview['url']; ?></span>
				</div>
				<div class="description">
					<span data-preview="description">
						<?php 
						if ( strlen( $preview['description'] ) > $max_meta_description_length ) {
							echo substr( $preview['description'], 0, $max_meta_description_length ) . '...';
						} else {
							echo $preview['description']; 
						}
						?>
					</span>
					<span class="count<?php echo ( ( strlen( $preview['description'] ) > $max_meta_description_length ) ? ' alert' : '' ); ?>" data-tooltip="<?php echo sprintf( __( 'Only the first %s characters are displayed in search engine results.', 'wp-simple-seo' ), $max_meta_description_length ); ?>">
						<span class="number"><?php echo strlen( $preview['description'] ); ?></span>
						<?php _e( 'characters', 'wp-simple-seo' ); ?>
					</span>
				</div>
			</div>

			<p class="description">
				<?php 
				echo sprintf( 
					__( 'This is how this %s will display in the search engine results.  You can change this for this individual %s by clicking the Edit button below, or <a href="%s" target="_blank">edit your site\'s meta settings</a> to change this for all %s', 'wp-simple-seo' ), 
					$post_type->labels->singular_name,
					$post_type->labels->singular_name,
					admin_url( 'admin.php?page=' . $this->base->plugin->name . '-meta#post_types' ),
					$post_type->labels->name
				); 
				?>
			</p>

			<a href="#" title="<?php _e( 'Edit', 'wp-simple-seo' ); ?>" class="button edit-meta">
				<?php _e( 'Edit', 'wp-simple-seo' ); ?>
			</a>
		</div>
	</div>

	<div class="meta-editor">
		<div class="option">
			<div class="left">
				<strong><?php _e( 'Title', 'wp-simple-seo' ); ?></strong>
			</div>

			<div class="right">
				<!-- Tags -->
		        <select size="1" class="wpcube-tags" data-element="#post_types_<?php echo $post_type->name; ?>_single_title">
		            <option value=""><?php _e( '--- Insert Tag ---', 'wp-simple-seo' ); ?></option>
		            <?php
		            foreach ( $tags as $tag_group => $tag_group_tags ) {
		                ?>
		                <optgroup label="<?php echo $tag_group; ?>">
		                    <?php
		                    foreach ( $tag_group_tags as $tag => $tag_label ) {
		                        ?>
		                        <option value="<?php echo $tag; ?>"><?php echo $tag_label; ?></option>
		                        <?php
		                    }
		                    ?>
		                </optgroup>
		                <?php
		            }
		            ?>
		        </select>

				<input type="text" id="post_types_<?php echo $post_type->name; ?>_single_title" name="<?php echo $this->base->plugin->name; ?>[post_types][<?php echo $post_type->name; ?>][single][title]" value="<?php echo $this->get_setting( 'meta', 'post_types[' . $post_type->name . '][single][title]', $post->ID ); ?>" class="widefat" data-preview="title" />
			</div>
		</div>

		<div class="option">
			<div class="left">
				<strong><?php _e( 'Description', 'wp-simple-seo' ); ?></strong>
			</div>

			<div class="right">
				<!-- Tags -->
		        <select size="1" class="wpcube-tags" data-element="#post_types_<?php echo $post_type->name; ?>_single_description">
		            <option value=""><?php _e( '--- Insert Tag ---', 'wp-simple-seo' ); ?></option>
		            <?php
		            foreach ( $tags as $tag_group => $tag_group_tags ) {
		                ?>
		                <optgroup label="<?php echo $tag_group; ?>">
		                    <?php
		                    foreach ( $tag_group_tags as $tag => $tag_label ) {
		                        ?>
		                        <option value="<?php echo $tag; ?>"><?php echo $tag_label; ?></option>
		                        <?php
		                    }
		                    ?>
		                </optgroup>
		                <?php
		            }
		            ?>
		        </select>

				<textarea id="post_types_<?php echo $post_type->name; ?>_single_description" name="<?php echo $this->base->plugin->name; ?>[post_types][<?php echo $post_type->name; ?>][single][description]" class="widefat" data-preview="description"><?php echo $this->get_setting( 'meta', 'post_types[' . $post_type->name . '][single][description]', $post->ID ); ?></textarea>
			</div>
		</div>
	</div>

	<div class="option">
		<label for="post_types_<?php echo $post_type->name; ?>_single_noindex">
			<div class="left">
				<strong><?php _e( 'Noindex?', 'wp-simple-seo' ); ?></strong>
			</div>
			<div class="right">
				<select id="post_types_<?php echo $post_type->name; ?>_single_noindex" name="<?php echo $this->base->plugin->name; ?>[post_types][<?php echo $post_type->name; ?>][single][noindex]" size="1">
					<option value="0"<?php selected( $this->get_setting( 'meta', 'post_types[' . $post_type->name . '][single][noindex]', $post->ID ), 0 ); ?>>
						<?php _e( 'Index Content', 'wp-simple-seo' ); ?>
					</option>
					<option value="1"<?php selected( $this->get_setting( 'meta', 'post_types[' . $post_type->name . '][single][noindex]', $post->ID ), 1 ); ?>>
						<?php _e( 'Don\'t Index Content', 'wp-simple-seo' ); ?>
					</option>
				</select>
				<p class="description">
					<?php 
					echo sprintf( __( 'Choose <b>Don\'t Index Content</b> if you do <b>not</b> want search engines to index this %s (this defines noindex as true).', 'wp-simple-seo' ), $post_type->labels->singular_name ); 

					// If Sitemaps are enabled, tell the user this content won't be included.
					if ( $sitemap_enabled ) {
						echo sprintf( __( ' This %s will <b>not</b> be included in the XML Sitemap.', 'wp-simple-seo' ), $post_type->labels->singular_name );
					}
					?>
				</p>
			</div>
		</label>
	</div>

	<div class="option">
		<label for="post_types_<?php echo $post_type->name; ?>_single_nofollow">
			<div class="left">
				<strong><?php _e( 'Nofollow?', 'wp-simple-seo' ); ?></strong>
			</div>
			<div class="right">
				<select id="post_types_<?php echo $post_type->name; ?>_single_nofollow" name="<?php echo $this->base->plugin->name; ?>[post_types][<?php echo $post_type->name; ?>][single][nofollow]" size="1">
					<option value="0"<?php selected( $this->get_setting( 'meta', 'post_types[' . $post_type->name . '][single][nofollow]', $post->ID  ), 0 ); ?>>
						<?php _e( 'Do Follow Links', 'wp-simple-seo' ); ?>
					</option>
					<option value="1"<?php selected( $this->get_setting( 'meta', 'post_types[' . $post_type->name . '][single][nofollow]', $post->ID  ), 1 ); ?>>
						<?php _e( 'Don\'t Follow Links (nofollow)', 'wp-simple-seo' ); ?>
					</option>
				</select>
				<p class="description">
					<?php 
					echo sprintf( __( 'Choose <b>Don\'t Follow Links</b> if you do <b>not</b> want search engines to pass on link equity / score through any links on  this %s (this defines nofollow as true).', 'wp-simple-seo' ), $post_type->labels->singular_name ); 
					?>
				</p>
			</div>
		</label>
	</div>

	<div class="option">
		<div class="left">
			<strong><?php _e( 'Canonical URL', 'wp-simple-seo' ); ?></strong>
		</div>

		<div class="right">
			<input type="text" id="post_types_<?php echo $post_type->name; ?>_single_canonical" name="<?php echo $this->base->plugin->name; ?>[post_types][<?php echo $post_type->name; ?>][single][canonical]" value="<?php echo $this->get_setting( 'meta', 'post_types[' . $post_type->name . '][single][canonical]', $post->ID ); ?>" class="widefat" />
		
			<p class="description">
				<?php _e( 'If there is an alternate URL that has the same / similar content, which you\'d prefer the search engines use for link signals and indexing, specify the URL here.  This prevents duplicate content penalties where you have multiple Posts / Pages with the same / similar content.', 'wp-simple-seo' ); ?>
			</p>
		</div>
	</div>
</div>

<?php 
do_action( 'wp_simple_seo_post_output_meta_box' );

// Load nonce field
wp_nonce_field( $this->base->plugin->name . '_post', $this->base->plugin->name . '_nonce' );		            	