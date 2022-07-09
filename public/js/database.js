const apiService = "https://www.gforcesolution.com/app/satelliteholo/api/v1/index.php";

class Database {

    httpRequest(method, route, body = null) {
        return new Promise(function (resolve, reject) {

            var xhr = new XMLHttpRequest();

            xhr.open(method, apiService + route, true);
            xhr.setRequestHeader('Content-type', 'application/json');

            xhr.onload = function () {
                // Request finished. Do processing here.
                if (isJsonStructure(this.responseText)) {
                    resolve(JSON.parse(this.responseText));
                } else {
                    reject(Error("Structure Error."));
                }
            };

            xhr.onerror = function () {
                console.log(this.status);
                console.log(this.statusText);
                reject(Error("Request Error."));
            }

            xhr.send(JSON.stringify(body));
        })
    }

    upload(route, file, name) {

        return new Promise(function (resolve, reject) {
            var formData = new FormData();

            formData.append("file", file);
            formData.append("name", name);

            var xhr = new XMLHttpRequest();

            xhr.open("POST", apiService + route, true);
            // xhr.setRequestHeader('Content-type', 'application/json');

            xhr.onload = function () {
                // Request finished. Do processing here.
                if (isJsonStructure(this.responseText)) {
                    resolve(JSON.parse(this.responseText));
                } else {
                    reject(Error("Structure Error."));
                }
            };

            xhr.onerror = function () {
                console.log(this.status);
                console.log(this.statusText);
                reject(Error("Request Error."));
            }

            xhr.send(formData);
        })
    }
}

function isJsonStructure(str) {
    if (typeof str !== 'string') return false;
    try {
        const result = JSON.parse(str);
        const type = Object.prototype.toString.call(result);
        return type === '[object Object]' ||
            type === '[object Array]';
    } catch (err) {
        return false;
    }
}