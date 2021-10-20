import React from "react";
import {
	Switch,
	Route
} from "react-router-dom";

//import Home from "../../containers/Home/Loadable";
//import Page from "../../containers/Page/Loadable";
//import Search from "../../containers/Search/Loadable";
import Home from "../../containers/Home";
import Page from "../../containers/Page";
import Search from "../../containers/Search";

export default function App( {store} ) {

	return (
			<Switch>
				<Route exact path="/" component={Home} />
				<Route path="/search/:term/:page" component={Search} />
				<Route path="/search/:term" component={Search} />
				<Route path="/:parent/:page/:tertiary" component={Page} />
				<Route path="/:parent/:page" component={Page} />
				<Route path="/:parent" component={Page} />
			</Switch>
	);
}