import { StrictMode, useState } from 'react'
import { createRoot } from 'react-dom/client'
import './index.css'
import App from './App.jsx'
import Menu from "./Menu.jsx"

const RootComponent = () => {
  const [isLoggedIn, setIsLoggedIn] = useState(false);
  const [userData, setUserData] = useState(null);

  const handleLogin = (user) => {
    setUserData(user);
    setIsLoggedIn(true);
  };

  const handleLogout = () => {
    setUserData(null);
    setIsLoggedIn(false);
  };

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

createRoot(document.getElementById('root')).render(<RootComponent />);