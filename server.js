const { Server } = require('ws');

// إنشاء WebSocket Server
const wss = new Server({ port: 5500 });

wss.on('connection', function connection(ws) {
    console.log('Client connected');

    // إرسال رسالة عند استقبال الإشعار الجديد
    ws.on('message', function incoming(data) {
        console.log(`Received message: ${data}`);
    });

    ws.on('close', () => {
        console.log('Client disconnected');
    });
});

// دالة لإرسال الإشعارات لجميع العملاء المتصلين
function broadcastNotification(notification) {
    wss.clients.forEach(client => {
        if (client.readyState === client.OPEN) { // استخدام readyState مع client
            client.send(JSON.stringify(notification));
        }
    });
}

module.exports = {
    broadcastNotification
};