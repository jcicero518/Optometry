import endpoint from "./instagram";

class Record {

    constructor() {
        this._records = [];
        this._endpoint = endpoint;
    }

    addRecords(...records) {
        this._records = this._records.concat(records);
    }

    get records() {
        return this._records;
    }

    get endpoint() {
        return this._endpoint;
    }

    *[Symbol.iterator]() {
        for (let rec of this._records) {
            yield rec.toUpperCase();
        }
    }
}

let record = new Record();
console.log(record, 'rec');
console.log(endpoint, 'end');


record.addRecords('June', 'Jack', 'Tommy');

for (let rec of record) {
    console.log(rec, 'rec');
}
console.log(record.records);