// Serveur Socket.IO pour notifications temps réel
const { Server } = require('socket.io');
const http = require('http');

const server = http.createServer();
const io = new Server(server, {
  cors: {
    origin: '*',
    methods: ['GET', 'POST']
  }
});

// Quand un client se connecte
io.on('connection', (socket) => {
  console.log('Client connecté :', socket.id);

  // Rejoindre une room utilisateur ou groupe
  socket.on('join', (room) => {
    socket.join(room);
    console.log(`Socket ${socket.id} a rejoint la room ${room}`);
  });

  // Déconnexion
  socket.on('disconnect', () => {
    console.log('Client déconnecté :', socket.id);
  });
});

// Endpoint HTTP pour Laravel : POST /notify-message
const express = require('express');
const app = express();
app.use(express.json());

app.post('/notify-message', (req, res) => {
  const { room, message } = req.body;
  if (room && message) {
    io.to(room).emit('new-message', message);
    return res.json({ status: 'ok' });
  }
  res.status(400).json({ error: 'room et message requis' });
});

server.listen(4000, () => {
  console.log('Socket.IO server running on port 4000');
});
app.listen(4001, () => {
  console.log('Express HTTP API for Socket.IO running on port 4001');
}); 