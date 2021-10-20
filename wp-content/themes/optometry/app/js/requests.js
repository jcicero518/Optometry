"use strict";

const API = 'https://api.github.com/users/';
const USER = 'jcicero518';

let request = () => {
  return new Promise( (resolve, reject) => {
      let xhr = new XMLHttpRequest();
      xhr.open('GET', API + USER);

      xhr.onload = () => {
        if (xhr.status === 200 && xhr.readyState === XMLHttpRequest.DONE) {
            console.log(xhr, 'xhr');
            resolve(xhr.responseText);
        } else {
            reject(xhr.responseText);
        }
      };

      xhr.onerror = () => reject(xhr.statusText);

      //xhr.setRequestHeader('Content-Type', 'application/json');

      xhr.send(null);
  });
};

class Requests {

    constructor(data) {
        this._data = data;
        this._keys = [];
        this._results = [];
    }

    get request() {
        return this._data;
    }

    setKeyMap(...keys) {
        this._keys = this._keys.concat(keys);
    }

    parseData() {
        this._data = JSON.parse(this._data);
    }
}


let promise = request();
promise.then((data) => {
    let req = new Requests(data);
    req.parseData();
    let results = req.request;
    let html = "";
    console.log(results, 'res');
    Object.keys(results).forEach(key => {
        req.setKeyMap(key);
        if (key == 'avatar_url') {
            html += `<img src="${results[key]}" />`;
        } else {
            html += `<div>${key} - ${results[key]}</div>`;
        }

    });
    document.querySelector('#list').innerHTML = html;
    //console.log(resObject, 'resobj');
    console.log(req, 'req');
});
promise.catch((error) => {
   console.log(error, 'caught error');
});

function toKeyed (coll) {
    return coll.reduce((keyedColl, member) => {
        return Object.assign({}, keyedColl, { [member.id]: member } )
    }, {})
}

function combine (...colls) {
    const keyedColls = colls.map(toKeyed);

    return Object.values(_.merge(keyedColls))
}

console.log(combine({arg: 1}));
//let req = new Requests();
//let data = req.request;
//console.log(data, 'data');
//console.log(req, 'req');
//req.makeRequest();