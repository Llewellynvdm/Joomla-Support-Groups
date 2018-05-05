self.addEventListener('message', function(e) {
    // start looping the rows
     for (i = 0; i < e.data.rows.length; i++) {
            // Perform many updates
            self.postMessage({'row':e.data.rows[i], 'type':'addRow'});
    }
    // only report foo after done with loop
    self.postMessage({'foo':e.data.foo, 'type':'foo'});
}, false);
