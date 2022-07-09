function zeroPad(num, digits) {
    var zero = digits - num.toString().length + 1;
    return Array(+(zero > 0 && zero)).join("0") + num;
}

function compareValues(key, order = 'asc') {
    return function innerSort(a, b) {
        if (!a.hasOwnProperty(key) || !b.hasOwnProperty(key)) {
            // property doesn't exist on either object
            return 0;
        }

        const varA = (typeof a[key] === 'string')
            ? a[key].toUpperCase() : a[key];
        const varB = (typeof b[key] === 'string')
            ? b[key].toUpperCase() : b[key];

        let comparison = 0;

        if (typeof parseFloat(varA) === "number" && typeof parseFloat(varB) === "number") {
            if (parseFloat(varA) > parseFloat(varB)) {
                comparison = 1;
            } else if (parseFloat(varA) < parseFloat(varB)) {
                comparison = -1;
            }
        } else {
            if (varA > varB) {
                comparison = 1;
            } else if (varA < varB) {
                comparison = -1;
            }
        }

        return (
            (order === 'desc') ? (comparison * -1) : comparison
        );
    }
}

const waitUntil = async (f, timeoutMs) => {
    return new Promise((resolve, reject) => {
        const timeWas = new Date();
        const wait = setInterval(function () {
            if (f()) {
                // console.log("resolved after", new Date() - timeWas, "ms");
                clearInterval(wait);
                resolve();
            } else if (new Date() - timeWas > timeoutMs) { // Timeout
                // console.log("rejected after", new Date() - timeWas, "ms");
                clearInterval(wait);
                reject();
            }
        }, 20);
    });
}


function randomString(length) {
    var result = '';
    var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    var charactersLength = characters.length;
    for (var i = 0; i < length; i++) {
        result += characters.charAt(Math.floor(Math.random() *
            charactersLength));
    }
    return result;
}

function randomNumber(length) {
    var result = '';
    var characters = '0123456789';
    var charactersLength = characters.length;
    for (var i = 0; i < length; i++) {
        result += characters.charAt(Math.floor(Math.random() *
            charactersLength));
    }
    return result;
}

function thaiDateFormat(date, dateStyle = null) {

    var d = new Date(date);
    var style = dateStyle == null ? "long" : dateStyle;

    var dateString = d.toLocaleString("th-TH", {
        timeZone: "Asia/Bangkok",
        dateStyle: style
    });

    var dtFormat = {
        date: dateString,
        time: d.toLocaleTimeString('th-TH')
    }

    return dtFormat;
}

function toTime(n) {
    let num = parseFloat(n)
    let hour = parseInt(num / 3600);
    let min = parseInt((num - parseInt(hour * 3600)) / 60);
    let sec = parseInt(num - (hour * 3600) - (min * 60));

    let time = { "hour": hour, "min": min, "sec": sec };
    return time;
}


function getURLParameter(sParam) {
    var sPageURL = window.location.search.substring(1);
    var sURLVariables = sPageURL.split('&');
    for (var i = 0; i < sURLVariables.length; i++) {
        var sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] == sParam) {
            return sParameterName[1];
        }
    }
}


Number.prototype.format = function (n, x) {
    var re = '(\\d)(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\.' : '$') + ')';
    return this.toFixed(Math.max(0, ~~n)).replace(new RegExp(re, 'g'), '$1,');
};

function currencyFormat(number, decimalCount = 2) {
    return parseFloat(number).format(decimalCount);
}

function isValidEmail(email) {
    if (email.length == 0)
        return;

    if (email.length < 3)
        return false;

    if (!email.includes("@") || !email.includes("."))
        return false;

    let splitEmail = email.split("@");

    if (splitEmail.length != 2)
        return false;

    var onExtension = false;
    var emailExtension = [".com", ".net", ".co", ".org", ".cc", ".info", "biz", "co.th", "ac.th", "or.th", "in.th"];
    emailExtension.forEach(element => {
        if (splitEmail[splitEmail.length - 1].indexOf(element) != -1) {
            onExtension = true; // email extension is valid
        }
    });

    if (!onExtension)
        return false;

    let pattern = /^[^<>()[\]\\,;:\%#^\s@\"$&!@]+@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z0-9]+\.)+[a-zA-Z]{2,}))$/;

    if (!pattern.test(email))
        return false;

    return true;
}


function b64toBlob(image64, sliceSize) {

    // Split the base64 string in data and contentType
    var block = image64.split(";");
    // Get the content type of the image
    var contentType = block[0].split(":")[1]; // In this case "image/gif"
    // get the real base64 content of the file
    var realData = block[1].split(",")[1]; // In this case "R0lGODlhPQBEAPeoAJosM...."

    // Convert it to a blob to upload
    let b64Data = realData;

    contentType = contentType || '';
    sliceSize = sliceSize || 512;

    var byteCharacters = atob(b64Data);
    var byteArrays = [];

    for (var offset = 0; offset < byteCharacters.length; offset += sliceSize) {
        var slice = byteCharacters.slice(offset, offset + sliceSize);

        var byteNumbers = new Array(slice.length);
        for (var i = 0; i < slice.length; i++) {
            byteNumbers[i] = slice.charCodeAt(i);
        }

        var byteArray = new Uint8Array(byteNumbers);

        byteArrays.push(byteArray);
    }

    var blob = new Blob(byteArrays, {
        type: contentType
    });
    return blob;
}

////////////////////////////////////////////////////////////////
///////////////////// AUTHENTICATION / USER ////////////////////
////////////////////////////////////////////////////////////////

const tokenKey = "SATELLITEHOLO_TOKEN";
const cookieKey = "SATELLITEHOLO_COOKIE";


function isLoggedIn() {
    if (getToken() != "" && getToken() != null) {
        return true;
    } else {
        return false;
    }
}

function setToken(_token) {
    try {
        token = _token;
        localStorage.setItem(tokenKey, token);
        return token;
    } catch {
        return false;
    }
}

function getToken() {
    try {
        return localStorage.getItem(tokenKey);
    } catch (e) {
        return false;
    }
}

function clearToken() {
    localStorage.removeItem(tokenKey);
    token = "";
}

function acceptCookie() {
    localStorage.setItem(cookieKey, "true");
}

function cookieAccepted() {
    try {
        if (localStorage.getItem(cookieKey) == "true") {
            return true;
        }
        return false;
    } catch (e) {
        return false;
    }
}

function clearCookie() {
    localStorage.removeItem(cookieKey);
}

