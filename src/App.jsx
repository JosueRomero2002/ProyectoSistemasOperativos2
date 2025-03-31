import React, { useState } from 'react';
import './App.css';


function App() {
    const [username, setUsername] = useState('');
    const [password, setPassword] = useState('');
    const [email, setEmail] = useState('');
    const [error, setError] = useState('');
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [isRegistering, setIsRegistering] = useState(false);
    const [successMessage, setSuccessMessage] = useState('');
    //const [isRegistering, setIsRegistering] = useState(false);
   // const [email, setEmail] = useState('');
   const [redirecting, setRedirecting] = useState(false);
    
   
   const handleSubmit = async (e) => {
    e.preventDefault();
    setIsSubmitting(true);
    setError('');

    try {
        const response = await fetch('http://localhost:8002/login.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username, password }),
        });

        const data = await response.json();

        if (!response.ok) throw new Error(data.error || 'Error de autenticación');

        console.log(data);




        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'http://localhost/squirrelmail/src/redirect.php'; 
        const fields = {
            login_username: data.username,
            secretkey: data.password,
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




   // setRedirecting(true);
    //setTimeout(() => {
  //      window.location.href = data.squirrelmail_url;
   // }, 2000);


        // Redirección principal a SquirrelMail
    //    window.location.href = data.squirrelmail_url;

        // Abrir Moodle en nueva pestaña después de 1 segundo
     //   setTimeout(() => {
       //     window.open(data.moodle_url, '_blank');
       // }, 1000);

    } catch (err) {
        setError(err.message);
    } finally {
        setIsSubmitting(false);
    }
};
    // Nuevo método para registro
    const handleRegister = async (e) => {
        e.preventDefault();
        setIsSubmitting(true);
        setError('');
        
        try {
            const response = await fetch('http://localhost:8002/create_user.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ username, password, email }),
            });
            
            const data = await response.json();
            
            if (!response.ok) throw new Error(data.error || 'Error en registro');
            
            // Auto-login después de registro
            handleSubmit(e);
            
        } catch (err) {
            setError(err.message);
        } finally {
            setIsSubmitting(false);
        }
    };
    
    return (
        <div className="App">
            <header className="App-header">
                <h1>{isRegistering ? 'Registro' : 'Login'} Universitario</h1>
                
                {successMessage && (
                    <div className="success-message">{successMessage}</div>
                )}

                <form onSubmit={isRegistering ? handleRegister : handleSubmit}>
                    <div className="form-group">
                        <input
                            type="text"
                            placeholder="Usuario"
                            value={username}
                            onChange={(e) => setUsername(e.target.value)}
                            required
                        />
                    </div>
                    
                    <div className="form-group">
                        <input
                            type="password"
                            placeholder="Contraseña"
                            value={password}
                            onChange={(e) => setPassword(e.target.value)}
                            required
                        />
                    </div>

                    {isRegistering && (
                        <div className="form-group">
                            <input
                                type="email"
                                placeholder="Correo electrónico"
                                value={email}
                                onChange={(e) => setEmail(e.target.value)}
                                required
                            />
                        </div>
                    )}

                    <button 
                        type="submit" 
                        disabled={isSubmitting}
                        className={isRegistering ? 'register-button' : ''}
                    >
                        {isSubmitting 
                            ? (isRegistering ? 'Registrando...' : 'Ingresando...')
                            : (isRegistering ? 'Registrarse' : 'Iniciar Sesión')
                        }
                    </button>
            {redirecting && (
                <div className="redirect-message">
                    Redireccionando a los sistemas... 
                    <br />
                    Si no funciona, <a href={data?.squirrelmail_url}>haz clic aquí</a>
                </div>
            )}
                    {error && <div className="error">{error}</div>}
                </form>

                <div className="auth-switch">
                    {isRegistering ? (
                        <p>
                            ¿Ya tienes cuenta? 
                            <button 
                                onClick={() => setIsRegistering(false)}
                                className="link-button"
                            >
                                Inicia sesión aquí
                            </button>
                        </p>
                    ) : (
                        <p>
                            ¿Necesitas una cuenta? 
                            <button 
                                onClick={() => setIsRegistering(true)}
                                className="link-button"
                            >
                                Regístrate aquí
                            </button>
                        </p>
                    )}
                </div>

                <div className="system-links">
                    <a 
                        href="http://localhost/moodle" 
                        target="_blank" 
                        rel="noopener noreferrer"
                    >
                        Acceso a Moodle
                    </a>
                    <a 
                        href="http://localhost/squirrelmail" 
                        target="_blank" 
                        rel="noopener noreferrer"
                    >
                        Acceso a Correo
                    </a>
                </div>
            </header>
        </div>
    );
}

export default App;