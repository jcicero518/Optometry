<?php

class Main_Nav_Walker extends Walker_Nav_Menu {

	protected $ID;
	protected $depth;
	protected $classes = array();
	protected $child_count = 0;
	protected $have_current = false;

	/**
	 * Starts the list before the elements are added.
	 *
	 * @param string $output
	 * @param int $depth
	 * @param array $args
	 */
	public function start_lvl(&$output, $depth = 1, $args = array()) {
		//var_dump(func_get_args());
		if ( 0 == $depth || $this->depth != $depth ) {
			return;
		}

		parent::start_lvl($output, $depth, $args);
	}

	/**
	 * Ends the list of after the elements are added.
	 *
	 * @param string $output
	 * @param int $depth
	 * @param array $args
	 */
	public function end_lvl(&$output, $depth = 1, $args = array()) {
		$output .= "</ul>";
		if ( 0 == $depth || $this->depth != $depth ) {
			return;
		}

		parent::end_lvl($output, $depth, $args);
	}

	/**
	 * Starts the element output.
	 *
	 * @param string $output
	 * @param WP_Post $item
	 * @param int $depth
	 * @param array $args
	 * @param int $id
	 */
	public function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {

		$is_current = in_array( 'current-menu_item', $this->classes );

		if ( 0 == $depth ) {
			return;
		}
		//var_dump(func_get_args());
		parent::start_el($output, $item, $depth, $args, $id);
	}

	/**
	 * Ends the element output, if needed.
	 *
	 * @param string $output
	 * @param WP_Post $item
	 * @param int $depth
	 * @param array $args
	 */
	public function end_el(&$output, $item, $depth = 0, $args = array()) {

		if ( 0 == $depth ) {
			return;
		}
		//var_dump(func_get_args());
		parent::end_el($output, $item, $depth, $args);
	}

	public function display_element($element, &$children_elements, $max_depth, $depth, $args, &$output) {

		if ( ! $element ) {

		}

		$id_field = $this->db_fields['id'];
		$id       = $element->$id_field;

		//display this element
		$this->has_children = ! empty( $children_elements[ $id ] );
		if ( isset( $args[0] ) && is_array( $args[0] ) ) {
			$args[0]['has_children'] = $this->has_children; // Back-compat.
		}

		$cb_args = array_merge( array(&$output, $element, $depth), $args);
		call_user_func_array(array($this, 'start_el'), $cb_args);

		if ( ( $depth >= 1) && !isset( $children_elements[$id] ) ) {
			//var_dump($element);
		}

		// descend only when the depth is right and there are childrens for this element
		if ( ($max_depth == 0 || $max_depth > $depth+1 ) && isset( $children_elements[$id]) ) {

			foreach ( $children_elements[ $id ] as $child ){

				if ( !isset($newlevel) ) {
					$newlevel = true;
					//start the child delimiter
					$cb_args = array_merge( array(&$output, $depth), $args);
					call_user_func_array(array($this, 'start_lvl'), $cb_args);
				}
				$this->display_element( $child, $children_elements, $max_depth, $depth + 1, $args, $output );
			}
			unset( $children_elements[ $id ] );
		}

		if ( isset($newlevel) && $newlevel ){
			//end the child delimiter
			$cb_args = array_merge( array(&$output, $depth), $args);
			call_user_func_array(array($this, 'end_lvl'), $cb_args);
		}

		//end this element
		$cb_args = array_merge( array(&$output, $element, $depth), $args);
		call_user_func_array(array($this, 'end_el'), $cb_args);
	}

	/*public function display_element($element, &$children_elements, $max_depth, $depth, $args, &$output) {

		// Check if element is in the current tree to display
		$current_element_markers = array( 'current-menu-item', 'current-menu-parent', 'current-menu-ancestor' );
		$this->classes = array_intersect( $current_element_markers, $element->classes );

		// If element has a 'current' class, it is an ancestor of the current element
		$ancestor_of_current = !empty($this->classes);

		// check if the element is the actual page element we are on.
		$is_current = in_array('current-menu-item', $this->classes);

		// if it is the current element
		if($is_current) {

			// set the count / ID / and depth to use in the other functions.
			$this->child_count = ( isset($children_elements[$element->ID]) ) ? count($children_elements[$element->ID]) : 0;
			$this->ID = $element->ID;
			$this->depth = $depth;
			$this->have_current = true;

			if($this->child_count > 0) {

				// if there are children loop through them and display the kids.
				foreach( $children_elements[$element->ID] as $child ) {
					parent::display_element( $child, $children_elements, $max_depth, $depth, $args, $output );
				}

			} else {
				// no children so loop through kids of parent item.
				foreach( $children_elements[$element->menu_item_parent] as $child ) {
					parent::display_element( $child, $children_elements, $max_depth, $depth, $args, $output );
				}

			}
		}

		// if depth is zero and not in current tree go to the next element
		if ( 0 == $depth && !$ancestor_of_current)
			return;

		// if we aren't on the current element proceed as normal
		if(! $this->have_current )
			parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );

		//return parent::display_element($element, $children_elements, $max_depth, $depth, $args, $output);
	}*/

}