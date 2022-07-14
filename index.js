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

// ## json web token ## //
const jwt = require("jsonwebtoken")
const secret = "basic-web"


// app.use(cors())
app.use(express.static(__dirname + '/public'))
app.use(bodyParser.urlencoded({ extended: true })) // to support URL-encoded bodies
app.use(express.json()) //to support JSON-encoded bodies


// ## create database connection ## //
var con = mysql.createConnection({
    host: "awsdemo.cmn3ors2efis.ap-southeast-1.rds.amazonaws.com",
    user: "admin",
    password: "12345678",
    database: "biig_chat_app"
});


/* =============================================== ROUTE ====================================================== */

// initial route
app.get('/', (req, res) => {
    res.sendFile(__dirname + "/public/index.html")
})

app.get('/register', (req, res) => {
    res.sendFile(__dirname + "/public/register.html")
})

app.get('/login', (req, res) => {
    res.sendFile(__dirname + "/public/login.html")
})

app.get('/chatroom/lobby', (req, res) => {
    res.sendFile(__dirname + "/public/chatroom/lobby.html")
})

app.get('/chatroom/chat', (req, res) => {
    res.sendFile(__dirname + "/public/chatroom/chat.html")
})

/* =============================================== API ====================================================== */

app.post('/register', (req, res, next) => {

    // ## get post body data ## //
    let body = req.body

    // ## assign variable ## //
    let email = body['email']
    let password = body['password']
    let firstname = body['firstname']
    let lastname = body['lastname']
    let picture = body['picture']
    let phone = body['phone']
    let gender = body['gender']
    let displayname = firstname + " " + lastname
    let uid = uuid.v4()

    // ## bind param ## //
    bcrypt.hash(password, saltRounds, (err, hash) => {
        let sql = `INSERT INTO users (uuid,email, password, firstname, lastname,displayname,picture,phone,gender) VALUES (?,?,?,?,?,?,?,?,?)`
        let values = [uid, email, hash, firstname, lastname, displayname, picture, phone, gender]

        // ## query ## //
        con.query(sql, values, (err, result) => {

            if (err) {
                res.json({ "status": "error", "message": err.sqlMessage })
                return
            }

            res.json({ "status": "success", "message": "register successfully.", "data": result })
        })
    })
})

app.post('/me', (req, res, next) => {

    // ## get post body data ## //
    let body = req.body

    // ## assign variable ## //
    let token = body['token']

    try {

        let decoded = jwt.verify(token, secret)
        let payload = decoded['payload']

        let exp = payload['exp']
        let now = new Date()

        let check_exp_token = false

        if (now > exp && check_exp_token) {
            res.json({ "status": "error", "message": "token time out." })
            return
        }

        let data = payload['data']
        let user_id = data['id']

        let sql = `SELECT * FROM users WHERE id = ?`
        let values = [user_id]

        con.query(sql, values, (err, result) => {
            res.json({ "status": "success", "message": "get self data successfully.", "data": result[0] })
        })



    } catch (err) {
        res.json({ status: "error", messege: err.message })
    }

})


app.post('/login', (req, res, next) => {

    // ## get post body data ## //
    let body = req.body

    // ## assign variable ## //
    let email = body['email']
    let password = body['password']

    let sql = `SELECT * FROM users WHERE email = ?`
    let values = [email]

    con.query(sql, values, (err, result) => {

        if (err) {
            res.json({ "status": "error", "message": err.sqlMessage })
            return
        }

        if (result.length == 0) {
            res.json({ "status": "error", "message": "data not found." })
            return
        }

        // ## check password ## //
        bcrypt.compare(password, result[0]['password'], (err, corrected) => {

            if (!corrected) {
                res.json({ "status": "error", "message": "incorrect password." })
                return
            }

            // ## unset password before return result
            result.forEach(element => {
                delete element['password']
            });

            // ## encode to jwt ## //
            let expired_min = 15
            let now = new Date()
            let expired = new Date(new Date().setTime(new Date().getTime() + (1000 * 60 * expired_min)))

            let payload = {
                "data": result[0],
                "exp": expired,
                "iat": now
            }

            let token = jwt.sign({ payload }, secret)
            res.json({ "status": "success", "message": "log in successfully.", "data": result[0], "token": token })
        })
    })
})


/* =============================================== SOCKET ====================================================== */

let users = []
let rooms = []

io.on('connection', (client) => {

    console.log(client.id + " connected");

    // join channel
    client.on("subscribe", (user, channel) => {

        client.join(channel)

        // check room exist
        let r = rooms.find(item => item.channel == channel)
        let action = ""

        if (r != undefined) {
            action = "join"

            if (r.clients.length != 0) {
                // check user already join
                if (r.clients.find(item => item.uuid == user.uuid) == undefined) {
                    r.clients.push(user)
                }
            } else {
                r.clients.push(user)
            }

            // send broadcast number of users in this channel
            io.in(channel).emit('online_users', r.clients.length);

        } else {
            action = "create"

            // create room and join the room
            let room_id = uuid.v4()
            let newroom = {
                "id": room_id,
                "channel": channel,
                "name": channel,
                "owner": user,
                "clients": []
            }

            newroom.clients.push(user)
            rooms.push(newroom)

            // send number of users in this channel to client who create the channel
            client.emit('online_users', 1);
        }

        console.log("client id: " + client.id + " " + action + " the room: " + channel)
    })

    // leave channel
    client.on("unsubscribe", (user, channel) => {
        
        client.leave(channel)
    })

    client.on("unsubscribe_all", () => {

    })

    client.on("online_users", (room) => {

        let r = rooms.find(item => item.name == room)

        if (r == undefined) {
            client.emit('online_users', 0)
        } else {
            client.emit('online_users', r.clients.length)
        }
    })

    client.on("create_room", (user, name) => {

        let room = {
            "id": user.uuid,
            "channel": user.uuid,
            "name": name,
            "owner": user,
            "clients": [user]
        }

        rooms.push(room)
        client.join(room.channel)

        let event = { "status": "success", "channel": room.channel, "room": room }

        client.emit("create_room", event)
        client.broadcast.to("lobby").emit('online_rooms', event);

        // ## console log event ## //
        console.log("Room " + name + " has created by " + user.firstname + " " + user.lastname);
    })

    client.on("join room", (user, name) => {

    })

    client.on("publish_message", (channel, event, callback) => {
        io.in(channel).emit('receive_message', event);
        callback({ "status": "success", "content": event })
    })


    // ## disconnect ## //
    client.on('disconnect', () => {
        // ## find disconnected user from users ## //
        let tempUser = users.find(item => item.uuid == socket.id)
        var index = users.indexOf(tempUser);

        if (index !== -1) {
            // ## remove user in users ## //
            users.splice(index, 1);

            // ## broadcast online user ## //
            io.sockets.emit("online users", users)

        }
        // ## console log event ## //
        console.log(client.id + " disconnected")
    });
});


// ## start server ## //
// var port = process.env.port || 3001
var port = 3001
http.listen(port, function () {
    console.log(`start server on port ${port}`)
})