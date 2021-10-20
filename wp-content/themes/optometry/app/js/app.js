require('babel-polyfill');
require('./record');
require('./requests');

class Person {

    constructor(name) {
        this._name = name;
    }

    get name() {
        return this._name;
    }

    set name(newName) {

        this._name = newName;
    }
}

let p = new Person('Jack');
let name = p.name;

console.log(name, 'name');
