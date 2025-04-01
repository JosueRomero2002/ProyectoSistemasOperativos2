import { StrictMode } from 'react'
import { createRoot } from 'react-dom/client'
import App from './App.jsx'
import Menu from "./Menu.jsx"
import './index.css'
import React, {useState, useEffect} from 'react';

const Root = () => {
  const [isLoggedIn, setIsLoggedIn] = useState(false);
  const [userData, setUserData] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const checkAuth = async () => {
      const token = localStorage.getItem('authToken');
      if (token) {
        try {
          const response = await fetch('http://localhost:8002/validate_token.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ token })
          });
          
          const data = await response.json();
          if (data.success) {
            setUserData(data.user);
            setIsLoggedIn(true);
          }
        } catch (error) {
          console.error('Error validando token:', error);
        }
      }
      setLoading(false);
    };

    checkAuth();
  }, []);

  const handleLogin = (data, password) => {
    localStorage.setItem('authToken', data.token); // Guardar token
    setUserData({
      username: data.username,
      email: data.email,
      token: data.token,
      pass: password
    });
    setIsLoggedIn(true);
  };

  const handleLogout = () => {
    localStorage.removeItem('authToken'); // Eliminar token
    setUserData(null);
    setIsLoggedIn(false);
  };

  if (loading) {
    return <div>Cargando...</div>;
  }

  return (
    <StrictMode>
      {isLoggedIn ? (
        <Menu user={userData} onLogout={handleLogout} />
      ) : (
        <App onLogin={handleLogin} />
      )}
    </StrictMode>
  );
};

// Exporta el componente como named export
export const RootComponent = Root;

// Renderiza usando el named export
createRoot(document.getElementById('root')).render(<RootComponent />);