// Local test fixture only. Never used by the application's default build.
import http from 'node:http';
const requests = [];
const server = http.createServer(async (req, res) => {
  let body = '';
  for await (const chunk of req) body += chunk;
  res.setHeader('Content-Type', 'application/json');
  if (req.url === '/requests') return res.end(JSON.stringify(requests));
  requests.push({method: req.method, path: req.url});
  if (req.method !== 'POST') { res.writeHead(404); return res.end('{}'); }
  let data;
  try { data = JSON.parse(body); } catch { res.writeHead(400); return res.end('{}'); }
  if (req.url === '/api/mobile/login') {
    if (data.username !== 'qa-user' || data.password !== 'qa-pass') {
      res.writeHead(401); return res.end('{}');
    }
    return res.end(JSON.stringify({name: 'Karyawan QA'}));
  }
  if (req.url === '/api/attendances') {
    if (!['masuk', 'pulang'].includes(data.type) || typeof data.camera_access_granted !== "boolean") {
      res.writeHead(422); return res.end('{}');
    }
    res.writeHead(201);
    return res.end(JSON.stringify({id: requests.length, type: data.type,
      occurred_at: new Date().toISOString(), status: 'Tepat waktu', is_late: false}));
  }
  res.writeHead(404); res.end('{}');
});
server.listen(18080, '127.0.0.1', () => console.log('QA API listening on 127.0.0.1:18080'));
