
let trace1 = {
    x: ["'09-10", "'10-11", "'11-12"],
    y: [18500, 19000, 19500],
    name: 'Suny Opt Year 1',
    marker: {
        line: {
            color: 'rgb(255, 0, 0)'
        }
    },
    type: 'bar'
};

let trace2 = {
    x: ["'09-10", "'10-11", "'11-12"],
    y: [20000, 25000, 27000],
    name: 'Suny Opt Year 2-4**',
    type: 'bar'
};

let trace3 = {
    x: ["'09-10", "'10-11", "'11-12"],
    y: [33000, 34000, 35000],
    name: 'Public Programs',
    type: 'bar'
};

export let data = [trace1, trace2, trace3];
export let layout = {

    barmode: 'group'
};

