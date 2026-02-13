
import React, { useState, useEffect, useRef } from 'react';
import { Link } from 'react-router-dom';
import { Post, Category, AIModel } from '../types';
import { fetchAndRefineNews, fetchSportsData } from '../services/geminiService';
import { INITIAL_SETTINGS } from '../constants';

const COMPETITIONS = [
  'UEFA Champions League',
  'English Premier League',
  'UEFA Europa League',
  'Spanish La Liga',
  'Italian Serie A'
];

const DATA_TYPES = [
  { id: 'LIVESCORE', label: 'LIVE UPDATES', icon: 'bi-broadcast', explanation: 'Real-time pitch metrics and tactical shifts.' },
  { id: 'RESULTS', label: 'FULL TIME', icon: 'bi-check-circle', explanation: 'Comprehensive post-match breakdowns.' },
  { id: 'SCHEDULE', label: 'UPCOMING', icon: 'bi-calendar-event', explanation: 'Strategic lookahead at future fixtures.' },
  { id: 'STATS', label: 'PERFORMANCE', icon: 'bi-graph-up', explanation: 'Deep-dive analytical data points.' },
  { id: 'LINEUPS', label: 'TEAM SHEETS', icon: 'bi-people', explanation: 'Confirmed tactical formations.' },
  { id: 'ODDS', label: 'MARKET PRICES', icon: 'bi-coin', explanation: 'Global probability metrics.' }
] as const;

const Home: React.FC = () => {
  const [posts, setPosts] = useState<Post[]>([]);
  const [activeComp, setActiveComp] = useState(COMPETITIONS[1]); // Default to EPL
  const [activeDataType, setActiveDataType] = useState<typeof DATA_TYPES[number]['id']>('LIVESCORE');
  const [sportsData, setSportsData] = useState<string>('');
  const [isRefreshing, setIsRefreshing] = useState(false);
  const [selectedModel, setSelectedModel] = useState<AIModel>(INITIAL_SETTINGS.selectedModel);
  const refreshTimer = useRef<number | null>(null);

  useEffect(() => {
    const storedSettings = localStorage.getItem('site_settings');
    if (storedSettings) {
      const settings = JSON.parse(storedSettings);
      setSelectedModel(settings.selectedModel || INITIAL_SETTINGS.selectedModel);
    }
    
    initNews();
    loadSportsData();

    refreshTimer.current = window.setInterval(() => {
      loadSportsData(true);
    }, 60000);

    return () => {
      if (refreshTimer.current) clearInterval(refreshTimer.current);
    };
  }, []);

  useEffect(() => {
    loadSportsData();
  }, [activeComp, activeDataType]);

  const initNews = async () => {
    const stored = localStorage.getItem('site_posts');
    if (stored) {
      setPosts(JSON.parse(stored));
    } else {
      const allNews = await fetchAndRefineNews(Category.EPL, Category.EPL, selectedModel);
      setPosts(allNews);
      localStorage.setItem('site_posts', JSON.stringify(allNews));
    }
  };

  const loadSportsData = async (silent = false) => {
    if (!silent) setIsRefreshing(true);
    try {
      const data = await fetchSportsData(activeDataType, activeComp, selectedModel);
      setSportsData(data);
    } catch (e) {
      setSportsData("### CONNECTION ERROR\nAI broadcast failed. Please verify your connection.");
    } finally {
      setIsRefreshing(false);
    }
  };

  const featuredStories = posts.filter(p => p.isTopStory).slice(0, 1);
  const secondaryStories = posts.filter(p => !p.isTopStory).slice(0, 3);
  const latestNews = posts.filter(p => !p.isTopStory).slice(3, 11);

  return (
    <div className="container-fluid py-0 px-0 overflow-x-hidden">
      
      {/* SECTION 1: ENHANCED EDITORIAL HERO */}
      <div className="row g-0 mb-5 border-bottom border-white border-opacity-10 bg-black min-vh-75 position-relative">
        {featuredStories.length > 0 && (
          <div className="position-absolute top-0 start-0 w-100 h-100 opacity-20 pointer-events-none d-none d-lg-block">
            <img src={featuredStories[0].image} className="w-100 h-100 object-fit-cover grayscale" alt="" />
          </div>
        )}
        <div className="col-lg-8 border-end-lg border-white border-opacity-10 z-1 d-flex flex-column">
          <div className="p-4 p-md-5 h-100 d-flex flex-column justify-content-center">
             <div className="d-flex align-items-center mb-4 flex-wrap gap-2">
               <span className="badge bg-electric-red rounded-0 px-3 py-2 italic font-condensed tracking-tighter fw-black">TOP EDITORIAL PICK</span>
               <div className="bg-white bg-opacity-10 px-3 py-1 text-white-50 font-monospace" style={{fontSize: '10px'}}>BROADCAST ID: {Math.random().toString(16).slice(2, 8).toUpperCase()}</div>
             </div>
             {featuredStories.length > 0 ? (
               <Link to={`/post/${featuredStories[0].id}`} className="text-decoration-none group">
                 <h1 className="display-1 font-condensed fw-black text-white italic text-uppercase lh-1 mb-4 group-hover:text-[#ff3e3e] transition-all sharp-text tracking-tighter">
                   {featuredStories[0].title}
                 </h1>
                 <p className="lead text-white text-opacity-50 fw-bold text-uppercase fs-3 mb-0 opacity-85 line-clamp-2 max-w-3xl d-none d-md-block">
                   {featuredStories[0].excerpt}
                 </p>
                 <div className="mt-4 mt-md-5">
                   <span className="btn btn-outline-light rounded-0 px-4 px-md-5 py-3 font-condensed fw-black italic tracking-widest hover:bg-electric-red hover:border-electric-red">READ FULL EXCLUSIVE →</span>
                 </div>
               </Link>
             ) : (
               <div className="placeholder-glow">
                 <span className="placeholder col-12 bg-secondary bg-opacity-10 mb-4 py-5 d-block"></span>
                 <span className="placeholder col-8 bg-secondary bg-opacity-10 py-4 d-block"></span>
               </div>
             )}
          </div>
        </div>
        <div className="col-lg-4 bg-[#0a0e17] shadow-inner position-relative z-1 border-top border-lg-0 border-white border-opacity-10">
           <div className="p-4 h-100 d-flex flex-column">
              <div className="d-flex justify-content-between align-items-center mb-4 border-bottom border-white border-opacity-5 pb-3">
                <h3 className="h6 font-condensed tracking-widest text-electric-red mb-0 d-flex align-items-center fw-black">
                  FOLLOWING NEXT
                </h3>
              </div>
              
              <div className="space-y-4">
                 {secondaryStories.map(post => (
                   <Link key={post.id} to={`/post/${post.id}`} className="d-block text-decoration-none group mb-4">
                      <div className="d-flex gap-3 align-items-center">
                         <div className="flex-shrink-0 w-20 h-14 w-md-24 h-md-16 overflow-hidden bg-dark">
                            <img src={post.image} className="w-100 h-100 object-fit-cover group-hover:scale-110 transition-transform" alt="" />
                         </div>
                         <div>
                            <span className="text-electric-red fw-black italic font-condensed" style={{fontSize: '9px'}}>{post.category.toUpperCase()}</span>
                            <h4 className="text-white font-condensed fw-black italic text-uppercase text-xs text-md-sm line-clamp-2 mt-1 group-hover:text-electric-red transition-all lh-1">{post.title}</h4>
                         </div>
                      </div>
                   </Link>
                 ))}
              </div>

              <div className="mt-auto pt-4 border-top border-white border-opacity-5">
                <p className="text-white-50 font-condensed fw-black italic tracking-widest mb-2" style={{fontSize: '11px'}}>LIVE BROADCASTING CHANNELS:</p>
                <div className="d-flex gap-2 overflow-x-auto no-scrollbar pb-2">
                   {COMPETITIONS.slice(0, 3).map(c => (
                     <span key={c} className="badge bg-dark border border-white border-opacity-10 text-muted px-3 py-2 rounded-0 text-nowrap">{c.toUpperCase()}</span>
                   ))}
                </div>
              </div>
           </div>
        </div>
      </div>

      {/* SECTION 2: AI SPORTS HUB */}
      <section className="mb-5 bg-[#0a0e17] p-3 p-md-5 rounded-md-4 border border-white border-opacity-5 mx-2 shadow-2xl overflow-hidden position-relative">
        <div className="position-absolute top-0 end-0 p-4 opacity-5 d-none d-md-block">
           <i className="bi bi-cpu" style={{fontSize: '12rem'}}></i>
        </div>
        
        <div className="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 mb-md-5 gap-3 relative z-1">
          <div>
            <h2 className="h2 h1-md font-condensed fw-black italic text-white mb-0 d-flex align-items-center">
              AI HUB
              <span className="badge bg-electric-red text-white ms-3 font-monospace fw-normal" style={{fontSize: '10px'}}>v3.0_LIVE</span>
            </h2>
            <p className="text-white text-opacity-40 font-condensed tracking-widest fw-black mb-0 uppercase" style={{fontSize: '10px'}}>Powered by {selectedModel.toUpperCase()}</p>
          </div>
          <div className="d-flex gap-2 overflow-x-auto no-scrollbar pb-2">
            {COMPETITIONS.map(comp => (
              <button 
                key={comp} 
                onClick={() => setActiveComp(comp)}
                className={`btn btn-sm px-3 py-2 rounded-0 font-condensed tracking-tighter fw-black border-2 transition-all text-nowrap ${activeComp === comp ? 'btn-danger border-danger' : 'btn-outline-secondary border-secondary opacity-50'}`}
              >
                {comp}
              </button>
            ))}
          </div>
        </div>

        <div className="row g-4 relative z-1">
          <div className="col-md-5 col-xl-4">
             {/* Mobile View: Horizontal scrolling tabs, Desktop: Vertical list */}
             <div className="d-flex d-md-block overflow-x-auto no-scrollbar gap-2 mb-3">
                {DATA_TYPES.map(type => (
                  <button 
                    key={type.id} 
                    onClick={() => setActiveDataType(type.id)}
                    className={`nav-link text-start rounded-0 py-2 py-md-3 px-3 px-md-4 font-condensed tracking-widest fw-black d-flex flex-column transition-all border-l-4 text-nowrap mb-0 mb-md-3 ${activeDataType === type.id ? 'active bg-electric-red text-white border-white' : 'text-secondary bg-white bg-opacity-5 hover-white border-transparent'}`}
                  >
                    <div className="d-flex align-items-center justify-content-between w-full mb-1 gap-4">
                      <span className="h6 mb-0">{type.label}</span>
                      <i className={`bi ${type.icon} fs-6 d-none d-md-block`}></i>
                    </div>
                    <span className={`text-[9px] font-bold tracking-normal uppercase d-none d-md-block ${activeDataType === type.id ? 'text-white/80' : 'text-gray-500'}`}>
                      {type.explanation}
                    </span>
                  </button>
                ))}
             </div>
             
             <div className="bg-black bg-opacity-40 p-3 border-start border-electric-red border-3 d-none d-md-block">
                <small className="fw-black text-uppercase text-muted d-block mb-2 tracking-widest" style={{fontSize: '9px'}}>SYNC STATUS</small>
                <div className="d-flex align-items-center">
                  <div className={`spinner-border spinner-border-sm text-danger me-3 ${isRefreshing ? '' : 'opacity-25'}`} role="status"></div>
                  <span style={{fontSize: '11px'}} className="fw-black uppercase tracking-tight text-white-50">POLLING DATA...</span>
                </div>
             </div>
          </div>
          
          <div className="col-md-7 col-xl-8">
             <div className="bg-black bg-opacity-60 p-3 p-md-5 rounded-0 border border-white border-opacity-5 min-vh-50 shadow-inner position-relative overflow-hidden backdrop-blur-sm">
                {isRefreshing ? (
                  <div className="d-flex flex-column align-items-center justify-content-center h-100 py-5">
                    <div className="spinner-grow text-danger mb-4" style={{width: '2.5rem', height: '2.5rem'}} role="status"></div>
                    <p className="font-condensed tracking-widest text-white fw-black italic h5">DECRYPTING...</p>
                  </div>
                ) : (
                  <div className="markdown-content text-white opacity-85 fs-6 lh-lg" style={{ whiteSpace: 'pre-wrap' }}>
                    {sportsData}
                  </div>
                )}
             </div>
          </div>
        </div>
      </section>

      {/* SECTION 3: EDITORIAL NEWS GRID */}
      <section className="p-3 p-md-5 bg-black">
        <div className="d-flex align-items-center justify-content-between mb-5 flex-wrap gap-3">
          <div className="d-flex align-items-center">
            <div className="bg-electric-red" style={{width: '6px', height: '40px'}}></div>
            <h2 className="h2 h1-md font-condensed fw-black italic text-white mb-0 ms-3 tracking-tighter">ELITE REPORTING</h2>
          </div>
          <Link to="/stories" className="text-electric-red font-condensed fw-black tracking-widest text-decoration-none border-bottom border-electric-red border-2 pb-1 hover:text-white hover:border-white transition-all text-nowrap">VIEW ALL</Link>
        </div>

        <div className="row g-3 g-md-4">
          {latestNews.length > 0 ? latestNews.map(post => (
            <div key={post.id} className="col-6 col-md-4 col-lg-3">
              <Link to={`/post/${post.id}`} className="card h-100 bg-transparent border-0 text-decoration-none group shadow-hover transition-all">
                <div className="ratio ratio-16x9 mb-2 mb-md-3 overflow-hidden rounded-0 border border-white border-opacity-10 position-relative">
                  <img src={post.image} className="object-fit-cover transition-transform duration-500 group-hover-scale" alt="" />
                  <div className="position-absolute top-0 start-0 m-2">
                    <span className="badge bg-electric-red font-condensed italic fw-black px-2 py-1" style={{fontSize: '8px'}}>{post.category.substring(0, 10).toUpperCase()}</span>
                  </div>
                </div>
                <div className="card-body p-0">
                  <h3 className="h6 text-white fw-black text-uppercase line-clamp-2 mb-1 group-hover:text-electric-red transition-all lh-1 italic" style={{fontSize: '0.85rem'}}>{post.title}</h3>
                  <div className="d-flex align-items-center gap-2 mt-2 opacity-50">
                    <span className="text-white fw-black uppercase tracking-tighter" style={{fontSize: '9px'}}>{post.author}</span>
                  </div>
                </div>
              </Link>
            </div>
          )) : (
            [...Array(4)].map((_, i) => (
              <div key={i} className="col-6 col-md-3 placeholder-glow">
                <div className="ratio ratio-16x9 mb-3 bg-dark bg-opacity-20 rounded-0 placeholder"></div>
                <div className="placeholder col-10 bg-dark mb-2"></div>
                <div className="placeholder col-6 bg-dark"></div>
              </div>
            ))
          )}
        </div>
      </section>

      {/* BROADCAST TICKER */}
      <div className="bg-electric-red py-2 py-md-3 overflow-hidden position-sticky bottom-0 z-3 shadow-2xl border-top border-white border-opacity-20">
        <div className="d-flex align-items-center">
          <span className="bg-black text-white px-3 px-md-5 py-2 font-condensed fw-black italic tracking-widest me-3 z-1 shadow-2xl skew-x-[-12deg] ms-2 d-none d-sm-block" style={{fontSize: '11px'}}>FLASH NEWS</span>
          <div className="marquee font-condensed text-white fw-black italic tracking-widest uppercase fs-6 fs-md-5">
            {posts.map(p => `• ${p.title.toUpperCase()} • `).join(' ')}
          </div>
        </div>
      </div>

      <style>{`
        .group-hover-scale { transition: transform 0.6s cubic-bezier(0.165, 0.84, 0.44, 1); }
        .group:hover .group-hover-scale { transform: scale(1.1); }
        .sharp-text { text-shadow: 2px 2px 0px rgba(0,0,0,0.8); }
        .min-vh-50 { min-height: 50vh; }
        .min-vh-75 { min-height: 75vh; }
        .marquee {
          white-space: nowrap;
          animation: marquee 80s linear infinite;
        }
        @keyframes marquee {
          0% { transform: translateX(5%); }
          100% { transform: translateX(-100%); }
        }
        @media (min-width: 992px) {
          .border-end-lg { border-right: 1px solid rgba(255,255,255,0.1) !important; }
        }
        .markdown-content h1, .markdown-content h2, .markdown-content h3 {
          font-family: 'Barlow Condensed', sans-serif;
          color: white;
          font-weight: 800;
          font-style: italic;
          text-transform: uppercase;
          border-bottom: 2px solid rgba(255,62,62,0.4);
          padding-bottom: 8px;
          margin-top: 24px;
          margin-bottom: 12px;
          font-size: 1.25rem;
        }
        .markdown-content p {
          margin-bottom: 1rem;
          color: rgba(255,255,255,0.85);
          font-size: 0.95rem;
        }
        .markdown-content strong {
          color: #ff3e3e;
          font-weight: 900;
        }
      `}</style>

    </div>
  );
};

export default Home;
