
import React, { useState, useEffect } from 'react';
import { SiteSettings, AIModel } from '../types';
import { INITIAL_SETTINGS } from '../constants';
import { compressImage } from '../services/imageService';

const MODELS: AIModel[] = ['gemini-3-flash-preview', 'gemini-3-pro-preview', 'gemini-2.5-flash-lite-latest'];

const AdminSettings: React.FC = () => {
  const [settings, setSettings] = useState<SiteSettings>(INITIAL_SETTINGS);
  const [activeTab, setActiveTab] = useState<'general' | 'ai' | 'smtp' | 'social'>('general');

  useEffect(() => {
    const stored = localStorage.getItem('site_settings');
    if (stored) setSettings(JSON.parse(stored));
  }, []);

  const handleSave = (e: React.FormEvent) => {
    e.preventDefault();
    localStorage.setItem('site_settings', JSON.stringify(settings));
    alert("Settings updated successfully!");
  };

  const handleLogoUpload = async (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (file) {
      const compressed = await compressImage(file);
      setSettings({ ...settings, logo: compressed });
    }
  };

  return (
    <div className="max-w-4xl space-y-8">
      <div className="flex justify-between items-end">
        <div>
          <h1 className="text-4xl font-condensed font-black italic uppercase text-white mb-2">System Control</h1>
          <p className="text-xs font-black uppercase text-gray-500 tracking-widest">Global Watch Infrastructure</p>
        </div>
      </div>

      <div className="bg-[#0a0e17] rounded-3xl border border-white/5 overflow-hidden shadow-2xl">
        <div className="flex border-b border-white/5">
          {['general', 'ai', 'smtp', 'social'].map(tab => (
            <button
              key={tab}
              onClick={() => setActiveTab(tab as any)}
              className={`flex-1 py-5 text-[10px] font-black uppercase tracking-[0.2em] transition-all ${activeTab === tab ? 'bg-[#ff3e3e] text-white shadow-inner' : 'text-gray-500 hover:bg-white/5'}`}
            >
              {tab}
            </button>
          ))}
        </div>

        <form onSubmit={handleSave} className="p-8 md:p-12 space-y-8">
          {activeTab === 'general' && (
            <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
              <label className="block col-span-full">
                <span className="text-[10px] font-black uppercase text-gray-500 tracking-widest mb-2 block">Website Name</span>
                <input type="text" className="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-4 text-white font-bold" value={settings.name} onChange={e => setSettings({...settings, name: e.target.value})} />
              </label>
              <label className="block">
                <span className="text-[10px] font-black uppercase text-gray-500 tracking-widest mb-2 block">Tagline</span>
                <input type="text" className="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-4 text-white font-bold" value={settings.tagline} onChange={e => setSettings({...settings, tagline: e.target.value})} />
              </label>
              <label className="block">
                <span className="text-[10px] font-black uppercase text-gray-500 tracking-widest mb-2 block">WhatsApp Contact</span>
                <input type="text" className="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-4 text-white font-bold" value={settings.whatsappNumber} onChange={e => setSettings({...settings, whatsappNumber: e.target.value})} />
              </label>
              <div className="col-span-full">
                <span className="text-[10px] font-black uppercase text-gray-500 tracking-widest mb-4 block">Site Identity</span>
                <div className="flex items-center gap-6 bg-black/40 p-6 rounded-3xl border border-white/5">
                  <img src={settings.logo} className="h-12 rounded bg-black/20 p-2" alt="Logo" />
                  <input type="file" onChange={handleLogoUpload} className="text-xs text-gray-500 file:bg-white/5 file:border-white/10 file:text-white file:rounded-xl file:px-4 file:py-2" />
                </div>
              </div>
            </div>
          )}

          {activeTab === 'ai' && (
            <div className="space-y-6">
               <h3 className="text-xl font-condensed font-black italic text-white uppercase">Intelligence Engine</h3>
               <p className="text-sm text-gray-500 max-w-lg">Select which Gemini model will generate real-time livescores, odds, and tactical summaries. Pro models offer deeper insights but higher latency.</p>
               <div className="grid grid-cols-1 gap-4">
                  {MODELS.map(model => (
                    <button 
                      type="button"
                      key={model}
                      onClick={() => setSettings({...settings, selectedModel: model})}
                      className={`flex items-center justify-between p-5 rounded-2xl border transition-all ${settings.selectedModel === model ? 'border-[#ff3e3e] bg-[#ff3e3e]/5' : 'border-white/5 bg-white/5'}`}
                    >
                      <div className="text-start">
                        <span className="text-xs font-black uppercase text-white tracking-widest block">{model}</span>
                        <span className="text-[10px] text-gray-500 uppercase">{model.includes('pro') ? 'Advanced Reasoning' : 'High Speed & Efficiency'}</span>
                      </div>
                      <div className={`w-4 h-4 rounded-full border-2 ${settings.selectedModel === model ? 'border-[#ff3e3e] bg-[#ff3e3e]' : 'border-gray-700'}`}></div>
                    </button>
                  ))}
               </div>
            </div>
          )}

          {activeTab === 'smtp' && (
            <div className="grid grid-cols-1 gap-8">
              <label className="block">
                <span className="text-[10px] font-black uppercase text-gray-500 tracking-widest mb-2 block">Admin Notification Email</span>
                <input type="email" className="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-4 text-white font-bold" value={settings.adminEmail} onChange={e => setSettings({...settings, adminEmail: e.target.value})} />
              </label>
              <label className="block">
                <span className="text-[10px] font-black uppercase text-gray-500 tracking-widest mb-2 block">SMTP Sender Identity</span>
                <input type="email" className="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-4 text-white font-bold" value={settings.smtpSender} onChange={e => setSettings({...settings, smtpSender: e.target.value})} />
              </label>
            </div>
          )}

          {activeTab === 'social' && (
            <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
              {Object.keys(settings.socials).map(key => (
                <label key={key} className="block">
                  <span className="text-[10px] font-black uppercase text-gray-500 tracking-widest mb-2 block capitalize">{key}</span>
                  <input 
                    type="text" 
                    className="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-4 text-white font-bold" 
                    value={(settings.socials as any)[key]} 
                    onChange={e => setSettings({...settings, socials: {...settings.socials, [key]: e.target.value}})} 
                  />
                </label>
              ))}
            </div>
          )}

          <div className="pt-8 border-t border-white/5 flex justify-end">
            <button type="submit" className="bg-[#ff3e3e] text-white px-12 py-4 rounded-2xl font-black uppercase italic tracking-widest shadow-xl hover:scale-105 transition-transform">
              Synchronize Settings
            </button>
          </div>
        </form>
      </div>
    </div>
  );
};

export default AdminSettings;
