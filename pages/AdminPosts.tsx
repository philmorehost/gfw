
import React, { useState, useEffect } from 'react';
import { Post, Category, Comment } from '../types';
import { MOCK_POSTS } from '../constants';
import { compressImage } from '../services/imageService';
import { generateNewsArticle, generatePostImage } from '../services/geminiService';
import { sendSubscriberNotification } from '../services/mailService';
import { autoPostToSocials } from '../services/socialService';

const AdminPosts: React.FC = () => {
  const [posts, setPosts] = useState<Post[]>([]);
  const [comments, setComments] = useState<Comment[]>([]);
  const [isEditing, setIsEditing] = useState(false);
  const [currentPost, setCurrentPost] = useState<Partial<Post>>({ seo: { metaTitle: '', metaDescription: '', keywords: '' }, tags: [] });
  const [loadingAI, setLoadingAI] = useState(false);
  const [loadingImage, setLoadingImage] = useState(false);
  const [imagePrompt, setImagePrompt] = useState('');
  const [showImageDialog, setShowImageDialog] = useState(false);
  const [selectedPosts, setSelectedPosts] = useState<Set<string>>(new Set());
  const [sortOrder, setSortOrder] = useState<'asc' | 'desc'>('desc');
  const [bulkCategory, setBulkCategory] = useState<Category | ''>('');

  useEffect(() => {
    const storedPosts = localStorage.getItem('site_posts');
    const storedComments = localStorage.getItem('site_comments');
    
    if (storedPosts) {
      setPosts(JSON.parse(storedPosts));
    } else {
      localStorage.setItem('site_posts', JSON.stringify(MOCK_POSTS));
      setPosts(MOCK_POSTS);
    }

    if (storedComments) {
      setComments(JSON.parse(storedComments));
    }
  }, []);

  const handleSave = async () => {
    let updatedPosts;
    const postToSave = {
      ...currentPost,
      id: currentPost.id || Date.now().toString(),
      date: currentPost.date || new Date().toISOString().split('T')[0],
      author: 'Admin'
    } as Post;

    if (currentPost.id) {
      updatedPosts = posts.map(p => p.id === currentPost.id ? postToSave : p);
    } else {
      updatedPosts = [postToSave, ...posts];
      
      const subsStored = localStorage.getItem('subscribers');
      const subs = subsStored ? JSON.parse(subsStored) : [];
      await sendSubscriberNotification(postToSave, subs);
      await autoPostToSocials(postToSave);
    }

    setPosts(updatedPosts);
    localStorage.setItem('site_posts', JSON.stringify(updatedPosts));
    setIsEditing(false);
    setCurrentPost({ seo: { metaTitle: '', metaDescription: '', keywords: '' }, tags: [] });
    setSelectedPosts(new Set());
  };

  const handleBulkDelete = () => {
    if (confirm(`Delete ${selectedPosts.size} posts?`)) {
      const updated = posts.filter(p => !selectedPosts.has(p.id));
      setPosts(updated);
      localStorage.setItem('site_posts', JSON.stringify(updated));
      setSelectedPosts(new Set());
    }
  };

  const handleBulkCategoryUpdate = () => {
    if (!bulkCategory) return;
    if (confirm(`Update category to ${bulkCategory} for ${selectedPosts.size} posts?`)) {
      const updated = posts.map(p => selectedPosts.has(p.id) ? { ...p, category: bulkCategory as Category } : p);
      setPosts(updated);
      localStorage.setItem('site_posts', JSON.stringify(updated));
      setSelectedPosts(new Set());
      setBulkCategory('');
    }
  };

  const handleDeleteAll = () => {
    if (confirm("DANGER: Delete ALL posts permanently?")) {
      setPosts([]);
      localStorage.setItem('site_posts', JSON.stringify([]));
    }
  };

  const toggleSelect = (id: string) => {
    const newSelected = new Set(selectedPosts);
    if (newSelected.has(id)) newSelected.delete(id);
    else newSelected.add(id);
    setSelectedPosts(newSelected);
  };

  const handleGenerateImage = async () => {
    if (!imagePrompt.trim()) return;
    setLoadingImage(true);
    const imageUrl = await generatePostImage(imagePrompt);
    if (imageUrl) {
      setCurrentPost({ ...currentPost, image: imageUrl });
      setShowImageDialog(false);
    }
    setLoadingImage(false);
  };

  const handleFileUpload = async (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (file) {
      setLoadingImage(true);
      try {
        const compressed = await compressImage(file);
        setCurrentPost({ ...currentPost, image: compressed });
      } catch (err) {
        alert("Image optimization failed.");
      } finally {
        setLoadingImage(false);
      }
    }
  };

  const toggleSort = () => {
    setSortOrder(sortOrder === 'desc' ? 'asc' : 'desc');
  };

  const sortedPosts = [...posts].sort((a, b) => {
    const dateA = new Date(a.date).getTime();
    const dateB = new Date(b.date).getTime();
    return sortOrder === 'desc' ? dateB - dateA : dateA - dateB;
  });

  const getPendingCommentCount = (postId: string) => {
    return comments.filter(c => c.postId === postId && c.status === 'pending').length;
  };

  return (
    <div className="space-y-8">
      <div className="flex justify-between items-center">
        <h1 className="text-3xl font-condensed font-black italic uppercase text-white">Editorial Control</h1>
        <div className="flex space-x-4">
          <button onClick={handleDeleteAll} className="px-4 py-2 border border-red-500/50 text-red-500 text-[10px] font-black uppercase rounded-xl hover:bg-red-500 hover:text-white transition-all">Delete All</button>
          <button
            onClick={() => { setCurrentPost({ seo: { metaTitle: '', metaDescription: '', keywords: '' }, tags: [] }); setIsEditing(true); }}
            className="bg-[#ff3e3e] text-white px-6 py-2 rounded-xl font-black uppercase italic flex items-center shadow-lg hover:scale-105 transition-transform"
          >
            Compose Post
          </button>
        </div>
      </div>

      {isEditing ? (
        <div className="bg-[#0a0e17] p-8 rounded-2xl border border-white/10 space-y-8">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div className="space-y-6">
              <label className="block">
                <span className="text-[10px] font-black uppercase tracking-[0.2em] text-gray-500">Headline</span>
                <input type="text" className="mt-1 block w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white" value={currentPost.title || ''} onChange={e => setCurrentPost({ ...currentPost, title: e.target.value })} />
              </label>
              
              <div className="grid grid-cols-2 gap-4">
                <label className="block">
                  <span className="text-[10px] font-black uppercase tracking-[0.2em] text-gray-500">Category</span>
                  <select className="mt-1 block w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white" value={currentPost.category || ''} onChange={e => setCurrentPost({ ...currentPost, category: e.target.value as Category })}>
                    <option value="" className="bg-[#0a0e17]">Select</option>
                    {Object.values(Category).map(cat => <option key={cat} value={cat} className="bg-[#0a0e17]">{cat}</option>)}
                  </select>
                </label>
                <div className="flex items-end gap-2 pb-1">
                  <button onClick={() => setShowImageDialog(true)} className="flex-1 bg-white/5 border border-white/10 rounded-xl py-3 text-[10px] font-black uppercase text-white hover:bg-[#ff3e3e]">AI Image Gen</button>
                  <label className="flex-1 bg-white/5 border border-white/10 rounded-xl py-3 text-[10px] font-black uppercase text-white text-center cursor-pointer hover:bg-white/10">
                    Upload
                    <input type="file" className="hidden" accept="image/*" onChange={handleFileUpload} />
                  </label>
                </div>
              </div>

              {currentPost.image && (
                <div className="mt-2">
                  <span className="text-[10px] font-black uppercase tracking-[0.2em] text-gray-500 block mb-2">Asset Preview</span>
                  <img src={currentPost.image} className="w-full aspect-video rounded-xl object-cover border border-white/10" alt="Preview" />
                </div>
              )}

              <label className="block">
                <span className="text-[10px] font-black uppercase tracking-[0.2em] text-gray-500">Tags (comma separated)</span>
                <input 
                  type="text" 
                  className="mt-1 block w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white" 
                  placeholder="e.g. transfer, epl, arsenal"
                  value={currentPost.tags?.join(', ') || ''} 
                  onChange={e => setCurrentPost({ ...currentPost, tags: e.target.value.split(',').map(t => t.trim()).filter(t => t !== '') })} 
                />
              </label>

              <div className="bg-white/5 p-6 rounded-2xl border border-white/5 space-y-4">
                <h3 className="text-[10px] font-black uppercase tracking-[0.2em] text-[#ff3e3e]">SEO Metadata</h3>
                <input type="text" placeholder="Meta Title" className="w-full bg-black/20 border border-white/5 rounded-lg px-4 py-2 text-xs" value={currentPost.seo?.metaTitle || ''} onChange={e => setCurrentPost({...currentPost, seo: {...currentPost.seo!, metaTitle: e.target.value}})} />
                <textarea placeholder="Meta Description" className="w-full bg-black/20 border border-white/5 rounded-lg px-4 py-2 text-xs" rows={2} value={currentPost.seo?.metaDescription || ''} onChange={e => setCurrentPost({...currentPost, seo: {...currentPost.seo!, metaDescription: e.target.value}})} />
                <input type="text" placeholder="Keywords (comma separated)" className="w-full bg-black/20 border border-white/5 rounded-lg px-4 py-2 text-xs" value={currentPost.seo?.keywords || ''} onChange={e => setCurrentPost({...currentPost, seo: {...currentPost.seo!, keywords: e.target.value}})} />
              </div>
            </div>
            
            <div className="space-y-6">
              <label className="block">
                <span className="text-[10px] font-black uppercase tracking-[0.2em] text-gray-500">Excerpt</span>
                <textarea className="mt-1 block w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white" rows={3} value={currentPost.excerpt || ''} onChange={e => setCurrentPost({ ...currentPost, excerpt: e.target.value })} />
              </label>
              <label className="block">
                <span className="text-[10px] font-black uppercase tracking-[0.2em] text-gray-500">Content</span>
                <textarea className="mt-1 block w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white min-h-[450px]" value={currentPost.content || ''} onChange={e => setCurrentPost({ ...currentPost, content: e.target.value })} />
              </label>
            </div>
          </div>
          
          <div className="flex justify-end space-x-6">
            <button onClick={() => setIsEditing(false)} className="text-gray-500 font-black uppercase text-xs">Cancel</button>
            <button onClick={handleSave} className="bg-[#ff3e3e] text-white px-10 py-3 rounded-xl font-black uppercase italic tracking-widest shadow-xl">Save & Notify</button>
          </div>
        </div>
      ) : (
        <>
          {selectedPosts.size > 0 && (
            <div className="bg-[#ff3e3e] p-4 rounded-xl flex flex-col md:flex-row justify-between items-md-center text-white mb-4 animate-in slide-in-from-top-4 gap-4">
              <span className="font-black uppercase text-xs tracking-widest">{selectedPosts.size} Posts Selected</span>
              <div className="flex items-center gap-2">
                <div className="flex items-center bg-black/20 rounded-lg px-2">
                  <span className="text-[10px] font-black uppercase mr-2 opacity-60">Bulk Category:</span>
                  <select 
                    className="bg-transparent text-[10px] font-black uppercase border-none focus:ring-0 py-1"
                    value={bulkCategory}
                    onChange={(e) => setBulkCategory(e.target.value as Category)}
                  >
                    <option value="" className="bg-[#ff3e3e]">Select...</option>
                    {Object.values(Category).map(cat => <option key={cat} value={cat} className="bg-[#ff3e3e]">{cat}</option>)}
                  </select>
                  <button onClick={handleBulkCategoryUpdate} disabled={!bulkCategory} className="ml-2 bg-white text-[#ff3e3e] px-3 py-0.5 rounded text-[10px] font-black uppercase disabled:opacity-50">Apply</button>
                </div>
                <button onClick={handleBulkDelete} className="bg-white text-[#ff3e3e] px-4 py-1 rounded-lg text-[10px] font-black uppercase">Delete Selected</button>
              </div>
            </div>
          )}
          <div className="bg-[#0a0e17] rounded-2xl border border-white/10 overflow-hidden shadow-2xl overflow-x-auto">
            <table className="w-full text-left min-w-[700px]">
              <thead className="bg-white/5 text-[10px] font-black uppercase tracking-[0.2em] text-gray-500">
                <tr>
                  <th className="px-8 py-4 w-10"></th>
                  <th className="px-8 py-4">Article</th>
                  <th className="px-8 py-4">Category</th>
                  <th className="px-8 py-4 cursor-pointer hover:text-white transition-colors group bg-white/5" onClick={toggleSort}>
                    <div className="flex items-center justify-between">
                      <span>Date</span>
                      <span className="text-[#ff3e3e] ml-2">{sortOrder === 'desc' ? '▼' : '▲'}</span>
                    </div>
                  </th>
                  <th className="px-8 py-4">Actions</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-white/5">
                {sortedPosts.map(post => {
                  const pendingCount = getPendingCommentCount(post.id);
                  return (
                    <tr key={post.id} className={`hover:bg-white/5 transition-colors ${selectedPosts.has(post.id) ? 'bg-[#ff3e3e]/5' : ''}`}>
                      <td className="px-8 py-6">
                        <input type="checkbox" checked={selectedPosts.has(post.id)} onChange={() => toggleSelect(post.id)} className="w-4 h-4 rounded border-gray-600 bg-transparent text-[#ff3e3e]" />
                      </td>
                      <td className="px-8 py-6">
                        <div className="flex items-center">
                          <img src={post.image} className="w-10 h-10 rounded mr-4 object-cover" alt="" />
                          <div>
                            <span className="font-bold text-white uppercase italic text-sm">{post.title}</span>
                            <div className="flex flex-wrap gap-1 mt-1">
                              {post.tags?.map(tag => (
                                <span key={tag} className="text-[8px] bg-white/5 text-gray-500 px-1.5 py-0.5 rounded">#{tag}</span>
                              ))}
                              {pendingCount > 0 && (
                                <span className="bg-yellow-500/20 text-yellow-500 text-[8px] font-black px-1.5 py-0.5 rounded border border-yellow-500/20 uppercase">
                                  {pendingCount} Pending Comment{pendingCount > 1 ? 's' : ''}
                                </span>
                              )}
                            </div>
                          </div>
                        </div>
                      </td>
                      <td className="px-8 py-6">
                        <span className="bg-white/5 text-[9px] px-2 py-1 rounded font-black uppercase text-gray-400">{post.category}</span>
                      </td>
                      <td className="px-8 py-6 text-[10px] font-bold text-gray-500 uppercase">
                        {post.date}
                      </td>
                      <td className="px-8 py-6">
                        <button onClick={() => { setCurrentPost(post); setIsEditing(true); }} className="text-gray-500 hover:text-white mr-4"><svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg></button>
                      </td>
                    </tr>
                  );
                })}
              </tbody>
            </table>
          </div>
        </>
      )}

      {showImageDialog && (
        <div className="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/90">
          <div className="bg-[#0a0e17] border border-white/10 w-full max-w-md p-8 rounded-2xl">
            <h3 className="text-xl font-condensed font-black text-white italic mb-4">ASSET GENERATOR</h3>
            <textarea className="w-full bg-white/5 border border-white/10 rounded-xl p-4 text-white text-xs mb-4" rows={4} placeholder="e.g. Cinematic action shot of a soccer player..." value={imagePrompt} onChange={e => setImagePrompt(e.target.value)} />
            <div className="flex space-x-4">
              <button onClick={() => setShowImageDialog(false)} className="flex-1 py-3 text-[10px] font-black uppercase text-gray-500">Cancel</button>
              <button onClick={handleGenerateImage} className="flex-[2] bg-[#ff3e3e] text-white py-3 rounded-xl font-black uppercase italic tracking-widest">{loadingImage ? 'Working...' : 'Generate'}</button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default AdminPosts;
