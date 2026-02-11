
import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { Post, Category } from '../types';
import { fetchAndRefineNews } from '../services/geminiService';

const Home: React.FC = () => {
  const [posts, setPosts] = useState<Post[]>([]);
  const [isLoading, setIsLoading] = useState(true);

  useEffect(() => {
    const initData = async () => {
      setIsLoading(true);
      const stored = localStorage.getItem('site_posts');
      if (stored) {
        setPosts(JSON.parse(stored));
      } else {
        const categories = [Category.EPL, Category.TRANSFERS, Category.UCL];
        const allNews = await Promise.all(categories.map(c => fetchAndRefineNews(c, c)));
        const flattened = allNews.flat();
        setPosts(flattened);
        localStorage.setItem('site_posts', JSON.stringify(flattened));
      }
      setIsLoading(false);
    };
    initData();
  }, []);

  if (isLoading) return (
    <div className="d-flex flex-column align-items-center justify-content-center py-5 min-vh-50">
      <div className="spinner-border text-danger mb-3" role="status" style={{ width: '3rem', height: '3rem' }}></div>
      <p className="font-condensed fw-black text-muted text-uppercase ls-widest">Gathering Intel...</p>
    </div>
  );

  const featuredStories = posts.filter(p => p.isTopStory).slice(0, 3);
  const sideStories = posts.filter(p => !p.isTopStory).slice(0, 6);

  return (
    <div className="container-fluid py-2">
      
      {/* FEATURED STORIES */}
      <section className="mb-5">
        <div className="d-flex align-items-center mb-4 border-bottom border-danger border-4 pb-1 d-inline-block">
          <h2 className="h4 font-condensed fw-black italic text-white mb-0 text-uppercase">FEATURED STORIES</h2>
        </div>

        <div className="row g-4">
          <div className="col-lg-8">
            <div className="row g-4">
              {featuredStories.map((post, idx) => (
                <div key={post.id} className={idx === 0 ? "col-12" : "col-md-6"}>
                  <Link to={`/post/${post.id}`} className="card border-0 rounded-0 overflow-hidden text-decoration-none group shadow-lg position-relative" style={{ height: idx === 0 ? '450px' : '350px' }}>
                    <img src={post.image} className="card-img h-100 object-fit-cover brightness-50 transition-transform" alt="" />
                    <div className="card-img-overlay d-flex flex-column justify-content-end p-4">
                      <span className="badge bg-electric-red rounded-0 mb-3 align-self-start fw-black italic text-uppercase ls-widest">{post.category}</span>
                      <h3 className={`card-title text-white font-condensed fw-black text-uppercase italic ${idx === 0 ? 'display-5' : 'h3'} lh-1 mb-0`}>
                        {post.title}
                      </h3>
                    </div>
                  </Link>
                </div>
              ))}
            </div>
          </div>

          <div className="col-lg-4">
            <div className="border-bottom border-white border-opacity-10 mb-4 pb-2">
              <h3 className="h5 font-condensed fw-black text-white italic text-uppercase ls-wider">MORE STORIES</h3>
            </div>
            <div className="list-group list-group-flush bg-transparent">
              {sideStories.map(post => (
                <Link key={post.id} to={`/post/${post.id}`} className="list-group-item list-group-item-action bg-transparent border-0 px-0 mb-3">
                  <div className="d-flex align-items-start gap-3">
                    <div className="bg-danger mt-1" style={{ width: '8px', height: '8px', flexShrink: 0 }}></div>
                    <div>
                      <h4 className="h6 text-white fw-bold text-uppercase mb-1 line-clamp-2">{post.title}</h4>
                      <div className="d-flex align-items-center gap-2">
                        <span className="text-electric-red fw-black text-uppercase" style={{ fontSize: '9px' }}>{post.category}</span>
                        <span className="text-muted fw-bold text-uppercase" style={{ fontSize: '9px' }}>• {post.date}</span>
                      </div>
                    </div>
                  </div>
                </Link>
              ))}
            </div>
          </div>
        </div>
      </section>

      {/* LATEST NEWS GRID */}
      <section className="mb-5">
        <div className="border-bottom border-white border-opacity-10 mb-4 pb-1">
          <h2 className="h5 font-condensed fw-black italic text-white text-uppercase">LATEST UPDATES</h2>
        </div>
        <div className="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4">
          {posts.slice(0, 8).map(post => (
            <div key={post.id} className="col">
              <Link to={`/post/${post.id}`} className="card border-0 rounded-0 bg-transparent text-decoration-none h-100 group">
                <div className="position-relative overflow-hidden mb-3" style={{ height: '180px' }}>
                  <img src={post.image} className="w-100 h-100 object-fit-cover transition-transform" alt="" />
                  <span className="position-absolute top-0 start-0 m-2 badge bg-dark bg-opacity-75 rounded-0 fw-black text-uppercase" style={{ fontSize: '8px' }}>{post.category}</span>
                </div>
                <div className="card-body p-0">
                  <h3 className="h6 text-white fw-bold text-uppercase line-clamp-2 group-hover-red mb-2">{post.title}</h3>
                  <p className="text-muted fw-bold text-uppercase mb-0" style={{ fontSize: '10px', letterSpacing: '1px' }}>{post.date} • {post.author}</p>
                </div>
              </Link>
            </div>
          ))}
        </div>
      </section>

    </div>
  );
};

export default Home;
