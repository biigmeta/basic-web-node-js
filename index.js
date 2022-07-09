const express = require('express')
const app = express()
const http = require('http').Server(app)
const io = require('socket.io')(http)


app.use(express.static(__dirname + '/public'));

// initial route
app.get('/', (req, res) => {

    res.sendFile(__dirname + "/public/index.html")
})

app.get('/chatroom', (req, res) => {
    res.sendFile(__dirname + "/public/chatroom.html")
})

let channels = []

// initial socket
io.on('connection', (socket) => {

    console.log(socket.id + " connected");

    // set first time user display name
    socket.displayname = "anonymous"

    socket.on("publish_message", (event, callback) => {
        io.sockets.emit("receive_message", event)
        callback({ "status": "success", "content": event })
    })


    // ## disconnect ## //
    socket.on('disconnect', () => {
        console.log('user disconnected');
    });
});

var port = process.env.port || 3000
http.listen(port, function () {
    console.log(`start server on port ${port}`)
})