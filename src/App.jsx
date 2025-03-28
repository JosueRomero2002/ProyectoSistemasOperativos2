import React, { useState } from 'react';
import './App.css';

function App() {
    const [numero, setNumero] = useState('');
    const [token, setToken] = useState('');
    const [error, setError] = useState('');
    const [success, setSuccess] = useState(false); // Nuevo estado para éxito

    const generarToken = async () => {
        try {
            // Hacer una solicitud al backend (PHP)
            const response = await fetch('http://localhost:8002/generar_token.php'); // URL del backend
            const data = await response.json();

            // Verificar si hay un error
            if (data.error) {
                setError(data.error);
                setNumero('');
                setToken('');
                setSuccess(false); // No hubo éxito
            } else {
                // Mostrar el número y el token
                setNumero(data.numero);
                setToken(data.token);
                setError('');
                setSuccess(true); // Hubo éxito
            }
        } catch (error) {
            console.error('Error al generar el token:', error);
            setError('Error al conectar con el servidor');
            setSuccess(false); // No hubo éxito
        }
    };

    return (
        <div className="App">
            <header className="App-header">
                <h1>Generador de Tokens</h1>
                <div className="numero">Número: {numero}</div>
                <div className="token">Token: {token}</div>
                {error && <div className="error">{error}</div>}
                {success && <div className="success">¡Token generado correctamente!</div>} {/* Mensaje de éxito */}
                <button onClick={generarToken}>Generar Nuevo Token</button>
            </header>
        </div>
    );
}

export default App;