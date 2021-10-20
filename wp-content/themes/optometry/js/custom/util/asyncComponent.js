import React, { Component } from "react";
import Nprogress from "nprogress";
import "nprogress/nprogress.css";
import ReactPlaceholder from 'react-placeholder';
import 'react-placeholder/lib/reactPlaceholder.css';

export default function asyncComponent( component ) {

	class AsyncComponentImporter extends Component {

		constructor( props ) {
			super( props );
			this.state = {
				component: null
			};
		}

		componentWillMount() {
			// Start progress bar?
			Nprogress.start();
		}

		async componentDidMount() {
			this.mounted = true;
			const {
				default: Component
			} = await component();
			Nprogress.done();

			if ( this.mounted ) {
				this.setState({
					component: <Component {...this.props} />
				});
			}
		}

		componentWillUnmount() {
			this.mounted = false;
		}


		render() {
			const Component = this.state.component || <div />;

			return (
				<ReactPlaceholder showLoadingAnimation type="media" rows={8}  ready={component !== null}>
					{Component}
				</ReactPlaceholder>
			);
		}
	}

	return AsyncComponentImporter;
}