    const express = require('express');
    const app = express();

    // Import the scheduler
    
    app.listen(3000, () => {
        console.log("Server started on port 3000");
        require('./server');

    });