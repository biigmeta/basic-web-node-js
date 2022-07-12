const express = require('express')
const app = express()
const http = require('http').Server(app)
const io = require('socket.io')(http)
const mysql = require('mysql')
const cors = require("cors")
const bodyParser = require('body-parser')
const uuid = require('uuid')

// ## encode password package ## //
const bcrypt = require('bcrypt');
const saltRounds = 10;

app.use(express.static(__dirname + '/public'))
app.use(bodyParser.urlencoded({ extended: true })) // to support URL-encoded bodies
app.use(express.json()) //to support JSON-encoded bodies
// app.use(cors) // to support access from other domain

// ## create database connection ## //
var con = mysql.createConnection({
    host: "localhost",
    user: "root",
    password: "",
    database: "metaverse_webservice"
});

// initial route
app.get('/', (req, res) => {
    res.sendFile(__dirname + "/public/index.html")
})

app.get('/register', (req, res) => {
    res.sendFile(__dirname + "/public/register.html")
})

app.get('/chat/lobby', (req, res) => {
    res.sendFile(__dirname + "/public/chat-lobby.html")
})

app.get('/chat/room', (req, res) => {
    res.sendFile(__dirname + "/public/chat-room.html")
})

app.post('/register', (req, res, next) => {

    let body = req.body

    let email = body['email']
    let password = body['password']
    let firstname = body['firstname']
    let lastname = body['lastname']
    let picture = body['picture']
    let phone = body['phone']
    let gender = body['gender']
    let displayname = firstname + " " + lastname
    let uid = uuid.v4()

    bcrypt.hash(password, saltRounds, (err, hash) => {
        let sql = `INSERT INTO users (uuid,email, password, firstname, lastname,displayname,picture,phone,gender) VALUES (?,?,?,?,?,?,?,?,?)`
        let values = [uid, email, hash, firstname, lastname, displayname, picture, phone, gender]
        con.query(sql, values, (err, result) => {
            if (err) {
                res.json({ "status": "error", "message": err.sqlMessage })
                return
            }
            
            res.json({ "status": "success", "message": "register successfully.", "data": result })
        })
    })


})


let users = []
let rooms = []

io.on('connection', (socket) => {

    console.log(socket.id + " connected");

    // set first time user display name
    socket.displayname = "anonymous"

    socket.on("join lobby", (user) => {
        users.push(user)
    })

    socket.on("users", (room) => {
        socket.emit('lobby users', users);
    })

    socket.on("create room", (user, roomName) => {
        let room = {
            "name": roomName,
            "users": [user]
        }

        rooms.push(room)
        socket.join(roomName)
        io.sockets.in(roomName).emit('create room', "Room " + roomName + " has created by " + user.firstname + " " + user.lastname);

        // ## console log event ## //
        console.log("Room " + roomName + " has created by " + user.firstname + " " + user.lastname);
    })

    socket.on("join room", (user, roomName) => {
        // rooms[roomName].users.push(users)
    })

    socket.on("publish_message", (event, callback) => {
        io.sockets.emit("receive_message", event)
        callback({ "status": "success", "content": event })
    })


    // ## disconnect ## //
    socket.on('disconnect', () => {
        // ## find disconnected user from users ## //
        let tempUser = users.find(item => item.id == socket.id)
        var index = users.indexOf(tempUser);

        if (index !== -1) {
            // ## remove user in users ## //
            users.splice(index, 1);

            // ## broadcast online user ## //
            io.sockets.emit("online users", users)

        }
        // ## console log event ## //
        console.log(socket.id + " disconnected")
    });
});


// ## start server ## //
var port = process.env.port || 3000
http.listen(port, function () {
    console.log(`start server on port ${port}`)
})