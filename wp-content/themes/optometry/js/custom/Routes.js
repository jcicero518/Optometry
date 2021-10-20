import React from "react";
import {
	Switch,
	Route,
	withRouter
} from "react-router-dom";
import {TransitionGroup, CSSTransitionGroup} from "react-transition-group";

import Home from "./containers/Home/index";
import Page from "./containers/Page/index";

const Routes = ( { location } ) => (
	<CSSTransitionGroup key={location.key}
	                    transitionName="fade"
	                    transitionAppear={false}
	                    transitionAppearTimeout={500}
	                    transitionEnterTimeout={500}
	                    transitionLeaveTimeout={1000}>
		<Switch location={location}>
			<Route exact path="/" component={Home} />
			<Route path="/search/:term" component={Page} />
			<Route path="/:parent/:page" component={Page} />
			<Route path="/:parent" component={Page} />
		</Switch>
	</CSSTransitionGroup>
);

export default withRouter( Routes );