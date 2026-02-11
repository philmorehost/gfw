
import React, { useState, useEffect } from 'react';
import { Link, useLocation } from 'react-router-dom';
import { SiteSettings, Match } from '../types';
import { INITIAL_SETTINGS, MOCK_MATCHES } from '../constants';

interface LayoutProps {
  children: React.ReactNode;
}

const FixtureCard: React.FC<{ match: Match }> = ({ match }) => (
  <div className="fixture-card p-3 d-flex flex-column justify-content-between h-100 transition-all cursor-pointer">
    <div>
      <span className="d-block text-uppercase text-secondary fw-black" style={{ fontSize: '9px', letterSpacing: '1px' }}>{match.league}</span>
    </div>
    <div className="my-2">
      <div className="d-flex align-items-center justify-content-between mb-1">
        <div className="d-flex align-items-center">
          <div className="bg-dark rounded-1 me-2 d-flex align-items-center justify-content-center" style={{ width: '20px', height: '20px' }}>
            <span className="text-muted fw-bold" style={{ fontSize: '8px' }}>H</span>
          </div>
          <span className="text-uppercase fw-black text-white text-truncate" style={{ fontSize: '11px', maxWidth: '80px' }}>{match.homeTeam}</span>
        </div>
        <span className="fw-black text-white">{match.homeScore ?? ''}</span>
      </div>
      <div className="d-flex align-items-center justify-content-between">
        <div className="d-flex align-items-center">
          <div className="bg-dark rounded-1 me-2 d-flex align-items-center justify-content-center" style={{ width: '20px', height: '20px' }}>
            <span className="text-muted fw-bold" style={{ fontSize: '8px' }}>A</span>
          </div>
          <span className="text-uppercase fw-black text-white text-truncate" style={{ fontSize: '11px', maxWidth: '80px' }}>{match.awayTeam}</span>
        </div>
        <span className="fw-black text-white">{match.awayScore ?? ''}</span>
      </div>
    </div>
    <div className="pt-2 border-top border-secondary border-opacity-10 d-flex justify-content-between align-items-center">
      <span className="text-uppercase text-muted fw-bold" style={{ fontSize: '9px' }}>{match.status === 'FINISHED' ? 'FINAL' : match.time}</span>
      <span className="badge bg-secondary bg-opacity-10 text-white-50" style={{ fontSize: '8px' }}>GFW</span>
    </div>
  </div>
);

const Layout: React.FC<LayoutProps> = ({ children }) => {
  const [settings, setSettings] = useState<SiteSettings>(INITIAL_SETTINGS);
  const location = useLocation();

  useEffect(() => {
    const storedSettings = localStorage.getItem('site_settings');
    if (storedSettings) setSettings(JSON.parse(storedSettings));
  }, []);

  const navItems = [
    { name: 'SCORES', path: '/', icon: 'bi-speedometer2' },
    { name: 'WATCH', path: '/watch', icon: 'bi-play-circle' },
    { name: 'BETTING', path: '/betting', icon: 'bi-currency-dollar' },
    { name: 'STORIES', path: '/stories', icon: 'bi-newspaper' },
  ];

  const sportsFilter = [
    { name: 'NFL', icon: '🏈' },
    { name: 'FIFA 2026', icon: '⚽' },
    { name: 'NCAABK', icon: '🏀' },
    { name: 'INDYCAR', icon: '🏎️' },
    { name: 'NASCAR', icon: '🏁' },
    { name: 'LIV', icon: '⛳' },
    { name: 'MLB', icon: '⚾' },
  ];

  return (
    <div className="d-flex flex-column min-vh-screen">
      
      {/* HEADER: FIXTURES & MOBILE NAV */}
      <header className="sticky-top bg-black border-bottom border-white border-opacity-10 z-3">
        <div className="d-flex align-items-stretch">
          <button 
            className="btn btn-dark rounded-0 border-end border-white border-opacity-10 px-3 d-md-none" 
            type="button" 
            data-bs-toggle="offcanvas" 
            data-bs-target="#sidebarOffcanvas"
          >
            <i className="bi bi-list fs-3"></i>
          </button>

          <div className="flex-grow-1 d-flex overflow-x-auto no-scrollbar py-1">
            {MOCK_MATCHES.map((m) => (
              <FixtureCard key={m.id} match={m} />
            ))}
            <div className="fixture-card p-3 d-flex align-items-center">
              <button className="btn btn-outline-light btn-sm rounded-pill text-nowrap fw-black" style={{ fontSize: '10px' }}>ALL SCORES</button>
            </div>
          </div>
        </div>

        {/* SECONDARY SPORTS NAV */}
        <div className="bg-dark bg-opacity-50 border-top border-white border-opacity-5 py-2 px-3 overflow-x-auto no-scrollbar">
          <div className="d-flex gap-2">
            {sportsFilter.map((sport) => (
              <button key={sport.name} className="btn btn-sm btn-dark border border-white border-opacity-10 rounded-pill d-flex align-items-center px-3 hover-shadow">
                <span className="me-2">{sport.icon}</span>
                <span className="fw-black text-uppercase text-secondary" style={{ fontSize: '9px' }}>{sport.name}</span>
              </button>
            ))}
          </div>
        </div>
      </header>

      <div className="d-flex flex-grow-1">
        {/* DESKTOP SIDEBAR */}
        <aside className="d-none d-md-flex flex-column bg-dark border-end border-white border-opacity-5" style={{ width: '240px', position: 'sticky', top: '120px', height: 'calc(100vh - 120px)' }}>
          <div className="p-4 mb-4">
            <Link to="/" className="text-decoration-none">
              <h1 className="h2 font-condensed fw-black italic text-white mb-0 lh-1">
                <span className="text-electric-red">GLOBAL</span><br/>FOOTBALL
              </h1>
              <small className="text-uppercase text-muted fw-black ls-wider" style={{ fontSize: '10px' }}>WATCH</small>
            </Link>
          </div>

          <nav className="nav flex-column mb-auto">
            {navItems.map((item) => (
              <Link 
                key={item.name} 
                to={item.path} 
                className={`nav-link px-4 py-3 fw-black text-uppercase ls-widest ${location.pathname === item.path ? 'text-electric-red' : 'text-secondary hover-white'}`}
                style={{ fontSize: '11px' }}
              >
                {item.name}
              </Link>
            ))}
          </nav>

          <div className="p-4 border-top border-white border-opacity-5">
            <Link to="/admin/login" className="nav-link text-secondary fw-black text-uppercase ls-widest mb-3" style={{ fontSize: '10px' }}>ADMIN PANEL</Link>
            <p className="text-muted mb-0" style={{ fontSize: '9px' }}>© {new Date().getFullYear()} GFW NETWORK</p>
          </div>
        </aside>

        {/* MAIN CONTENT CONTAINER */}
        <main className="flex-grow-1 p-3 p-md-5 container-fluid">
          {children}
        </main>
      </div>

      {/* MOBILE OFFCANVAS SIDEBAR */}
      <div className="offcanvas offcanvas-start" tabIndex={-1} id="sidebarOffcanvas">
        <div className="offcanvas-header border-bottom border-white border-opacity-5">
          <h5 className="offcanvas-title font-condensed fw-black italic">GLOBAL FOOTBALL</h5>
          <button type="button" className="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div className="offcanvas-body d-flex flex-column">
          <nav className="nav flex-column mb-auto">
            {navItems.map((item) => (
              <Link 
                key={item.name} 
                to={item.path} 
                className="nav-link text-white fw-black text-uppercase py-3"
                onClick={() => {
                   const bsOffcanvas = (window as any).bootstrap.Offcanvas.getInstance(document.getElementById('sidebarOffcanvas'));
                   bsOffcanvas?.hide();
                }}
              >
                {item.name}
              </Link>
            ))}
          </nav>
          <div className="pt-3 border-top border-white border-opacity-5">
            <Link to="/admin/login" className="nav-link text-secondary fw-black text-uppercase" style={{ fontSize: '12px' }}>ADMIN LOGIN</Link>
          </div>
        </div>
      </div>

      {/* WHATSAPP FLOAT */}
      <a 
        href={`https://wa.me/${settings.whatsappNumber}`} 
        target="_blank" 
        className="position-fixed bottom-0 end-0 m-4 btn btn-success rounded-circle shadow-lg p-3 z-3 d-flex align-items-center justify-content-center"
        style={{ width: '60px', height: '60px' }}
      >
        <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16"><path d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.898 7.898 0 0 0 13.6 2.326zM7.994 14.521a6.573 6.573 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.557 6.557 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592zm3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.729.729 0 0 0-.529.247c-.182.198-.691.677-.691 1.654 0 .977.71 1.916.81 2.049.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232z"/></svg>
      </a>
    </div>
  );
};

export default Layout;
