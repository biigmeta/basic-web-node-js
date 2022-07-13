const auth_key = "authenticationkey"

class User {

    id = ""
    uuid = ""
    email = ""
    firstname = ""
    lastname = ""
    picture = ""
    gender = ""
    phone = ""

    constructor() {

    }

    setData(user)
    {
        this.id = user.id
        this.uuid = user.uuid
        this.email = user.email
        this.firstname = user.firstname
        this.lastname = user.lastname
        this.gender = user.gender
        this.picture = user.picture
    }

    isLogIn() {
        if (localStorage.getItem(auth_key) == null || localStorage.getItem(auth_key) == "")
            return false
        return true
    }

    setToken(tk) {
        localStorage.setItem(auth_key, tk)
        return tk
    }

    getToken() {
        return localStorage.getItem(auth_key)
    }

    logout() {
        localStorage.removeItem(auth_key);
    }
}



