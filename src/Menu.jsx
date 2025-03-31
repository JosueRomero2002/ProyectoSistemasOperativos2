import React from 'react';
import { motion } from 'framer-motion';
import styled from 'styled-components';

const PortalContainer = styled.div`
  max-width: 1000px;
  margin: 0 auto;
  padding: 30px;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  color: #333;
`;

const Header = styled.div`
  background: linear-gradient(135deg, #0056b3 0%, #003366 100%);
  color: white;
  padding: 30px;
  border-radius: 10px;
  margin-bottom: 30px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
`;

const WelcomeTitle = styled.h1`
  font-size: 2.2rem;
  margin-bottom: 10px;
`;

const UserInfo = styled.div`
  background: white;
  color: #333;
  padding: 20px;
  border-radius: 8px;
  margin-top: 20px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
`;

const SectionTitle = styled.h2`
  font-size: 1.8rem;
  color: #0056b3;
  margin: 30px 0 20px;
  padding-bottom: 10px;
  border-bottom: 2px solid #eee;
`;

const ToolsGrid = styled.div`
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 20px;
  margin-bottom: 40px;
`;

const ToolCard = styled(motion.div)`
  background: white;
  border-radius: 8px;
  padding: 20px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  cursor: pointer;
  transition: all 0.3s ease;
  border-left: 4px solid ${props => props.color || '#0056b3'};

  &:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
  }
`;

const ToolTitle = styled.h3`
  font-size: 1.3rem;
  color: #0056b3;
  margin-bottom: 10px;
`;

const ToolDescription = styled.p`
  color: #666;
  margin-bottom: 15px;
`;

const ToolLink = styled.a`
  display: inline-block;
  padding: 8px 16px;
  background: ${props => props.color || '#0056b3'};
  color: white;
  border-radius: 4px;
  text-decoration: none;
  font-weight: bold;
  transition: all 0.2s ease;

  &:hover {
    opacity: 0.9;
    transform: translateY(-2px);
  }
`;

const LogoutButton = styled.button`
  margin-top: 10px;
  padding: 8px 16px;
  background: #e74c3c;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: bold;
  transition: all 0.2s ease;

  &:hover {
    background: #c0392b;
    transform: translateY(-2px);
  }
`;

const Menu = ({ user, onLogout }) => {
  const tools = [
    {
      id: 'moodle',
      title: 'Moodle UNITEC',
      description: 'Plataforma de aprendizaje en línea donde encontrarás tus cursos, materiales y actividades académicas.',
      color: '#f98012',
      url: 'http://localhost/moodle'
    },
    {
      id: 'squirrelmail',
      title: 'SquirrelMail',
      description: 'Accede a tu correo institucional a través de esta plataforma de correo web.',
      color: '#5d8a2a',
      url: 'http://localhost/squirrelmail'
    },
  ];

  const handleToolClick = (url) => {
    // Abre la herramienta en una nueva pestaña
    window.open(url, '_blank');
  };

  return (
    <PortalContainer>
      <Header>
        <WelcomeTitle>¡Hola {user?.name || 'Usuario'}!</WelcomeTitle>
        <p>Este es tu nuevo Portal de UNITEC.</p>
        
        <UserInfo>
          <p><strong>{user?.name || 'Usuario'}</strong></p>
          <p>UNITEC</p>
          <p>{user?.email || 'correo@unitec.edu'}</p>
          <LogoutButton onClick={onLogout}>
            Cerrar sesión
          </LogoutButton>
        </UserInfo>
      </Header>

      <SectionTitle>HERRAMIENTAS</SectionTitle>
      
      <ToolsGrid>
        {tools.map((tool) => (
          <ToolCard 
            key={tool.id}
            color={tool.color}
            whileHover={{ scale: 1.03 }}
            whileTap={{ scale: 0.98 }}
            onClick={() => handleToolClick(tool.url)}
          >
            <ToolTitle>{tool.title}</ToolTitle>
            <ToolDescription>{tool.description}</ToolDescription>
            <ToolLink 
              color={tool.color}
            >
              Acceder
            </ToolLink>
          </ToolCard>
        ))}
      </ToolsGrid>
    </PortalContainer>
  );
};

export default Menu;