import { useAuth } from './auth';
import { Navigate, Route, Routes } from 'react-router-dom';
import axios from 'axios';
import { useEffect, useState } from 'react';

const colors = {
  primary900: '#17377A',
  primary800: '#1E438F',
  primary600: '#3267D5',
  primary100: '#E8F0FF',
  background: '#F6F8FC',
  surface: '#FFFFFF',
  text: '#182230',
  textMuted: '#667085',
  border: '#E3E8EF',
  success: '#16834A',
  successBg: '#DDF8E7',
  warning: '#9A6700',
  warningBg: '#FFF0C2',
  danger: '#B42318',
  dangerBg: '#FEE4E2',
};

function StatCard({ label, value }: { label: string; value: number }) {
  return (
    <div style={{ backgroundColor: colors.surface, padding: '24px', borderRadius: '12px', boxShadow: '0 1px 3px rgba(16, 24, 40, 0.06)', flex: 1, minWidth: '200px' }}>
      <p style={{ margin: '0 0 8px 0', color: colors.textMuted, fontSize: '14px' }}>{label}</p>
      <p style={{ margin: 0, fontSize: '32px', fontWeight: 'bold', color: colors.text }}>{value}</p>
    </div>
  );
}

function Dashboard() {
  const { user } = useAuth();
  const [summary, setSummary] = useState<any>(null);
  useEffect(() => {
    if (user) {
      axios.get('http://localhost:3000/dashboard/summary', { headers: { Authorization: `Bearer ${user.token}` } })
        .then(r => setSummary(r.data))
        .catch(console.error);
    }
  }, [user]);
  return (
    <div style={{ padding: '32px' }}>
      <h1 style={{ margin: '0 0 8px 0', fontSize: '28px', fontWeight: 'bold', color: colors.text }}>Dashboard</h1>
      <p style={{ margin: '0 0 24px 0', color: colors.textMuted, fontSize: '14px' }}>Ringkasan kondisi inventaris Setia Elektronik</p>
      {summary && (
        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(200px, 1fr))', gap: '16px' }}>
          <StatCard label="Total Produk" value={summary.totalProducts} />
          <StatCard label="Total Unit Stok" value={summary.totalUnits} />
          <StatCard label="Stok Rendah" value={summary.lowStock} />
          <StatCard label="Stok Habis" value={summary.outOfStock} />
        </div>
      )}
    </div>
  );
}

function Products() {
  const { user } = useAuth();
  const [products, setProducts] = useState<any[]>([]);
  const [form, setForm] = useState({ sku: '', name: '', categoryId: 1, unit: 'pcs', purchasePrice: 0, sellingPrice: 0, minimumStock: 5 });
  useEffect(() => {
    if (user) {
      axios.get('http://localhost:3000/products', { headers: { Authorization: `Bearer ${user.token}` } })
        .then(r => setProducts(r.data))
        .catch(console.error);
    }
  }, [user]);
  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!user) return;
    await axios.post('http://localhost:3000/products', form, { headers: { Authorization: `Bearer ${user.token}` } });
    setForm({ sku: '', name: '', categoryId: 1, unit: 'pcs', purchasePrice: 0, sellingPrice: 0, minimumStock: 5 });
    const r = await axios.get('http://localhost:3000/products', { headers: { Authorization: `Bearer ${user.token}` } });
    setProducts(r.data);
  };
  return (
    <div style={{ padding: '32px' }}>
      <h1 style={{ margin: '0 0 8px 0', fontSize: '28px', fontWeight: 'bold', color: colors.text }}>Produk</h1>
      <p style={{ margin: '0 0 24px 0', color: colors.textMuted, fontSize: '14px' }}>Kelola daftar produk inventaris</p>
      <form onSubmit={handleSubmit} style={{ backgroundColor: colors.surface, padding: '24px', borderRadius: '12px', marginBottom: '24px', display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(150px, 1fr))', gap: '12px' }}>
        <input placeholder="SKU" value={form.sku} onChange={e => setForm({ ...form, sku: e.target.value })} style={{ padding: '8px', border: `1px solid ${colors.border}`, borderRadius: '6px', fontSize: '14px' }} />
        <input placeholder="Nama" value={form.name} onChange={e => setForm({ ...form, name: e.target.value })} style={{ padding: '8px', border: `1px solid ${colors.border}`, borderRadius: '6px', fontSize: '14px' }} />
        <input placeholder="Kategori ID" type="number" value={form.categoryId} onChange={e => setForm({ ...form, categoryId: Number(e.target.value) })} style={{ padding: '8px', border: `1px solid ${colors.border}`, borderRadius: '6px', fontSize: '14px' }} />
        <input placeholder="Satuan" value={form.unit} onChange={e => setForm({ ...form, unit: e.target.value })} style={{ padding: '8px', border: `1px solid ${colors.border}`, borderRadius: '6px', fontSize: '14px' }} />
        <input placeholder="Harga Beli" type="number" value={form.purchasePrice} onChange={e => setForm({ ...form, purchasePrice: Number(e.target.value) })} style={{ padding: '8px', border: `1px solid ${colors.border}`, borderRadius: '6px', fontSize: '14px' }} />
        <input placeholder="Harga Jual" type="number" value={form.sellingPrice} onChange={e => setForm({ ...form, sellingPrice: Number(e.target.value) })} style={{ padding: '8px', border: `1px solid ${colors.border}`, borderRadius: '6px', fontSize: '14px' }} />
        <input placeholder="Stok Min" type="number" value={form.minimumStock} onChange={e => setForm({ ...form, minimumStock: Number(e.target.value) })} style={{ padding: '8px', border: `1px solid ${colors.border}`, borderRadius: '6px', fontSize: '14px' }} />
        <button type="submit" style={{ backgroundColor: colors.primary600, color: 'white', padding: '8px 16px', borderRadius: '6px', border: 'none', cursor: 'pointer', fontWeight: 'bold' }}>Tambah Produk</button>
      </form>
      <div style={{ backgroundColor: colors.surface, borderRadius: '12px', overflow: 'hidden', boxShadow: '0 1px 3px rgba(16, 24, 40, 0.06)' }}>
        <table style={{ width: '100%', borderCollapse: 'collapse' }}>
          <thead style={{ backgroundColor: colors.primary100 }}>
            <tr>
              <th style={{ padding: '16px', textAlign: 'left', fontWeight: 'bold', color: colors.text, borderBottom: `1px solid ${colors.border}` }}>SKU</th>
              <th style={{ padding: '16px', textAlign: 'left', fontWeight: 'bold', color: colors.text, borderBottom: `1px solid ${colors.border}` }}>Nama</th>
              <th style={{ padding: '16px', textAlign: 'left', fontWeight: 'bold', color: colors.text, borderBottom: `1px solid ${colors.border}` }}>Stok</th>
            </tr>
          </thead>
          <tbody>
            {products.map(p => (
              <tr key={p.id} style={{ borderBottom: `1px solid ${colors.border}` }}>
                <td style={{ padding: '16px', color: colors.text }}>{p.sku}</td>
                <td style={{ padding: '16px', color: colors.text }}>{p.name}</td>
                <td style={{ padding: '16px', color: colors.text, fontWeight: 'bold' }}>{p.currentStock}</td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}

function Login() {
  const { login } = useAuth();
  const [u, setU] = useState('');
  const [p, setP] = useState('');
  const [err, setErr] = useState('');
  const handle = async (e: React.FormEvent) => {
    e.preventDefault();
    try {
      await login(u, p);
    } catch {
      setErr('Login gagal');
    }
  };
  return (
    <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'center', height: '100vh', backgroundColor: colors.background }}>
      <form onSubmit={handle} style={{ backgroundColor: colors.surface, padding: '32px', borderRadius: '12px', boxShadow: '0 1px 3px rgba(16, 24, 40, 0.06)', width: '100%', maxWidth: '400px' }}>
        <h2 style={{ margin: '0 0 24px 0', fontSize: '24px', fontWeight: 'bold', color: colors.text, textAlign: 'center' }}>Setia Elektronik</h2>
        <p style={{ margin: '0 0 24px 0', fontSize: '14px', color: colors.textMuted, textAlign: 'center' }}>Sistem Inventaris</p>
        <input placeholder="Username" value={u} onChange={e => setU(e.target.value)} style={{ width: '100%', padding: '12px', marginBottom: '12px', border: `1px solid ${colors.border}`, borderRadius: '6px', fontSize: '14px', boxSizing: 'border-box' }} />
        <input placeholder="Password" type="password" value={p} onChange={e => setP(e.target.value)} style={{ width: '100%', padding: '12px', marginBottom: '16px', border: `1px solid ${colors.border}`, borderRadius: '6px', fontSize: '14px', boxSizing: 'border-box' }} />
        {err && <p style={{ color: colors.danger, fontSize: '14px', marginBottom: '16px' }}>{err}</p>}
        <button type="submit" style={{ width: '100%', padding: '12px', backgroundColor: colors.primary600, color: 'white', border: 'none', borderRadius: '6px', fontWeight: 'bold', fontSize: '14px', cursor: 'pointer' }}>Login</button>
      </form>
    </div>
  );
}

function App() {
  const { user, logout } = useAuth();
  return (
    <div style={{ display: 'flex', minHeight: '100vh', backgroundColor: colors.background }}>
      {user && (
        <div style={{ width: '240px', backgroundColor: colors.primary900, padding: '24px', color: 'white', display: 'flex', flexDirection: 'column' }}>
          <h3 style={{ margin: '0 0 32px 0', fontSize: '18px', fontWeight: 'bold' }}>Setia Elektronik</h3>
          <nav style={{ flex: 1 }}>
            <a href="/dashboard" style={{ display: 'block', padding: '12px', marginBottom: '8px', color: 'white', textDecoration: 'none', borderRadius: '6px', backgroundColor: 'rgba(255,255,255,0.1)' }}>Dashboard</a>
            <a href="/products" style={{ display: 'block', padding: '12px', marginBottom: '8px', color: 'white', textDecoration: 'none', borderRadius: '6px' }}>Produk</a>
          </nav>
          <button onClick={logout} style={{ padding: '12px', backgroundColor: colors.danger, color: 'white', border: 'none', borderRadius: '6px', cursor: 'pointer', fontWeight: 'bold' }}>Logout</button>
        </div>
      )}
      <div style={{ flex: 1 }}>
        {user && (
          <div style={{ backgroundColor: colors.surface, borderBottom: `1px solid ${colors.border}`, padding: '16px 32px', display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
            <h2 style={{ margin: 0, color: colors.text }}>Inventory System</h2>
            <p style={{ margin: 0, color: colors.textMuted }}>{user.name}</p>
          </div>
        )}
        <Routes>
          <Route path="/login" element={<Login />} />
          {user ? (
            <>
              <Route path="/" element={<Navigate to="/dashboard" replace />} />
              <Route path="/dashboard" element={<Dashboard />} />
              <Route path="/products" element={<Products />} />
            </>
          ) : (
            <Route path="*" element={<Navigate to="/login" replace />} />
          )}
        </Routes>
      </div>
    </div>
  );
}

export default App;
