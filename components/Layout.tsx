
import React, { useState, useEffect } from 'react';
import { Link, useLocation } from 'react-router-dom';
import { SiteSettings, Post } from '../types';
import { INITIAL_SETTINGS, MOCK_POSTS } from '../constants';

interface LayoutProps {
  children: React.ReactNode;
}

const NewsTickerItem: React.FC<{ post: Post }> = ({ post }) => (
  <Link to={`/post/${post.id}`} className="news-ticker-item p-3 d-flex flex-column justify-content-center h-100 transition-all cursor-pointer hover:bg-white hover:bg-opacity-5 text-decoration-none border-end border-white border-opacity-10" style={{ minWidth: '240px' }}>
    <div className="d-flex align-items-center mb-1">
      <span className="badge bg-electric-red rounded-0 px-2 py-0.5 me-2 italic font-condensed fw-black" style={{ fontSize: '7px' }}>BREAKING</span>
      <span className="text-uppercase text-secondary fw-black italic" style={{ fontSize: '8px', letterSpacing: '1px' }}>{post.category}</span>
    </div>
    <h4 className="text-white font-condensed fw-black italic text-uppercase text-truncate mb-0" style={{ fontSize: '11px' }}>
      {post.title}
    </h4>
    <div className="mt-1 d-flex align-items-center opacity-50">
      <span className="fw-black text-white-50 uppercase" style={{ fontSize: '7px' }}>{post.date}</span>
    </div>
  </Link>
);

const Layout: React.FC<LayoutProps> = ({ children }) => {
  const [posts, setPosts] = useState<Post[]>([]);
  const location = useLocation();

  useEffect(() => {
    const storedPosts = localStorage.getItem('site_posts');
    if (storedPosts) {
      setPosts(JSON.parse(storedPosts));
    } else {
      setPosts(MOCK_POSTS);
    }
  }, [location.pathname]);

  const navItems = [
    { name: 'HOME', path: '/', icon: 'bi-house-door' },
    { name: 'WATCH', path: '/watch', icon: 'bi-play-circle' },
    { name: 'BETTING', path: '/betting', icon: 'bi-currency-dollar' },
    { name: 'TABLES', path: '/tables', icon: 'bi-table' },
    { name: 'STORIES', path: '/stories', icon: 'bi-newspaper' },
  ];

  const sportsFilter = [
    { name: 'UCL', icon: '⭐' },
    { name: 'EPL', icon: '⚽' },
    { name: 'LALIGA', icon: '🇪🇸' },
    { name: 'SERIEA', icon: '🇮🇹' },
    { name: 'TRANSFERS', icon: '💸' },
  ];

  return (
    <div className="d-flex flex-column min-vh-100 w-100">
      
      {/* HEADER: NEWS TICKER & MOBILE NAV */}
      <header className="sticky-top bg-black border-bottom border-white border-opacity-10 z-[1020]">
        <div className="d-flex align-items-stretch" style={{ height: '60px' }}>
          <button 
            className="btn btn-dark rounded-0 border-end border-white border-opacity-10 px-3 d-md-none" 
            type="button" 
            data-bs-toggle="offcanvas" 
            data-bs-target="#sidebarOffcanvas"
          >
            <i className="bi bi-list fs-4"></i>
          </button>

          <div className="flex-grow-1 d-flex overflow-x-auto no-scrollbar py-0">
            {posts.length > 0 ? posts.map((p) => (
              <NewsTickerItem key={p.id} post={p} />
            )) : (
               <div className="px-4 d-flex align-items-center text-muted font-condensed fw-black italic text-xs uppercase">SEARCHING WIRE...</div>
            )}
            <div className="p-3 d-flex align-items-center">
              <Link to="/stories" className="btn btn-dark border border-white border-opacity-10 rounded-0 text-nowrap fw-black italic px-3 py-1 hover:bg-electric-red transition-all" style={{ fontSize: '9px' }}>WIRE →</Link>
            </div>
          </div>
        </div>

        {/* SECONDARY SPORTS NAV */}
        <div className="bg-dark bg-opacity-30 border-top border-white border-opacity-5 py-1 px-2 overflow-x-auto no-scrollbar">
          <div className="d-flex gap-2">
            {sportsFilter.map((sport) => (
              <button key={sport.name} className="btn btn-sm btn-dark border border-white border-opacity-5 rounded-0 d-flex align-items-center px-3 py-1 hover:border-electric-red transition-all">
                <span className="me-2" style={{fontSize: '0.8rem'}}>{sport.icon}</span>
                <span className="fw-black text-uppercase text-secondary" style={{ fontSize: '9px', letterSpacing: '1px' }}>{sport.name}</span>
              </button>
            ))}
          </div>
        </div>
      </header>

      <div className="d-flex flex-grow-1 position-relative">
        {/* DESKTOP SIDEBAR */}
        <aside className="d-none d-md-flex flex-column bg-black border-end border-white border-opacity-5 overflow-y-auto" style={{ width: '240px', position: 'sticky', top: '95px', height: 'calc(100vh - 95px)' }}>
          <div className="p-4 mb-2">
            <Link to="/" className="text-decoration-none group">
              <h1 className="h2 font-condensed fw-black italic text-white mb-0 lh-1 tracking-tighter">
                <span className="text-electric-red group-hover:text-white transition-all">GLOBAL</span><br/>WATCH
              </h1>
              <div className="d-flex align-items-center mt-2">
                <div className="bg-electric-red me-2" style={{width: '12px', height: '2px'}}></div>
                <small className="text-uppercase text-white-50 fw-black italic ls-wider" style={{ fontSize: '9px' }}>ELITE V4.1</small>
              </div>
            </Link>
          </div>

          <nav className="nav flex-column mb-auto">
            {navItems.map((item) => (
              <Link 
                key={item.name} 
                to={item.path} 
                className={`nav-link px-4 py-3 fw-black text-uppercase tracking-widest transition-all italic border-start border-4 ${location.pathname === item.path ? 'text-white bg-white bg-opacity-5 border-electric-red' : 'text-secondary border-transparent hover:text-white hover:bg-white/5'}`}
                style={{ fontSize: '11px' }}
              >
                <i className={`bi ${item.icon} me-3`}></i>
                {item.name}
              </Link>
            ))}
          </nav>

          <div className="p-4 border-top border-white border-opacity-5">
            <Link to="/admin/login" className="nav-link text-white-50 hover:text-white fw-black text-uppercase italic ls-widest mb-3" style={{ fontSize: '9px' }}>ADMIN</Link>
            <div className="text-muted" style={{ fontSize: '8px' }}>
               <p className="mb-0 font-monospace uppercase opacity-50">GFW NETWORK © {new Date().getFullYear()}</p>
            </div>
          </div>
        </aside>

        {/* MAIN CONTENT CONTAINER */}
        <main className="flex-grow-1 bg-black min-vh-100 overflow-x-hidden">
          {children}
        </main>
      </div>

      {/* MOBILE OFFCANVAS SIDEBAR */}
      <div className="offcanvas offcanvas-start bg-black" tabIndex={-1} id="sidebarOffcanvas">
        <div className="offcanvas-header border-bottom border-white border-opacity-5 p-4">
          <h5 className="offcanvas-title font-condensed fw-black italic text-white h4 tracking-tighter">GLOBAL FOOTBALL</h5>
          <button type="button" className="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div className="offcanvas-body d-flex flex-column p-0">
          <nav className="nav flex-column mb-auto">
            {navItems.map((item) => (
              <Link 
                key={item.name} 
                to={item.path} 
                className={`nav-link fw-black text-uppercase py-4 px-4 h5 mb-0 italic border-bottom border-white border-opacity-5 ${location.pathname === item.path ? 'text-electric-red bg-white bg-opacity-5' : 'text-white'}`}
                onClick={() => {
                   const bsOffcanvas = (window as any).bootstrap.Offcanvas.getInstance(document.getElementById('sidebarOffcanvas'));
                   bsOffcanvas?.hide();
                }}
              >
                <i className={`bi ${item.icon} me-3`}></i>
                {item.name}
              </Link>
            ))}
          </nav>
          <div className="p-4 mt-auto border-top border-white border-opacity-5">
            <Link to="/admin/login" className="btn btn-outline-danger w-100 rounded-0 fw-black italic text-uppercase py-3">ADMIN ACCESS</Link>
          </div>
        </div>
      </div>

      <style>{`
        .news-ticker-item { border-right: 1px solid rgba(255,255,255,0.05); }
        .ls-widest { letter-spacing: 0.15em; }
        .border-transparent { border-color: transparent; }
        .min-vh-100 { min-height: 100vh; }
        @media (max-width: 768px) {
           header { height: auto !important; }
        }
      `}</style>
    </div>
  );
};

export default Layout;
