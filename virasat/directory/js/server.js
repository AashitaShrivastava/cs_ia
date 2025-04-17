
console.log("Starting server...");
const express = require('express');
const connection = require('./db.js'); // Ensure db.js also exists

const app = express();
app.use(express.json());

app.get('/products', (req, res) => {
    connection.query('SELECT * FROM products', (err, results) => {
        if (err) throw err;
        res.json(results);
    });
});

app.listen(3000, () => {
    console.log('Server is running on http://localhost:3000');
});
