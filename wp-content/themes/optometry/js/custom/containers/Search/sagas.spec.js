import test from "tape";

import { put, call } from 'redux-saga/effects';
import { delay } from 'redux-saga';

import { watchForSearch, watchForTopNav } from "./sagas";

test( 'Watch for search saga test', assert => {
    const gen = watchForSearch();

    assert.deepEqual(
       gen.next(), {
           done: false,
           value: {
               '@@redux-saga/IO': true,
               FORK: {
                   context: null
               }
           }
       });

    assert.end();

    process.exit(0);
});

