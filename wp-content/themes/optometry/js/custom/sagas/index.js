import { LOCATION_CHANGE } from 'react-router-redux';
import { take, takeEvery, takeLatest } from "redux-saga/effects";

const handleLocationChange = function* handleLocationChange( params ) {

	yield takeEvery( LOCATION_CHANGE, action => console.log( params, 'location change') );
};

export default handleLocationChange;