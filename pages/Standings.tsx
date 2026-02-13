
import React, { useState, useEffect } from 'react';
import { fetchSportsData } from '../services/geminiService';

const Standings: React.FC = () => {
  const [activeLeague, setActiveLeague] = useState('Premier League');
  const [data, setData] = useState('');
  const [loading, setLoading] = useState(true);

  const leagues = [
    'Premier League',
    'La Liga',
    'Serie A',
    'Bundesliga',
    'Ligue 1',
    'Champions League'
  ];

  useEffect(() => {
    loadStandings();
  }, [activeLeague]);

  const loadStandings = async () => {
    setLoading(true);
    try {
      const result = await fetchSportsData('STATS', `${activeLeague} League Table`);
      setData(result);
    } catch (e) {
      setData('Table data currently offline.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="container-fluid py-4">
      <div className="mb-5">
        <h1 className="display-4 font-condensed fw-black italic text-white mb-2">LEAGUE TABLES</h1>
        <p className="text-electric-red font-condensed tracking-widest fw-black">REAL-TIME RANKINGS & STATISTICS</p>
      </div>

      <div className="d-flex overflow-x-auto no-scrollbar gap-2 mb-5">
        {leagues.map(l => (
          <button 
            key={l}
            onClick={() => setActiveLeague(l)}
            className={`btn btn-sm px-4 py-2 rounded-0 font-condensed tracking-tighter fw-black border-2 transition-all ${activeLeague === l ? 'btn-danger border-danger' : 'btn-outline-secondary border-secondary opacity-50'}`}
          >
            {l.toUpperCase()}
          </button>
        ))}
      </div>

      <div className="bg-[#0a0e17] rounded-4 border border-white border-opacity-10 overflow-hidden shadow-2xl">
         <div className="p-4 bg-dark border-bottom border-white border-opacity-5 d-flex align-items-center">
            <div className="bg-electric-red me-3" style={{width: '4px', height: '20px'}}></div>
            <h2 className="h5 font-condensed fw-black text-white italic mb-0 uppercase">{activeLeague} CLASSIFICATION</h2>
         </div>
         <div className="p-4 p-md-5">
            {loading ? (
              <div className="text-center py-5">
                <div className="spinner-border text-danger" role="status"></div>
              </div>
            ) : (
              <div className="markdown-content text-white opacity-75 fs-6 lh-lg" style={{ whiteSpace: 'pre-wrap' }}>
                {data}
              </div>
            )}
         </div>
      </div>
    </div>
  );
};

export default Standings;
