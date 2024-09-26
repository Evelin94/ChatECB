
const socket = new WebSocket('ws://localhost:8080');

        socket.onopen = function() {
            document.getElementById("messages").innerHTML += "<p>Conectado al servidor</p>";
        };

        socket.onmessage = function(event) {
            var message = event.data;
            document.getElementById("messages").innerHTML += "<p>" + message + "</p>";
        };

        socket.onclose = function() {
            document.getElementById("messages").innerHTML += "<p>Conexión cerrada</p>";
        };

        socket.onerror = function(error) {
            console.log("Error:", error);
            document.getElementById("messages").innerHTML += "<p>Error: " + error.message + "</p>";
        };

        function sendMessage() {
            var message = document.getElementById("message-input").value;
            socket.send(message);
            document.getElementById("message-input").value = '';
        }

        document.getElementById("send-button").onclick = sendMessage;