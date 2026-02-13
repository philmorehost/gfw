
import React, { useState, useEffect } from 'react';
import { fetchSportsData } from '../services/geminiService';

const Watch: React.FC = () => {
  const [aiAnalysis, setAiAnalysis] = useState('Syncing with recent footage...');
  const [isLoading, setIsLoading] = useState(true);

  useEffect(() => {
    loadAnalysis();
  }, []);

  const loadAnalysis = async () => {
    setIsLoading(true);
    try {
      const insight = await fetchSportsData('RESULTS', 'Recent Major European Matches and Tactical Highlights');
      setAiAnalysis(insight);
    } catch (e) {
      setAiAnalysis('Analysis stream interrupted.');
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <div className="container-fluid py-4">
      <div className="mb-5">
        <h1 className="display-4 font-condensed fw-black italic text-white mb-2">EDITORIAL VIDEO HUB</h1>
        <p className="text-electric-red font-condensed tracking-widest fw-black">ARCHIVED HIGHLIGHTS & TACTICAL ANALYTICS</p>
      </div>

      <div className="row g-4">
        <div className="col-xl-8">
          <div className="bg-black border border-white border-opacity-10 rounded-4 overflow-hidden shadow-2xl p-5 text-center min-vh-50 d-flex flex-column align-items-center justify-content-center">
             <i className="bi bi-camera-reels fs-1 text-electric-red mb-3"></i>
             <h2 className="h3 font-condensed fw-black italic text-white uppercase">ARCHIVE STREAMING READY</h2>
             <p className="text-white-50 font-condensed tracking-widest">Select a trending highlight below to initiate high-definition broadcast playback.</p>
          </div>
          
          <div className="mt-4 p-4 bg-dark bg-opacity-25 rounded-4 border border-white border-opacity-5">
             <h3 className="h5 font-condensed fw-black text-white italic mb-3">NOW TRENDING</h3>
             <div className="row g-3">
               {[1,2,3].map(i => (
                 <div key={i} className="col-md-4">
                    <div className="card bg-black border-white border-opacity-10 overflow-hidden cursor-pointer hover-shadow transition-all group">
                       <div className="ratio ratio-16x9">
                          <img src={`https://images.unsplash.com/photo-${i === 1 ? '1508098682722-e99c43a406b2' : i === 2 ? '1574629810360-7efbbe195018' : '1551958219-acbc608c6377'}?auto=format&fit=crop&q=80&w=400`} className="object-fit-cover group-hover-scale" />
                       </div>
                       <div className="p-3 text-center">
                         <h4 className="h6 text-white font-condensed mb-0">TACTICAL REVIEW: MATCHDAY {i+10}</h4>
                         <span className="text-electric-red fw-black italic font-condensed mt-1 d-block" style={{fontSize: '9px'}}>WATCH REPLAY →</span>
                       </div>
                    </div>
                 </div>
               ))}
             </div>
          </div>
        </div>

        <div className="col-xl-4">
          <div className="sticky-top" style={{ top: '120px' }}>
            <div className="card bg-black border-electric-red border-opacity-20 shadow-2xl rounded-4">
              <div className="card-header bg-dark border-bottom border-white border-opacity-5 p-4">
                <h2 className="h4 font-condensed fw-black italic text-white mb-0 d-flex align-items-center">
                  <span className="spinner-grow spinner-grow-sm text-danger me-3" role="status"></span>
                  AI VIDEO ANALYTICS
                </h2>
              </div>
              <div className="card-body p-4 min-vh-50 overflow-auto">
                {isLoading ? (
                  <div className="text-center py-5">
                    <div className="spinner-border text-danger" role="status"></div>
                  </div>
                ) : (
                  <div className="markdown-content text-white-50 small fs-6 lh-lg" style={{ whiteSpace: 'pre-wrap' }}>
                    {aiAnalysis}
                  </div>
                )}
              </div>
              <div className="card-footer bg-black border-top border-white border-opacity-5 p-3">
                <button onClick={loadAnalysis} className="btn btn-dark w-100 rounded-pill font-condensed fw-black tracking-widest py-2">REFRESH ANALYSIS</button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <style>{`
        .hover-shadow:hover { box-shadow: 0 0 30px rgba(255, 62, 62, 0.1); }
        .group-hover-scale { transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1); }
        .card:hover .group-hover-scale { transform: scale(1.1); }
      `}</style>
    </div>
  );
};

export default Watch;
