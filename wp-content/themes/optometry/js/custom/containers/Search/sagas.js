/*global OPTO_REST_API */
import "whatwg-fetch";

import {
	all,
	take,
	takeLatest,
	takeEvery,
	call,
	put,
	select,
	fork,
	cancel
} from "redux-saga/effects";

import { LOCATION_CHANGE } from "react-router-redux";
import * as actions from "../../actions/globalActions";
import {
	setFrontPageId,
	setHomeData,
	setHomeDataSuccess,
	setHomeDataError
} from "../Home/actions";
import * as actionTypes from "../../actions/actionTypes";
import * as selectors from "../../selectors/index";

const cacheLocal = false;

/**
 * WP Response Headers Pagination
 * X-WP-Total: the total number of records in the collection
 * X-WP-TotalPages: the total number of pages encompassing all available records
 */
/**
 * Checks if a network request came back fine, and throws an error if not
 *
 * @param  {object} response   A response from a network request
 *
 * @return {object|undefined} Returns either the response, or throws an error
 */
function checkStatus( response ) {
	if (response.status >= 200 && response.status < 300) {
		return response;
	}

	const error = new Error(response.statusText);
	error.response = response;
	throw error;
}

function getHeaders( response ) {
	if ( response.headers ) {
		return {
			total: response.headers.get( 'X-WP-Total' ),
			totalPages: parseInt( response.headers.get( 'X-WP-TotalPages' ) )
		};
	}
}

function requestHeaders( url ) {
	return fetch( url )
		.then( getHeaders )
		.then( headers => headers )
		.catch( error => error );
}

function request( url, options ) {
	if ( options ) {
		return fetch( url )
			.then( response => {
				return Promise.all([
					{
						totalPages: response.headers.get( 'X-WP-TotalPages' ) ? parseInt( response.headers.get( 'X-WP-TotalPages' ) ) : 0,
						totalResults: response.headers.get( 'X-WP-Total' ) ? parseInt( response.headers.get( 'X-WP-Total' ) ) : 0
					},
					response.json()
				]);
			})
			.then( data => data );
	} else {
		return fetch( url )
			.then( checkStatus )
			.then( data => data.json() )
			.catch( error => error );
	}

}

function checkLocal( requestURL ) {
	return localStorage.getItem( requestURL );
}

function getLocal( requestURL, responseData ) {
	let localData = localStorage.getItem( requestURL );
	if ( !localData ) {
		localStorage.setItem( requestURL, JSON.stringify( responseData ) );
	}

	return localData ? JSON.parse( localData ) : false;
}

/**
 * Search request / response handler
 * @param term
 */
export function* searchSite( term, currPage, numPages ) {
	const restApiUrl = OPTO_REST_API.wp_rest_url;
	const apiUrl = `${restApiUrl}wp/v2`;
	const requestUrl = `${apiUrl}/pages?search=${term}&page=${currPage}&per_page=5`;

	const resultFetch = yield call( request, requestUrl, true );
	const headers = resultFetch[0];
	const results = resultFetch[1];

	if ( !results.error ) {
		yield put( actions.searchSiteSuccess( results, headers ) );
	} else {
		yield put( actions.searchSiteError( results.error ) );
	}
}

export function* getTopNavMenu( menu ) {
	const appUrl = `${location.protocol}//${location.host}`;
	const menuApiUrl = `${appUrl}/wp-json/wp-menus/v1/menus/`;
	const requestUrl = `${menuApiUrl}${menu}`;
	let results, localData;

	localData = checkLocal( requestUrl );
	if ( !localData ) {
		results = yield call( request, requestUrl );
	} else {
		results = cacheLocal ? JSON.parse( checkLocal( requestUrl ) ) : yield call( request, requestUrl );
	}

	if ( !results.error ) {
		getLocal( requestUrl, results );
		yield put( actions.setNavMenuSuccess( results ) );
	}
}

function* getHomeData( postId ) {
    const restApiUrl = OPTO_REST_API.wp_rest_url;
    const apiUrl = `${restApiUrl}wp/v2`;
    const requestUrl = `${apiUrl}/pages/${postId}`;

	let results, localData;

	localData = checkLocal( requestUrl );
	if ( !localData ) {
		results = yield call( request, requestUrl );
	} else {
		results = cacheLocal ? JSON.parse( checkLocal( requestUrl ) ) : yield call( request, requestUrl );
	}

    if ( !results.error ) {
	    getLocal( requestUrl, results );
    	yield put( setHomeDataSuccess( results ) );
	} else {
    	yield put( setHomeDataError( results.error ) );
	}
}

/**
 * Watches for SET_NAV_MENU action and calls handler
 */
export function* topNavMenuWatcher( action ) {
	const getState = yield select();
	const globalState = getState.get( 'global' ).toJS();
	yield call( getTopNavMenu, action.menu );
}

/**
 * Watches for SEARCH_SITE action and calls handler
 */
export function* searchSiteWatcher( action ) {
	// take action object { type: SEARCH_SITE, term: whatever }
	//yield call( searchSiteHeaders, action.term, action.currPage, action.numPages );
	yield call( searchSite, action.term, action.currPage, action.numPages );
}

export function* watchForTopNav() {
	const takeTop = yield takeLatest( actionTypes.SET_NAV_MENU, topNavMenuWatcher );
	//yield takeLatest( actionTypes.SET_NAV_MENU, topNavMenuWatcher );
}

/**
 * takeEvery is used to spawn a new task on each incoming action.
 */
export function* watchForSearch() {
	yield takeEvery( actionTypes.SEARCH_SITE, searchSiteWatcher );
}

export function* frontPageWatcher( action ) {
	yield call( getHomeData, action.postId );
}

export function* watchForFront() {
	yield takeLatest( actionTypes.SET_HOME_DATA, frontPageWatcher );
}

export function* getChildrenItems( postId ) {
	const restApiUrl = OPTO_REST_API.wp_rest_url;
	const apiUrl = `${restApiUrl}optometry/v1`;
	const requestUrl = `${apiUrl}/children?pageId=${postId}`;

	let results, localData;

	localData = checkLocal( requestUrl );
	if ( !localData ) {
		results = yield call( request, requestUrl );
	} else {
		results = cacheLocal ? JSON.parse( checkLocal( requestUrl ) ) : yield call( request, requestUrl );
	}

	if ( ! results.error ) {
		getLocal( requestUrl, results );
		yield put( actions.setPageChildrenSuccess( results ) );
	} else {
		console.warn( results.error );
	}
}

export function* getChildrenWatcher( action ) {
	yield call( getChildrenItems, action.postId );
}

export function* watchForGetChildren() {
	yield takeLatest( actionTypes.SET_PAGE_CHILDREN, getChildrenWatcher );
}

export function* watchLocation() {
	yield takeLatest( LOCATION_CHANGE, function* ( action ) {
        const restApiUrl = OPTO_REST_API.wp_rest_url;
        const apiUrl = `${restApiUrl}wp/v2`;
		const { pathname } = action.payload;
		const requestUrl = `${apiUrl}/pages?slug=${pathname}`;

		const results = yield call( request, requestUrl );
		console.log(results, 'results');
        if ( !results.error ) {
            yield put( actions.setPageSuccess( results ) );
        } else {
        	yield put( actions.setPageError( results.error ) );
		}

	});
}

/**
 * Bootstrap saga watcher
 */
function* root() {
	yield all([
		//fork( watchLocation ),
		//fork( watchForPage ),
		fork( watchForTopNav ),
		fork( watchForSearch ),
		fork( watchForFront ),
		fork( watchForGetChildren )
	]);
}

/**
 * e.g. on take -
 * suspend the Generator until a matching action is dispatched.
 *
 * take Effects are resolved by waiting for actions to be dispatched to the Store
 * put Effects are resolved by dispatching the actions provided as argument.
 * take / put like I/O
 *
 *  It knows that the LOGIN action should always be followed by a LOGOUT action,
 *  and that LOGOUT is always followed by a LOGIN (a good UI should always
 *  enforce a consistent order of the actions, by hiding or disabling unexpected actions).
 *
 *  The loginFlow implements its entire flow inside a while (true) loop,
 *  which means once we reach the last step in the flow (LOGOUT) we start a new iteration
 */
function* loginFlow() {
    while (true) {
        yield take('LOGIN'); // Waiting for a LOGIN action - next()
        // ... perform the login logic
        yield take('LOGOUT'); // then waiting for LOGOUT action
        // ... perform the logout logic
    }
}

export default root;