<tr class="form-field <?php echo $this->base->plugin->name; ?>">
	<th scope="row">
		<?php echo $this->base->plugin->displayName; ?>
	</th>
	<td>
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
			<a href="https://wpsimpleseo.com/documentation/term-settings" class="nav-tab last documentation" target="_blank">
				<?php _e( 'Documentation', 'wp-simple-seo' ); ?>
				<span class="dashicons dashicons-admin-page"></span>
			</a>
		</h2>

		<!-- Meta -->
		<div id="wp-simple-seo" class="panel meta-panel">
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
							$taxonomy->labels->singular_name,
							$taxonomy->labels->name,
							admin_url( 'admin.php?page=' . $this->base->plugin->name . '-meta#taxonomies' ),
							$taxonomy->labels->name
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
				        <select size="1" class="wpcube-tags" data-element="#taxonomies_<?php echo $taxonomy->name; ?>_title">
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

						<input type="text" id="taxonomies_<?php echo $taxonomy->name; ?>_title" name="<?php echo $this->base->plugin->name; ?>[taxonomies][<?php echo $taxonomy->name; ?>][title]" value="<?php echo $this->get_setting( 'meta', 'taxonomies[' . $taxonomy->name . '][title]', $term->term_id ); ?>" class="widefat" data-preview="title" />
					</div>
				</div>

				<div class="option">
					<div class="left">
						<strong><?php _e( 'Description', 'wp-simple-seo' ); ?></strong>
					</div>

					<div class="right">
						<!-- Tags -->
				        <select size="1" class="wpcube-tags" data-element="#taxonomies_<?php echo $taxonomy->name; ?>_description">
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

						<textarea id="taxonomies_<?php echo $taxonomy->name; ?>_description" name="<?php echo $this->base->plugin->name; ?>[taxonomies][<?php echo $taxonomy->name; ?>][description]" class="widefat" data-preview="description"><?php echo $this->get_setting( 'meta', 'taxonomies[' . $taxonomy->name . '][description]', $term->term_id ); ?></textarea>
					</div>
				</div>
			</div>

			<div class="option">
				<label for="taxonomies_<?php echo $taxonomy->name; ?>_noindex">
					<div class="left">
						<strong><?php _e( 'Noindex?', 'wp-simple-seo' ); ?></strong>
					</div>
					<div class="right">
						<select id="taxonomies_<?php echo $taxonomy->name; ?>_noindex" name="<?php echo $this->base->plugin->name; ?>[taxonomies][<?php echo $taxonomy->name; ?>][noindex]" size="1">
							<option value="0"<?php selected( $this->get_setting( 'meta', 'taxonomies[' . $taxonomy->name . '][noindex]', $term->term_id ), 0 ); ?>>
								<?php _e( 'Index Content', 'wp-simple-seo' ); ?>
							</option>
							<option value="1"<?php selected( $this->get_setting( 'meta', 'taxonomies[' . $taxonomy->name . '][noindex]', $term->term_id ), 1 ); ?>>
								<?php _e( 'Don\'t Index Content', 'wp-simple-seo' ); ?>
							</option>
						</select>
						<p class="description">
							<?php 
							echo sprintf( __( 'Choose <b>Don\'t Index Content</b> if you do <b>not</b> want search engines to index %s (this defines noindex as true).', 'wp-simple-seo' ), $taxonomy->labels->name ); 
							?>
						</p>
					</div>
				</label>
			</div>
		</div>

		<?php 
		do_action( 'wp_simple_seo_term_output_meta_box' );

		// Load nonce field
		wp_nonce_field( $this->base->plugin->name . '_term', $this->base->plugin->name . '_nonce' );
		?>
	</td>
</tr>