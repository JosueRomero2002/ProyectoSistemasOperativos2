import React, { useState } from 'react';
import './App.css';

function App() {
    const [username, setUsername] = useState('');
    const [password, setPassword] = useState('');
    const [error, setError] = useState('');
    const [isSubmitting, setIsSubmitting] = useState(false);

    const handleSubmit = async (e) => {
        e.preventDefault();
        setIsSubmitting(true);
        setError('');

      /*  try {
            // Primero validar credenciales con tu backend
            const response = await fetch('http://localhost:8002/login.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ username, password }),
            });
            
            if (!response.ok) {
                throw new Error('Error de autenticación');
            }
*/
           
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'http://localhost/squirrelmail/src/redirect.php'; 
            const fields = {
                login_username: username,
                secretkey: password,
                js_autodetect_results: '1',  
                just_logged_in: '1',          
                smsubmit: 'Login'             
            };

            // Crear inputs hidden
            Object.entries(fields).forEach(([name, value]) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = name;
                input.value = value;
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();

      /*  } catch (err) {
            setError(err.message || 'Error al conectar con el servidor');
        } finally {
            setIsSubmitting(false);
        }*/
    };

    return (
        <div className="App">
            <header className="App-header">
                <h1>Login Portal</h1>
                <form onSubmit={handleSubmit}>
                    <div>
                        <input
                            type="text"
                            placeholder="Usuario"
                            value={username}
                            onChange={(e) => setUsername(e.target.value)}
                            required
                        />
                    </div>
                    <div>
                        <input
                            type="password"
                            placeholder="Contraseña"
                            value={password}
                            onChange={(e) => setPassword(e.target.value)}
                            required
                        />
                    </div>
                    <button type="submit" disabled={isSubmitting}>
                        {isSubmitting ? 'Ingresando...' : 'Iniciar Sesión'}
                    </button>
                    {error && <div className="error">{error}</div>}
                </form>
            </header>
        </div>
    );
}

export default App;