import React, { createContext, useContext, useEffect, useState } from 'react';
import axios from 'axios';

type User = { id: number; name: string; role: string; token: string };
type AuthContextType = { user: User | null; login: (u: string, p: string) => Promise<void>; logout: () => void };

const AuthContext = createContext<AuthContextType | undefined>(undefined);

export const AuthProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const [user, setUser] = useState<User | null>(null);
  const login = async (u: string, p: string) => {
    const res = await axios.post('http://localhost:3000/auth/login', { username: u, password: p });
    const token = res.data.token;
    const payload = JSON.parse(atob(token.split('.')[1]));
    setUser({ id: payload.id, name: u, role: payload.role, token });
    localStorage.setItem('token', token);
  };
  const logout = () => { setUser(null); localStorage.removeItem('token'); };
  useEffect(() => {
    const t = localStorage.getItem('token');
    if (t) {
      const p = JSON.parse(atob(t.split('.')[1]));
      setUser({ id: p.id, name: 'admin', role: p.role, token: t });
    }
  }, []);
  return <AuthContext.Provider value={{ user, login, logout }}>{children}</AuthContext.Provider>;
};

export const useAuth = () => {
  const ctx = useContext(AuthContext);
  if (!ctx) throw new Error('AuthProvider missing');
  return ctx;
};
