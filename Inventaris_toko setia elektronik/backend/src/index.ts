import express from 'express';
import cors from 'cors';
import dotenv from 'dotenv';
import jwt from 'jsonwebtoken';

dotenv.config();

const app = express();
app.use(cors());
app.use(express.json());

const JWT_SECRET = process.env.JWT_SECRET || 'secret';

// demo admin
const admin = { id: 1, username: 'admin', password: 'admin', role: 'admin' };

app.post('/auth/login', (req, res) => {
  const { username, password } = req.body;
  if (username === admin.username && password === admin.password) {
    const token = jwt.sign({ id: admin.id, username: admin.username, role: admin.role }, JWT_SECRET);
    return res.json({ token });
  }
  res.status(401).json({ error: 'Invalid' });
});

// dummy data
const products = [
  { id: 1, sku: 'PROD001', name: 'Kabel NYM 2.5mm²', currentStock: 50 },
  { id: 2, sku: 'PROD002', name: 'Lampu LED 9W', currentStock: 30 },
];

app.get('/products', (req, res) => {
  // simple auth check (optional)
  const auth = req.headers.authorization?.split(' ')[1];
  if (!auth) return res.status(401).json({ error: 'No token' });
  try { jwt.verify(auth, JWT_SECRET); } catch { return res.status(401).json({ error: 'Bad token' }); }
  res.json(products);
});

app.get('/dashboard/summary', (req, res) => {
  const auth = req.headers.authorization?.split(' ')[1];
  if (!auth) return res.status(401).json({ error: 'No token' });
  try { jwt.verify(auth, JWT_SECRET); } catch { return res.status(401).json({ error: 'Bad token' }); }
  res.json({ totalProducts: products.length, totalUnits: products.reduce((a, p) => a + p.currentStock, 0), lowStock: 0, outOfStock: 0 });
});

const PORT = Number(process.env.PORT) || 3000;
app.listen(PORT, () => console.log(`Server running on ${PORT}`));
