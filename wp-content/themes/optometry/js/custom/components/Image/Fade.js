import React, { Component } from "react";
import PropTypes from "prop-types";
import { Transition, TransitionGroup, CSSTransitionGroup} from "react-transition-group";
import LazyLoad from "react-lazyload";

import LoadingIndicator from "../LoadingIndicator";

class Fade extends Component {

	constructor(props) {
		super(props);
		this.state = {
			loaded: false
		};

		this.onLoad = this.onLoad.bind( this );
	}

	onLoad() {
		this.setState({
			loaded: true
		});
	}

	render() {
		const { height, children } = this.props,
			{ loaded } = this.state;


		return (
			<LazyLoad
				throttle={200}
				height={height}
				placeholder={<LoadingIndicator/>}
			>
				{children}
			</LazyLoad>
		);
	}

}

export default Fade;