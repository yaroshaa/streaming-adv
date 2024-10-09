import Echo from 'laravel-echo';
import EventBus from './eventbus';
import channels from './events';

window.io = require('socket.io-client');

let EchoServer = null;

if (process.env.NODE_ENV === 'development') {
    EchoServer = new Echo({
        broadcaster: 'socket.io',
        host: window.location.hostname + ':6001',
    });
} else {
    EchoServer = new Echo({
        broadcaster: 'socket.io',
        host: window.location.hostname,
    });
}

channels.forEach((data) => {
    EchoServer.channel(data.channel).listen(data.event, (e) => {
        EventBus.emit(data.event, e);
    });
});

export default EchoServer;
