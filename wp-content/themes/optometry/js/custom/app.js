import "babel-polyfill";
//import "core-js/es6";
import "../../sass/style.scss";

import React from "react";
import ReactDOM from "react-dom";
import { Provider } from "react-redux";
import { ConnectedRouter } from "react-router-redux";
import { setNavMenu } from "./actions/globalActions";

import createHistory from "history/createBrowserHistory";
import configureStore from "./store/configureStore";

import App from "./containers/App/App";

import root from "./containers/Search/sagas";

// Create redux store with history
// this uses the singleton browserHistory provided by react-router
// Optionally, this could be changed to leverage a created history
// e.g. `const browserHistory = useRouterHistory(createBrowserHistory)();`
const initialState = {};
const history = createHistory();
const store = configureStore( initialState, history );

// Start up sagas
store.runSaga( root );

// dispatch actions needed immediately
store.dispatch( setNavMenu( 'main-menu' ) );
//store.runSaga( 'interface', 'saga', 'args?')

const render = () => (
	ReactDOM.render(
		<Provider store={store}>
			<ConnectedRouter history={history}>
				<App store={store} history={history} />
			</ConnectedRouter>
		</Provider>,
		document.getElementById( 'root' )
	)
);

render();



