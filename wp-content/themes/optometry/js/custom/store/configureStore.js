import { fromJS, Iterable } from "immutable";
import { createStore, applyMiddleware, compose } from "redux";
import createReducer from "../reducers";
import createSagaMiddleware from "redux-saga";

/**
 * Middleware imports
 */
import { createLogger } from "redux-logger";
import { routerMiddleware, routerReducer } from "react-router-redux";
import thunk from "redux-thunk";

/**
 * Saga middleware
 * @type {SagaMiddleware<object>}
 */
const sagaMiddleware = createSagaMiddleware();

/**
 * Logger options
 * @type {{duration: boolean}}
 */
const loggerOptionsPrimitive = {
	duration: true
};
const { duration } = loggerOptionsPrimitive;

const stateTransformer = state => {
	if ( Iterable.isIterable( state ) ) {
		return state.toJS();
	}
	return state;
};

export default function configureStore( initialState = {}, history ) {

	const logger = createLogger({
		duration,
		stateTransformer
	});

	// Create the store with two middlewares
	// 1. thunks
	// 2. routerMiddleware: Syncs the location/URL path to the state
	const middlewares = [
		thunk,
		sagaMiddleware,
		routerMiddleware(history),
	];

	const enhancers = [
		applyMiddleware(...middlewares),
	];

	// If Redux DevTools Extension is installed use it, otherwise use Redux compose
	/* eslint-disable no-underscore-dangle */
	const composeEnhancers =
		process.env.NODE_ENV !== 'production' &&
		typeof window === 'object' &&
		window.__REDUX_DEVTOOLS_EXTENSION_COMPOSE__
			? window.__REDUX_DEVTOOLS_EXTENSION_COMPOSE__({
				// TODO Try to remove when `react-router-redux` is out of beta, LOCATION_CHANGE should not be fired more than once after hot reloading
				// Prevent recomputing reducers for `replaceReducer`
				shouldHotReload: false,
			})
			: compose;
	/* eslint-enable */

	const store = createStore(
		createReducer(),
		fromJS( initialState ),
		composeEnhancers(...enhancers)
	);

	//sagaMiddleware.run( root );

	// Extensions
	store.runSaga = sagaMiddleware.run;
	store.asyncReducers = {}; // Async reducer registry
	store.asyncSagas = {};

	// dispatch thunk for top navigation
	//store.dispatch( actions.receivedNavMenuRequest( 'main-menu' ) );
	// dispatch for saga
	//store.dispatch( setNavMenu( 'main-menu' ) );

	if ( module.hot ) {
		module.hot.accept( ['../reducers/index'], () => {
			store.replaceReducer( require("../reducers/index").default );
			//store.replaceReducer( createStore( store.injectedReducers ) );
		});
	}

	return store;
}