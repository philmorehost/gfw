
import React, { useState, useEffect } from 'react';
import { SiteSettings } from '../types';
import { INITIAL_SETTINGS } from '../constants';
import { compressImage } from '../services/imageService';

const AdminSettings: React.FC = () => {
  const [settings, setSettings] = useState<SiteSettings>(INITIAL_SETTINGS);
  const [activeTab, setActiveTab] = useState<'general' | 'smtp' | 'social'>('general');

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
      <h1 className="text-3xl font-oswald font-bold uppercase">System Settings</h1>

      <div className="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div className="flex border-b">
          {['general', 'smtp', 'social'].map(tab => (
            <button
              key={tab}
              onClick={() => setActiveTab(tab as any)}
              className={`flex-1 py-4 text-xs font-bold uppercase tracking-widest ${activeTab === tab ? 'bg-red-700 text-white' : 'hover:bg-gray-50'}`}
            >
              {tab} Settings
            </button>
          ))}
        </div>

        <form onSubmit={handleSave} className="p-8 space-y-6">
          {activeTab === 'general' && (
            <div className="grid grid-cols-1 gap-6">
              <label className="block">
                <span className="text-sm font-bold uppercase text-gray-500">Website Name</span>
                <input type="text" className="mt-1 block w-full rounded border-gray-300 py-2 px-3 border shadow-sm" value={settings.name} onChange={e => setSettings({...settings, name: e.target.value})} />
              </label>
              <label className="block">
                <span className="text-sm font-bold uppercase text-gray-500">Tagline</span>
                <input type="text" className="mt-1 block w-full rounded border-gray-300 py-2 px-3 border shadow-sm" value={settings.tagline} onChange={e => setSettings({...settings, tagline: e.target.value})} />
              </label>
              <label className="block">
                <span className="text-sm font-bold uppercase text-gray-500">Site Logo</span>
                <input type="file" onChange={handleLogoUpload} className="mt-1 block w-full text-sm text-gray-500" />
                <img src={settings.logo} className="mt-4 h-12 rounded border p-2" alt="Logo" />
              </label>
              <label className="block">
                <span className="text-sm font-bold uppercase text-gray-500">WhatsApp Contact</span>
                <input type="text" className="mt-1 block w-full rounded border-gray-300 py-2 px-3 border shadow-sm" value={settings.whatsappNumber} onChange={e => setSettings({...settings, whatsappNumber: e.target.value})} />
              </label>
            </div>
          )}

          {activeTab === 'smtp' && (
            <div className="grid grid-cols-1 gap-6">
              <label className="block">
                <span className="text-sm font-bold uppercase text-gray-500">Admin Email</span>
                <input type="email" className="mt-1 block w-full rounded border-gray-300 py-2 px-3 border shadow-sm" value={settings.adminEmail} onChange={e => setSettings({...settings, adminEmail: e.target.value})} />
              </label>
              <label className="block">
                <span className="text-sm font-bold uppercase text-gray-500">SMTP Sender Address</span>
                <input type="email" className="mt-1 block w-full rounded border-gray-300 py-2 px-3 border shadow-sm" value={settings.smtpSender} onChange={e => setSettings({...settings, smtpSender: e.target.value})} />
              </label>
              <div className="p-4 bg-gray-50 rounded border border-dashed text-gray-500 text-sm">
                * SMTP details are simulated. Any emails sent will use the site name as the sender display name.
              </div>
            </div>
          )}

          {activeTab === 'social' && (
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              {Object.keys(settings.socials).map(key => (
                <label key={key} className="block">
                  <span className="text-sm font-bold uppercase text-gray-500 capitalize">{key} Handle</span>
                  <input 
                    type="text" 
                    className="mt-1 block w-full rounded border-gray-300 py-2 px-3 border shadow-sm" 
                    value={(settings.socials as any)[key]} 
                    onChange={e => setSettings({...settings, socials: {...settings.socials, [key]: e.target.value}})} 
                  />
                </label>
              ))}
              <div className="col-span-full">
                <div className="flex items-center space-x-2 p-3 bg-blue-50 text-blue-700 rounded text-xs font-bold">
                  <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" /></svg>
                  <span>Auto-post to Social Media is ENABLED for these handles.</span>
                </div>
              </div>
            </div>
          )}

          <div className="pt-6 border-t flex justify-end">
            <button type="submit" className="bg-red-700 text-white px-10 py-3 rounded font-bold uppercase hover:bg-black transition-colors">
              Save All Changes
            </button>
          </div>
        </form>
      </div>
    </div>
  );
};

export default AdminSettings;
