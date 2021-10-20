<?php
$sitemap_enabled = $this->get_setting( 'sitemap', 'general[enabled]' );
?>

<!-- General -->
<div class="panel general-panel">
    <div class="postbox">
        <h3 class="hndle"><?php _e( 'General', 'wp-simple-seo' ); ?></h3>

        <div class="option">
			<p class="description">
				<?php _e( 'Defines the Title Separator Symbol, and some site wide metadata for the search engines', 'wp-simple-seo' ); ?>
			</p>
		</div>

		<div class="option">
			<div class="left">
				<strong><?php _e( 'Title Separator Symbol', 'wp-simple-seo' ); ?></strong>
			</div>
			<div class="right">
				<input type="text" id="title_seperator" name="general[title_separator]" value="<?php echo $this->get_setting( 'meta', 'general[title_separator]' ); ?>" class="widefat" />
				<p class="description">
					<?php _e( 'The title separator is used when the {title_separator} tag is used - for example, you might use it between your Post\'s title and Site title.', 'wp-simple-seo' ); ?>
				</p>
			</div>
		</div>

		<div class="option">
			<div class="left">
				<strong><?php _e( 'Pagination Separator Symbol', 'wp-simple-seo' ); ?></strong>
			</div>
			<div class="right">
				<input type="text" id="pagination_separator" name="general[pagination_separator]" value="<?php echo $this->get_setting( 'meta', 'general[pagination_separator]' ); ?>" class="widefat" />
				<p class="description">
					<?php _e( 'The pagination separator is used when the {pagination_page_total} tag is used.  For example, if set to a forwardslash, the output would be <b>1 / 2</b>', 'wp-simple-seo' ); ?>
				</p>
			</div>
		</div>

		<div class="option">
			<label for="general_noodp">
				<div class="left">
					<strong><?php _e( 'No ODP?', 'wp-simple-seo' ); ?></strong>
				</div>
				<div class="right">
					<select id="general_noodp" name="general[noodp]" size="1">
						<option value="0"<?php selected( $this->get_setting( 'meta', 'general[noodp]' ), 0 ); ?>>
							<?php _e( 'Allow ODP/DMOZ\'s description of your site being used by Search Engines', 'wp-simple-seo' ); ?>
						</option>
						<option value="1"<?php selected( $this->get_setting( 'meta', 'general[noodp]' ), 1 ); ?>>
							<?php _e( 'Prevent ODP/DMOZ\'s description of your site being used by Search Engines (noodp)', 'wp-simple-seo' ); ?>
						</option>
					</select>
					<p class="description">
						<?php 
						_e( 'Choose <b>Prevent ODP/DMOZ\'s description of your site being used by Search Engines</b> if you do <b>not</b> want search engines to use the ODP\DMOZ description in search engine results (this defines noodp as true)', 'wp-simple-seo' ); 
						?>
					</p>
				</div>
			</label>
		</div>

		<div class="option">
			<label for="general_noydir">
				<div class="left">
					<strong><?php _e( 'No Ydir?', 'wp-simple-seo' ); ?></strong>
				</div>
				<div class="right">
					<select id="general_noydir" name="general[noydir]" size="1">
						<option value="0"<?php selected( $this->get_setting( 'meta', 'general[noydir]' ), 0 ); ?>>
							<?php _e( 'Allow Yahoo\'s description of your site being used by Search Engines', 'wp-simple-seo' ); ?>
						</option>
						<option value="1"<?php selected( $this->get_setting( 'meta', 'general[noydir]' ), 1 ); ?>>
							<?php _e( 'Prevent Yahoo\'s description of your site being used by Search Engines (noydir)', 'wp-simple-seo' ); ?>
						</option>
					</select>
					<p class="description">
						<?php 
						_e( 'Choose <b>Prevent Yahoo\'s description of your site being used by Search Engines</b> if you do <b>not</b> want search engines to use the Yahoo description in search engine results (this defines noydir as true)', 'wp-simple-seo' ); 
						?>
					</p>
				</div>
			</label>
		</div>
    </div>
</div>

<!-- Home -->
<div class="panel home-panel">
	<?php
	// Get tags for the Home Page
	$tags = WP_Simple_SEO_Tags::get_instance()->get_home_tags();
	?>
    <div class="postbox">
        <h3 class="hndle"><?php _e( 'Home Page', 'wp-simple-seo' ); ?></h3>

        <div class="option">
			<p class="description">
				<?php _e( 'Defines the SEO metadata when viewing the Home Page', 'wp-simple-seo' ); ?>
			</p>
		</div>

		<div class="option">
			<div class="left">
				<strong><?php _e( 'Title', 'wp-simple-seo' ); ?></strong>
			</div>
			<div class="right">
				<!-- Tags -->
	            <select size="1" class="wpcube-tags" data-element="#home_title">
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

				<input type="text" id="home_title" name="home[title]" value="<?php echo $this->get_setting( 'meta', 'home[title]' ); ?>" class="widefat" />
			</div>
		</div>

		<div class="option">
			<div class="left">
				<strong><?php _e( 'Description', 'wp-simple-seo' ); ?></strong>
			</div>
			<div class="right">
				<!-- Tags -->
	            <select size="1" class="wpcube-tags" data-element="#home_description">
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

				<textarea id="home_description" name="home[description]" class="widefat"><?php echo $this->get_setting( 'meta', 'home[description]' ); ?></textarea>
			</div>
		</div>

		<div class="option">
			<label for="home_noindex">
				<div class="left">
					<strong><?php _e( 'Noindex?', 'wp-simple-seo' ); ?></strong>
				</div>
				<div class="right">
					<select id="home_noindex" name="home[noindex]" size="1">
						<option value="0"<?php selected( $this->get_setting( 'meta', 'home[noindex]' ), 0 ); ?>>
							<?php _e( 'Index Content', 'wp-simple-seo' ); ?>
						</option>
						<option value="1"<?php selected( $this->get_setting( 'meta', 'home[noindex]' ), 1 ); ?>>
							<?php _e( 'Don\'t Index Content', 'wp-simple-seo' ); ?>
						</option>
					</select>
					<p class="description">
						<?php 
						_e( 'Choose <b>Don\'t Index Content</b> if you do <b>not</b> want search engines to index the Home Page (this defines noindex as true)', 'wp-simple-seo' ); 

						// If Sitemaps are enabled, tell the user this content won't be included.
						if ( $sitemap_enabled ) {
							_e( ' The Home Page will <b>not</b> be included in the XML Sitemap.', 'wp-simple-seo' );
						}
						?>
					</p>
				</div>
			</label>
		</div>
    </div>
</div>

<!-- Post Types -->
<div class="panel post_types-panel">
	<?php
	// Get post types
	$post_types = WP_Simple_SEO_Common::get_instance()->get_post_types();
	foreach ( (array) $post_types as $post_type ) {

		// Single Post Type
		?>
		<h3><?php echo $post_type->labels->name; ?></h3>

		<div class="postbox">
			<h3 class="hndle"><?php echo sprintf( __( 'Single %s', 'wp-simple-seo' ), $post_type->labels->singular_name ); ?></h3>

			<div class="option">
				<p class="description">
					<?php echo sprintf( __( 'Defines the SEO metadata when viewing a single / individual %s', 'wp-simple-seo' ), $post_type->labels->singular_name ); ?>
				</p>
			</div>

			<?php
			// Get tags for Single Posts
			$tags = WP_Simple_SEO_Tags::get_instance()->get_post_tags( $post_type->name );
			?>
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

					<input type="text" id="post_types_<?php echo $post_type->name; ?>_single_title" name="post_types[<?php echo $post_type->name; ?>][single][title]" value="<?php echo $this->get_setting( 'meta', 'post_types[' . $post_type->name . '][single][title]' ); ?>" class="widefat" />
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

					<textarea id="post_types_<?php echo $post_type->name; ?>_single_description" name="post_types[<?php echo $post_type->name; ?>][single][description]" class="widefat"><?php echo $this->get_setting( 'meta', 'post_types[' . $post_type->name . '][single][description]' ); ?></textarea>
				</div>
			</div>

			<div class="option">
				<label for="post_types_<?php echo $post_type->name; ?>_single_noindex">
					<div class="left">
						<strong><?php _e( 'Noindex?', 'wp-simple-seo' ); ?></strong>
					</div>
					<div class="right">
						<select id="post_types_<?php echo $post_type->name; ?>_single_noindex" name="post_types[<?php echo $post_type->name; ?>][single][noindex]" size="1">
							<option value="0"<?php selected( $this->get_setting( 'meta', 'post_types[' . $post_type->name . '][single][noindex]' ), 0 ); ?>>
								<?php _e( 'Index Content', 'wp-simple-seo' ); ?>
							</option>
							<option value="1"<?php selected( $this->get_setting( 'meta', 'post_types[' . $post_type->name . '][single][noindex]' ), 1 ); ?>>
								<?php _e( 'Don\'t Index Content (noindex)', 'wp-simple-seo' ); ?>
							</option>
						</select>
						<p class="description">
							<?php 
							echo sprintf( __( 'Choose <b>Don\'t Index Content</b> if you do <b>not</b> want search engines to index %s (this defines noindex as true).', 'wp-simple-seo' ), $post_type->labels->name ); 

							// If Sitemaps are enabled, tell the user this content won't be included.
							if ( $sitemap_enabled ) {
								echo sprintf( __( ' <b>No</b> %s will be included in the XML Sitemap.', 'wp-simple-seo' ), $post_type->labels->name );
							}
							?>
						</p>
					</div>
				</label>
			</div>

			<div class="option">
				<label for="post_types_<?php echo $post_type->name; ?>_single_noindex">
					<div class="left">
						<strong><?php _e( 'Nofollow?', 'wp-simple-seo' ); ?></strong>
					</div>
					<div class="right">
						<select id="post_types_<?php echo $post_type->name; ?>_single_nofollow" name="post_types[<?php echo $post_type->name; ?>][single][nofollow]" size="1">
							<option value="0"<?php selected( $this->get_setting( 'meta', 'post_types[' . $post_type->name . '][single][nofollow]' ), 0 ); ?>>
								<?php _e( 'Do Follow Links', 'wp-simple-seo' ); ?>
							</option>
							<option value="1"<?php selected( $this->get_setting( 'meta', 'post_types[' . $post_type->name . '][single][nofollow]' ), 1 ); ?>>
								<?php _e( 'Don\'t Follow Links (nofollow)', 'wp-simple-seo' ); ?>
							</option>
						</select>
						<p class="description">
							<?php 
							echo sprintf( __( 'Choose <b>Don\'t Follow Links</b> if you do <b>not</b> want search engines to pass on link equity / score through any links on  %s (this defines nofollow as true).', 'wp-simple-seo' ), $post_type->labels->name ); 
							?>
						</p>
					</div>
				</label>
			</div>

			<div class="option">
				<label for="post_types_<?php echo $post_type->name; ?>_single_noimageindex">
					<div class="left">
						<strong><?php _e( 'No Image Index?', 'wp-simple-seo' ); ?></strong>
					</div>
					<div class="right">
						<select id="post_types_<?php echo $post_type->name; ?>_single_noimageindex" name="post_types[<?php echo $post_type->name; ?>][single][noimageindex]" size="1">
							<option value="0"<?php selected( $this->get_setting( 'meta', 'post_types[' . $post_type->name . '][single][noimageindex]' ), 0 ); ?>>
								<?php echo sprintf( __( 'Index Images on %s in Google Image Search', 'wp-simple-seo' ), $post_type->labels->name ); ?>
							</option>
							<option value="1"<?php selected( $this->get_setting( 'meta', 'post_types[' . $post_type->name . '][single][noimageindex]' ), 1 ); ?>>
								<?php echo sprintf( __( 'Don\'t index Images on %s in Google Image Search (noimageindex)', 'wp-simple-seo' ), $post_type->labels->name ); ?>
							</option>
						</select>
						<p class="description">
							<?php 
							echo sprintf( __( 'Choose <b>Don\'t index Images on %s in Google Image Search</b> if you do <b>not</b> want search engines to index images in Google Image Search results (this defines noimageindex as true).', 'wp-simple-seo' ), $post_type->labels->name, $post_type->labels->name ); 
							?>
						</p>
					</div>
				</label>
			</div>

			<div class="option">
				<label for="post_types_<?php echo $post_type->name; ?>_single_meta_box">
					<div class="left">
						<strong><?php _e( 'Show Meta Box?', 'wp-simple-seo' ); ?></strong>
					</div>
					<div class="right">
						<select id="post_types_<?php echo $post_type->name; ?>_single_meta_box" name="post_types[<?php echo $post_type->name; ?>][single][meta_box]" size="1">
							<option value="1"<?php selected( $this->get_setting( 'meta', 'post_types[' . $post_type->name . '][single][meta_box]' ), 1 ); ?>>
								<?php _e( 'Yes', 'wp-simple-seo' ); ?>
							</option>
							<option value="0"<?php selected( $this->get_setting( 'meta', 'post_types[' . $post_type->name . '][single][meta_box]' ), 0 ); ?>>
								<?php _e( 'No', 'wp-simple-seo' ); ?>
							</option>
						</select>
						<p class="description">
							<?php 
							echo sprintf( __( '<b>Yes</b> will display the above options when editing %s. This allows you to override settings on a per-%s basis.', 'wp-simple-seo' ), $post_type->labels->name, $post_type->labels->singular_name ); 
							?>
						</p>
					</div>
				</label>
			</div>
		</div>

		
		<?php
		// Archive Post Type
		// If the Post Type is Post, and a static posts page has been set on this WordPress installation, display the Archive settings here
		// If the Post Type is NOT a Post, and has an archive, display the Archive settings here
		if ( WP_Simple_SEO_Settings::get_instance()->post_type_has_archive( $post_type ) ) {
			// Get tags for Post Archives
			$tags = WP_Simple_SEO_Tags::get_instance()->get_post_archive_tags( $post_type );
			?>
			<!-- Archive -->
			<div class="postbox">
				<h3 class="hndle"><?php echo sprintf( __( '%s Archives', 'wp-simple-seo' ), $post_type->labels->singular_name ); ?></h3>

				<div class="option">
					<p class="description">
						<?php echo sprintf( __( 'Defines the SEO metadata when viewing the %s Archives', 'wp-simple-seo' ), $post_type->labels->singular_name ); ?>
					</p>
				</div>

				<div class="option">
					<div class="left">
						<strong><?php _e( 'Title', 'wp-simple-seo' ); ?></strong>
					</div>
					<div class="right">
						<!-- Tags -->
			            <select size="1" class="wpcube-tags" data-element="#post_types_<?php echo $post_type->name; ?>_archive_title">
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

						<input type="text" id="post_types_<?php echo $post_type->name; ?>_archive_title" name="post_types[<?php echo $post_type->name; ?>][archive][title]" value="<?php echo $this->get_setting( 'meta', 'post_types[' . $post_type->name . '][archive][title]' ); ?>" class="widefat" />
					</div>
				</div>

				<div class="option">
					<div class="left">
						<strong><?php _e( 'Description', 'wp-simple-seo' ); ?></strong>
					</div>
					<div class="right">
						<!-- Tags -->
			            <select size="1" class="wpcube-tags" data-element="#post_types_<?php echo $post_type->name; ?>_archive_description">
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

						<textarea id="post_types_<?php echo $post_type->name; ?>_archive_description" name="post_types[<?php echo $post_type->name; ?>][archive][description]" class="widefat"><?php echo $this->get_setting( 'meta', 'post_types[' . $post_type->name . '][archive][description]' ); ?></textarea>
					</div>
				</div>

				<div class="option">
					<label for="post_types_<?php echo $post_type->name; ?>_archive_noindex">
						<div class="left">
							<strong><?php _e( 'Noindex?', 'wp-simple-seo' ); ?></strong>
						</div>
						<div class="right">
							<select id="post_types_<?php echo $post_type->name; ?>_archive_noindex" name="post_types[<?php echo $post_type->name; ?>][archive][noindex]" size="1">
								<option value="0"<?php selected( $this->get_setting( 'meta', 'post_types[' . $post_type->name . '][archive][noindex]' ), 0 ); ?>>
									<?php _e( 'Index Content', 'wp-simple-seo' ); ?>
								</option>
								<option value="1"<?php selected( $this->get_setting( 'meta', 'post_types[' . $post_type->name . '][archive][noindex]' ), 1 ); ?>>
									<?php _e( 'Don\'t Index Content', 'wp-simple-seo' ); ?>
								</option>
							</select>

							<p class="description">
								<?php 
								echo sprintf( __( 'Choose <b>Don\'t Index Content</b> if you do <b>not</b> want search engines to index %s Archives (this defines noindex as true).', 'wp-simple-seo' ), $post_type->labels->name ); 

								// If Sitemaps are enabled, tell the user this content won't be included.
								if ( $sitemap_enabled ) {
									echo sprintf( __( ' This also excludes %s Archive pages from the XML Sitemap.<br />Individual %s <b>may</b> be included in the XML Sitemap, unless the Single %s noindex option has been checked.', 'wp-simple-seo' ), $post_type->labels->singular_name, $post_type->labels->name, $post_type->labels->singular_name );
								}
								?>
							</p>
						</div>
					</label>
				</div>
			</div>
			<?php
		}
		?>

		<p><br /><hr /><br /></p>
		<?php
	}
	?>
</div>

<!-- Taxonomies -->
<div class="panel taxonomies-panel">
	<?php
	$taxonomies = WP_Simple_SEO_Common::get_instance()->get_taxonomies();
	foreach ( (array) $taxonomies as $taxonomy ) {

		// Get tags for this Taxonomy
		$tags = WP_Simple_SEO_Tags::get_instance()->get_taxonomy_tags( $taxonomy );
		?>
		<div class="postbox">
			<h3 class="hndle"><?php echo $taxonomy->labels->name; ?></h3>
			<div class="option">
				<p class="description">
					<?php echo sprintf( __( 'Defines the SEO metadata when viewing a %s taxonomy term', 'wp-simple-seo' ), $taxonomy->labels->name ); ?>
				</p>
			</div>

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

					<input type="text" id="taxonomies_<?php echo $taxonomy->name; ?>_title" name="taxonomies[<?php echo $taxonomy->name; ?>][title]" value="<?php echo $this->get_setting( 'meta', 'taxonomies[' . $taxonomy->name . '][title]' ); ?>" class="widefat" />
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

					<textarea id="taxonomies_<?php echo $taxonomy->name; ?>_description" name="taxonomies[<?php echo $taxonomy->name; ?>][description]" class="widefat"><?php echo $this->get_setting( 'meta', 'taxonomies[' . $taxonomy->name . '][description]' ); ?></textarea>
				</div>
			</div>

			<div class="option">
				<label for="taxonomies_<?php echo $taxonomy->name; ?>_noindex">
					<div class="left">
						<strong><?php _e( 'Noindex?', 'wp-simple-seo' ); ?></strong>
					</div>
					<div class="right">
						<select id="taxonomies<?php echo $taxonomy->name; ?>_noindex" name="taxonomies[<?php echo $taxonomy->name; ?>][noindex]" size="1">
							<option value="0"<?php selected( $this->get_setting( 'meta', 'taxonomies[' . $taxonomy->name . '][noindex]' ), 0 ); ?>>
								<?php _e( 'Index Content', 'wp-simple-seo' ); ?>
							</option>
							<option value="1"<?php selected( $this->get_setting( 'meta', 'taxonomies[' . $taxonomy->name . '][noindex]' ), 1 ); ?>>
								<?php _e( 'Don\'t Index Content', 'wp-simple-seo' ); ?>
							</option>
						</select>
						<p class="description">
							<?php 
							echo sprintf( __( 'Choose <b>Don\'t Index Content</b> if you do <b>not</b> want to index the %s Archive (this defines noindex as true)', 'wp-simple-seo' ), $taxonomy->labels->name ); 
							?>
						</p>
					</div>
				</label>
			</div>

			<div class="option">
				<label for="taxonomies_<?php echo $taxonomy->name; ?>_meta_box">
					<div class="left">
						<strong><?php _e( 'Show Meta Box?', 'wp-simple-seo' ); ?></strong>
					</div>
					<div class="right">
						<select id="taxonomies_<?php echo $taxonomy->name; ?>_meta_box" name="taxonomies[<?php echo $taxonomy->name; ?>][meta_box]" size="1">
							<option value="1"<?php selected( $this->get_setting( 'meta', 'taxonomies[' . $taxonomy->name . '][meta_box]' ), 1 ); ?>>
								<?php _e( 'Yes', 'wp-simple-seo' ); ?>
							</option>
							<option value="0"<?php selected( $this->get_setting( 'meta', 'taxonomies[' . $taxonomy->name . '][meta_box]' ), 0 ); ?>>
								<?php _e( 'No', 'wp-simple-seo' ); ?>
							</option>
						</select>

						<p class="description">
							<?php 
							echo sprintf( __( '<b>Yes</b> will display the Title and Description editor when editing %s. This allows you to override settings on a per-%s basis.', 'wp-simple-seo' ), $taxonomy->labels->name, $taxonomy->labels->singular_name ); 
							?>
						</p>
					</div>
				</label>
			</div>
		</div>
		<?php
	}
	?>
</div>

<!-- Archives -->
<div class="panel archives-panel">
	<?php
    // Get tags for Authors
	$tags = WP_Simple_SEO_Tags::get_instance()->get_author_tags();
	?>
	<!-- Authors -->
    <div class="postbox">
        <h3 class="hndle"><?php _e( 'Authors', 'wp-simple-seo' ); ?></h3>

        <div class="option">
			<p class="description">
				<?php _e( 'Defines the SEO metadata when viewing an Author\'s archive.', 'wp-simple-seo' ); ?>
			</p>
		</div>

		<div class="option">
			<div class="left">
				<strong><?php _e( 'Title', 'wp-simple-seo' ); ?></strong>
			</div>
			<div class="right">
				<!-- Tags -->
	            <select size="1" class="wpcube-tags" data-element="#archives_authors_title">
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

				<input type="text" id="archives_authors_title" name="archives[author][title]" value="<?php echo $this->get_setting( 'meta', 'archives[author][title]' ); ?>" class="widefat" />
			</div>
		</div>

		<div class="option">
			<div class="left">
				<strong><?php _e( 'Description', 'wp-simple-seo' ); ?></strong>
			</div>

			<div class="right">
				<!-- Tags -->
	            <select size="1" class="wpcube-tags" data-element="#archives_authors_description">
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

				<textarea id="archives_authors_description" name="archives[author][description]" class="widefat"><?php echo $this->get_setting( 'meta', 'archives[author][description]' ); ?></textarea>
			</div>
		</div>

		<?php
		$has_multiple_authors = WP_Simple_SEO_Settings::get_instance()->site_has_multiple_authors();
		?>
		<div class="option">
			<label for="archives_authors_noindex">
				<div class="left">
					<strong><?php _e( 'Noindex?', 'wp-simple-seo' ); ?></strong>
				</div>
				<div class="right">
					<select id="archives_authors_noindex" name="archives[author][noindex]" size="1"<?php echo ( ! $has_multiple_authors ? ' disabled="disabled"' : '' ); ?>>
						<?php
						// If this is a single Author site, we always noindex
						// Explain to the user why this option isn't available to configure
					    if ( ! $has_multiple_authors ) {
					    	?>
					    	<option value="1" selected>
								<?php _e( 'Don\'t Index Content', 'wp-simple-seo' ); ?>
							</option>
							<?php
						} else {
							?>
							<option value="0"<?php selected( $this->get_setting( 'meta', 'archives[author][noindex]' ), 0 ); ?>>
								<?php _e( 'Index Content', 'wp-simple-seo' ); ?>
							</option>
							<option value="1"<?php selected( $this->get_setting( 'meta', 'archives[author][noindex]' ), 1 ); ?>>
								<?php _e( 'Don\'t Index Content', 'wp-simple-seo' ); ?>
							</option>
							<?php
						}
					    ?>
					</select>
					<p class="description">
						<?php 
						if ( ! $has_multiple_authors ) {
							_e( 'Because your site only has one author (WordPress User) with published Posts, we won\'t index your author archives.', 'wp-simple-seo' ); ?>
					    	<br />
					    	<?php _e( 'This prevents search engines from potentially seeing duplicate content, and therefore negatively impacting on your search engine rankings.', 'wp-simple-seo' ); ?>
					    	<br />
					    	<?php _e( 'Once your site has two or more authors with published Posts, you can choose to index your author archives.', 'wp-simple-seo' );
						} else {
							_e( 'Choose <b>Don\'t Index Content</b> if you do <b>not</b> want search engines to index Author archives (this defines noindex as true).', 'wp-simple-seo' ); 

							// If Sitemaps are enabled, tell the user this content won't be included.
							if ( $sitemap_enabled ) {
								echo sprintf( __( ' <b>No</b> %s will be included in the XML Sitemap.', 'wp-simple-seo' ), $post_type->labels->name );
							}
						}
						?>
					</p>
				</div>
			</label>
		</div>
    </div>

    <!-- Dates -->
    <?php
	// Get tags for Dates
	$tags = WP_Simple_SEO_Tags::get_instance()->get_date_tags();
	?>
    <div class="postbox">
        <h3 class="hndle"><?php _e( 'Dates', 'wp-simple-seo' ); ?></h3>

        <div class="option">
			<p class="description">
				<?php _e( 'Defines the SEO metadata when viewing any date-based archive.', 'wp-simple-seo' ); ?>
			</p>
		</div>

		<div class="option">
			<div class="left">
				<strong><?php _e( 'Title', 'wp-simple-seo' ); ?></strong>
			</div>
			<div class="right">
				<!-- Tags -->
	            <select size="1" class="wpcube-tags" data-element="#archives_dates_title">
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

				<input type="text" id="archives_dates_title" name="archives[date][title]" value="<?php echo $this->get_setting( 'meta', 'archives[date][title]' ); ?>" class="widefat" />
			</div>
		</div>

		<div class="option">
			<div class="left">
				<strong><?php _e( 'Description', 'wp-simple-seo' ); ?></strong>
			</div>

			<div class="right">
				<!-- Tags -->
	            <select size="1" class="wpcube-tags" data-element="#archives_dates_description">
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

				<textarea id="archives_dates_description" name="archives[date][description]" class="widefat"><?php echo $this->get_setting( 'meta', 'archives[date][description]' ); ?></textarea>
			</div>
		</div>

		<div class="option">
			<label for="archives_dates_noindex">
				<div class="left">
					<strong><?php _e( 'Noindex?', 'wp-simple-seo' ); ?></strong>
				</div>
				<div class="right">
					<select id="archives_dates_noindex" name="archives[date][noindex]" size="1"<?php echo ( ! $has_multiple_authors ? ' disabled="disabled"' : '' ); ?>>
						<?php
						// If this is a single Author site, we always noindex
						// Explain to the user why this option isn't available to configure
					    if ( ! $has_multiple_authors ) {
					    	?>
					    	<option value="1" selected>
								<?php _e( 'Don\'t Index Content', 'wp-simple-seo' ); ?>
							</option>
							<?php
						} else {
							?>
							<option value="0"<?php selected( $this->get_setting( 'meta', 'archives[date][noindex]' ), 0 ); ?>>
								<?php _e( 'Index Content', 'wp-simple-seo' ); ?>
							</option>
							<option value="1"<?php selected( $this->get_setting( 'meta', 'archives[date][noindex]' ), 1 ); ?>>
								<?php _e( 'Don\'t Index Content', 'wp-simple-seo' ); ?>
							</option>
							<?php
						}
						?>
					</select>
					<p class="description">
						<?php 
						if ( ! $has_multiple_authors ) {
							_e( 'Because your site only has one author (WordPress User) with published Posts, we won\'t index your date-based archives.', 'wp-simple-seo' ); ?>
					    	<br />
					    	<?php _e( 'This prevents search engines from potentially seeing duplicate content, and therefore negatively impacting on your search engine rankings.', 'wp-simple-seo' ); ?>
					    	<br />
					    	<?php _e( 'Once your site has two or more authors with published Posts, you can choose whether to index your date-based archives.', 'wp-simple-seo' );
						} else {
							_e( 'Choose <b>Don\'t Index Content</b> if you do <b>not</b> want search engines to index date-based archives (this defines noindex as true).', 'wp-simple-seo' ); 

							// If Sitemaps are enabled, tell the user this content won't be included.
							if ( $sitemap_enabled ) {
								echo sprintf( __( ' <b>No</b> %s will be included in the XML Sitemap.', 'wp-simple-seo' ), $post_type->labels->name );
							}
						}
						?>
					</p>
				</div>
			</label>
		</div>
    </div>
 
</div>

<!-- Search -->
<div class="panel search-panel">
	<?php
	// Get tags for Search
	$tags = WP_Simple_SEO_Tags::get_instance()->get_search_tags();
	?>
	<div class="postbox">
		<h3 class="hndle"><?php _e( 'Search Results', 'wp-simple-seo' ); ?></h3>
		<div class="option">
			<p class="description">
				<?php _e( 'Defines the SEO metadata when viewing a search results screen', 'wp-simple-seo' ); ?>
			</p>
		</div>

		<div class="option">
			<div class="left">
				<strong><?php _e( 'Title', 'wp-simple-seo' ); ?></strong>
			</div>
			<div class="right">
				<!-- Tags -->
	            <select size="1" class="wpcube-tags" data-element="#search_title">
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

				<input type="text" id="search_title" name="search[title]" value="<?php echo $this->get_setting( 'meta', 'search[title]' ); ?>" class="widefat" />
			</div>
		</div>

		<div class="option">
			<div class="left">
				<strong><?php _e( 'Description', 'wp-simple-seo' ); ?></strong>
			</div>
			<div class="right">
				<!-- Tags -->
	            <select size="1" class="wpcube-tags" data-element="#search_description">
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

				<textarea id="search_description" name="search[description]" class="widefat"><?php echo $this->get_setting( 'meta', 'search[description]' ); ?></textarea>
			</div>
		</div>

		<div class="option">
			<label for="search_noindex">
				<div class="left">
					<strong><?php _e( 'Noindex?', 'wp-simple-seo' ); ?></strong>
				</div>
				<div class="right">
					<select id="search_noindex" name="search[noindex]" size="1">
						<option value="0"<?php selected( $this->get_setting( 'meta', 'search[noindex]' ), 0 ); ?>>
							<?php _e( 'Index Content', 'wp-simple-seo' ); ?>
						</option>
						<option value="1"<?php selected( $this->get_setting( 'meta', 'search[noindex]' ), 1 ); ?>>
							<?php _e( 'Don\'t Index Content', 'wp-simple-seo' ); ?>
						</option>
					</select>
					<p class="description">
						<?php 
						_e( 'Choose <b>Don\'t Index Content</b> if you do <b>not</b> want search engines to index internal search results on your web site (this defines noindex as true).', 'wp-simple-seo' ); 
						?>
					</p>
				</div>
			</label>
		</div>
	</div>
</div>

<!-- 404 -->
<div class="panel 404-panel">
	<?php
	// Get tags for 404
	$tags = WP_Simple_SEO_Tags::get_instance()->get_404_tags();
	?>

	<div class="postbox">
		<h3 class="hndle"><?php _e( '404', 'wp-simple-seo' ); ?></h3>
		<div class="option">
			<p class="description">
				<?php _e( 'Defines the SEO metadata when viewing a 404 not found screen', 'wp-simple-seo' ); ?>
			</p>
		</div>

		<div class="option">
			<div class="left">
				<strong><?php _e( 'Title', 'wp-simple-seo' ); ?></strong>
			</div>
			<div class="right">
				<!-- Tags -->
	            <select size="1" class="wpcube-tags" data-element="#title_four04">
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

				<input type="text" id="title_four04" name="four04[title]" value="<?php echo $this->get_setting( 'meta', 'four04[title]' ); ?>" class="widefat" />
			</div>
		</div>
	</div>
</div>

<!-- Save -->
<div class="submit">
    <input type="submit" name="submit" value="<?php _e( 'Save', 'wp-simple-seo' ); ?>" class="button button-primary" />
</div>