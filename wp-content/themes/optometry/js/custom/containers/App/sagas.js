/*global OPTO_REST_API */
import "whatwg-fetch";
import { delay } from "redux-saga";
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
import * as actionTypes from "../../actions/actionTypes";
import * as selectors from "../../selectors/index";

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

function request( url, options ) {
    return fetch( url )
        .then( checkStatus )
        .then( data => data.json() )
        .catch( error => error );
}

/**
 * Search request / response handler
 * @param term
 */
export function* searchSite( term ) {
    const restApiUrl = OPTO_REST_API.wp_rest_url;
    const apiUrl = `${restApiUrl}wp/v2`;
    const requestUrl = `${apiUrl}/pages?search=${term}`;

    const results = yield call( request, requestUrl );

    if ( !results.error ) {
        yield put( actions.searchSiteSuccess( results ) );
    } else {
        yield put( actions.searchSiteError( results.error ) );
    }
}

/**
 * Watches for SEARCH_SITE action and calls handler
 */
export function* searchSiteWatcher( action ) {
    // take action object { type: SEARCH_SITE, term: whatever }
    console.log(action, 'action');
    /*const { term } = yield take( actionTypes.SEARCH_SITE );
    yield call( searchSite, term );*/
    yield call( searchSite, action.term );

}

/**
 * Root saga manages watcher lifecycle
 */
export function* searchDataManager() {
    // Fork watcher so we can continue execution
    const watcher = yield fork( searchSiteWatcher );
    console.log(watcher, 'watcher');
    // Suspend execution until location changes
    //yield take( LOCATION_CHANGE );
    //yield cancel( watcher );
}

export function* helloSaga() {
    console.log( 'testing' );
}

/**
 * takeEvery is used to spawn a new task on each incoming action.
 */
export function* watchForSearch() {
    yield takeEvery( actionTypes.SEARCH_SITE, searchSiteWatcher );
}

export function* initCalls() {

}

/**
 * Bootstrap saga watcher
 */
function* root() {
    yield fork( watchForSearch );
    /*yield all([
        helloSaga(),
        watchForSearch()
    ])*/
}

/**
 * e.g. on take -
 * suspend the Generator until a matching action is dispatched.
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