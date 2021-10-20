import React from "react";
import { hot } from "react-hot-loader";
import {
	BrowserRouter as Router,
	Switch,
	Route,
	NavLink,
	Link,
	withRouter
}  from "react-router-dom";

import About from "./About";
import MatchRegex from "./MatchRegex";

const Main = ( {props} ) => {
	console.log(props, 'props main');
	return (
		<Router>
		<div>
			<h2>Main</h2>
			<Route path="/:id" component={About} />

			{/*
         It's possible to use regular expressions to control what param values should be matched.
            * "/order/asc"  - matched
            * "/order/desc" - matched
            * "/order/foo"  - not matched
      */}
			<Route
				path="/order/:direction(asc|desc)"
				component={MatchRegex}
			/>
		</div>
		</Router>
	)
};

export default hot( module )( Main );